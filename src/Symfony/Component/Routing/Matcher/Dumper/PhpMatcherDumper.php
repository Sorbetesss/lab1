<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher\Dumper;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * PhpMatcherDumper creates a PHP class able to match URLs for a given set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class PhpMatcherDumper extends MatcherDumper
{
    /**
     * Dumps a set of routes to a PHP class.
     *
     * Available options:
     *
     *  * class:      The class name
     *  * base_class: The base class name
     *
     * @param  array  $options An array of options
     *
     * @return string A PHP class representing the matcher class
     */
    public function dump(array $options = array())
    {
        $options = array_merge(array(
            'class'      => 'ProjectUrlMatcher',
            'base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
        ), $options);

        // trailing slash support is only enabled if we know how to redirect the user
        $interfaces = class_implements($options['base_class']);
        $supportsRedirections = isset($interfaces['Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface']);

        return <<<EOF
<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext \$context)
    {
        \$this->context = \$context;
    }

{$this->generateMatchMethod($supportsRedirections)}
}

EOF;
    }

    private function generateMatchMethod($supportsRedirections)
    {
        $code = rtrim($this->compileRoutes($this->getRoutes(), $supportsRedirections), "\n");

        return <<<EOF
    public function match(\$pathinfo)
    {
        \$allow = array();
        \$pathinfo = rawurldecode(\$pathinfo);

$code

        throw 0 < count(\$allow) ? new MethodNotAllowedException(array_unique(\$allow)) : new ResourceNotFoundException();
    }
EOF;
    }

    private function compileRoutes(RouteCollection $routes, $supportsRedirections, $parentPrefix = null)
    {
        $code = '';

        foreach ($routes as $name => $route) {
            if ($route instanceof RouteCollection) {
                $prefix = $route->getPrefix();
                $countWorth = count($route->all()) > 1;
                // whether it's optimizable
                if ('' !== $prefix && $prefix !== $parentPrefix && $countWorth && false === strpos($prefix, '{')) {
                    $code .= sprintf("        if (0 === strpos(\$pathinfo, %s)) {\n", var_export($prefix, true));

                    foreach (explode("\n", $this->compileRoutes($route, $supportsRedirections, $prefix)) as $line) {
                        if ('' !== $line) {
                            $code .= '    '; // apply extra indention
                        }
                        $code .= $line."\n";
                    }

                    $code = substr($code, 0, -2); // remove redundant last two line breaks
                    $code .= "        }\n\n";
                } else {
                    $code .= $this->compileRoutes($route, $supportsRedirections, $countWorth ? $prefix : null);
                }
            } else {
                $code .= $this->compileRoute($route, $name, $supportsRedirections, $parentPrefix)."\n";
            }
        }

        return $code;
    }

    private function compileRoute(Route $route, $name, $supportsRedirections, $parentPrefix = null)
    {
        $code = '';
        $compiledRoute = $route->compile();
        $conditions = array();
        $hasTrailingSlash = false;
        $matches = false;
        $methods = array();

        if ($req = $route->getRequirement('_method')) {
            $methods = explode('|', strtoupper($req));
            // GET and HEAD are equivalent
            if (in_array('GET', $methods) && !in_array('HEAD', $methods)) {
                $methods[] = 'HEAD';
            }
        }

        $supportsTrailingSlash = $supportsRedirections && (!$methods || in_array('HEAD', $methods));

        if (!count($compiledRoute->getVariables()) && false !== preg_match('#^(.)\^(?P<url>.*?)\$\1#', $compiledRoute->getRegex(), $m)) {
            if ($supportsTrailingSlash && substr($m['url'], -1) === '/') {
                $conditions[] = sprintf("rtrim(\$pathinfo, '/') === %s", var_export(rtrim(str_replace('\\', '', $m['url']), '/'), true));
                $hasTrailingSlash = true;
            } else {
                $conditions[] = sprintf("\$pathinfo === %s", var_export(str_replace('\\', '', $m['url']), true));
            }
        } else {
            if ($compiledRoute->getStaticPrefix() && $compiledRoute->getStaticPrefix() !== $parentPrefix) {
                $conditions[] = sprintf("0 === strpos(\$pathinfo, %s)", var_export($compiledRoute->getStaticPrefix(), true));
            }

            $regex = $compiledRoute->getRegex();
            if ($supportsTrailingSlash && $pos = strpos($regex, '/$')) {
                $regex = substr($regex, 0, $pos).'/?$'.substr($regex, $pos + 2);
                $hasTrailingSlash = true;
            }
            $conditions[] = sprintf("preg_match(%s, \$pathinfo, \$matches)", var_export($regex, true));

            $matches = true;
        }

        $conditions = implode(' && ', $conditions);

        $code .= <<<EOF
        // $name
        if ($conditions) {

EOF;

        if ($methods) {
            $gotoname = 'not_'.preg_replace('/[^A-Za-z0-9_]/', '', $name);

            if (1 === count($methods)) {
                $code .= <<<EOF
            if (\$this->context->getMethod() != '$methods[0]') {
                \$allow[] = '$methods[0]';
                goto $gotoname;
            }

EOF;
            } else {
                $methods = implode("', '", $methods);
                $code .= <<<EOF
            if (!in_array(\$this->context->getMethod(), array('$methods'))) {
                \$allow = array_merge(\$allow, array('$methods'));
                goto $gotoname;
            }

EOF;
            }
        }

        if ($hasTrailingSlash) {
            $code .= <<<EOF
            if (substr(\$pathinfo, -1) !== '/') {
                return \$this->redirect(\$pathinfo.'/', '$name');
            }

EOF;
        }

        if ($scheme = $route->getRequirement('_scheme')) {
            if (!$supportsRedirections) {
                throw new \LogicException('The "_scheme" requirement is only supported for URL matchers that implement RedirectableUrlMatcherInterface.');
            }

            $code .= <<<EOF
            if (\$this->context->getScheme() !== '$scheme') {
                return \$this->redirect(\$pathinfo, '$name', '$scheme');
            }

EOF;
        }

        // optimize parameters array
        if (true === $matches && $compiledRoute->getDefaults()) {
            $code .= sprintf("            return array_merge(\$this->mergeDefaults(\$matches, %s), array('_route' => '%s'));\n"
                , str_replace("\n", '', var_export($compiledRoute->getDefaults(), true)), $name);
        } elseif (true === $matches) {
            $code .= sprintf("            \$matches['_route'] = '%s';\n", $name);
            $code .= "            return \$matches;\n";
        } elseif ($compiledRoute->getDefaults()) {
            $code .= sprintf("            return %s;\n", str_replace("\n", '', var_export(array_merge($compiledRoute->getDefaults(), array('_route' => $name)), true)));
        } else {
            $code .= sprintf("            return array('_route' => '%s');\n", $name);
        }
        $code .= "        }\n";

        if ($methods) {
            $code .= "        $gotoname:\n";
        }

        return $code;
    }
}
