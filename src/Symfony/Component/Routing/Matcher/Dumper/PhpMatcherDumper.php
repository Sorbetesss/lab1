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
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * PhpMatcherDumper creates a PHP class able to match URLs for a given set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class PhpMatcherDumper extends MatcherDumper
{
    private $expressionLanguage;

    /**
     * @var ExpressionFunctionProviderInterface[]
     */
    private $expressionLanguageProviders = array();

    /**
     * Dumps a set of routes to a PHP class.
     *
     * Available options:
     *
     *  * class:      The class name
     *  * base_class: The base class name
     *
     * @param array $options An array of options
     *
     * @return string A PHP class representing the matcher class
     */
    public function dump(array $options = array())
    {
        $options = array_replace(array(
            'class' => 'ProjectUrlMatcher',
            'base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
        ), $options);

        // trailing slash support is only enabled if we know how to redirect the user
        $interfaces = class_implements($options['base_class']);
        $supportsRedirections = isset($interfaces['Symfony\\Component\\Routing\\Matcher\\RedirectableUrlMatcherInterface']);

        return <<<EOF
<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    public function __construct(RequestContext \$context)
    {
        \$this->context = \$context;
    }

{$this->generateMatchMethod($supportsRedirections)}
}

EOF;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguageProviders[] = $provider;
    }

    /**
     * Generates the code for the match method implementing UrlMatcherInterface.
     *
     * @param bool $supportsRedirections Whether redirections are supported by the base class
     *
     * @return string Match method as PHP code
     */
    private function generateMatchMethod($supportsRedirections)
    {
        $code = rtrim($this->compileRoutes($this->getRoutes(), $supportsRedirections), "\n");

        return <<<EOF
    public function match(\$rawPathinfo)
    {
        \$allow = array();
        \$pathinfo = rawurldecode(\$rawPathinfo);
        \$trimmedPathinfo = rtrim(\$pathinfo, '/');
        \$context = \$this->context;
        \$request = \$this->request ?: \$this->createRequest(\$pathinfo);
        \$requestMethod = \$canonicalMethod = \$context->getMethod();

        if ('HEAD' === \$requestMethod) {
            \$canonicalMethod = 'GET';
        }

$code

        throw 0 < count(\$allow) ? new MethodNotAllowedException(array_unique(\$allow)) : new ResourceNotFoundException();
    }
EOF;
    }

    /**
     * Generates PHP code to match a RouteCollection with all its routes.
     *
     * @param RouteCollection $routes               A RouteCollection instance
     * @param bool            $supportsRedirections Whether redirections are supported by the base class
     *
     * @return string PHP code
     */
    private function compileRoutes(RouteCollection $routes, $supportsRedirections)
    {
        $fetchedHost = false;
        $groups = $this->groupRoutesByHostRegex($routes);
        $code = '';

        foreach ($groups as $collection) {
            if (null !== $regex = $collection->getAttribute('host_regex')) {
                if (!$fetchedHost) {
                    $code .= "        \$host = \$context->getHost();\n\n";
                    $fetchedHost = true;
                }

                $code .= sprintf("        if (preg_match(%s, \$host, \$hostMatches)) {\n", var_export($regex, true));
            }

            $groupCode = $this->compileStaticRoutes($collection, $supportsRedirections);
            $tree = $this->buildStaticPrefixCollection($collection);
            $groupCode .= $this->compileStaticPrefixRoutes($tree, $supportsRedirections);

            if (null !== $regex) {
                // apply extra indention at each line (except empty ones)
                $groupCode = preg_replace('/^.{2,}$/m', '    $0', $groupCode);
                $code .= $groupCode;
                $code .= "        }\n\n";
            } else {
                $code .= $groupCode;
            }
        }

        if ('' === $code) {
            $code .= "        if ('/' === \$pathinfo) {\n";
            $code .= "            throw new Symfony\Component\Routing\Exception\NoConfigurationException();\n";
            $code .= "        }\n";
        }

        return $code;
    }

    private function buildStaticPrefixCollection(DumperCollection $collection)
    {
        $prefixCollection = new StaticPrefixCollection();

        foreach ($collection as $dumperRoute) {
            $prefix = $dumperRoute->getRoute()->compile()->getStaticPrefix();
            $prefixCollection->addRoute($prefix, $dumperRoute);
        }

        $prefixCollection->optimizeGroups();

        return $prefixCollection;
    }

    /**
     * Generates PHP code to match the static routes in a collection.
     */
    private function compileStaticRoutes(DumperCollection $collection, bool $supportsRedirections): string
    {
        $code = '';
        $dynamicRegex = array();
        $dynamicRoutes = array();
        $staticRoutes = array();

        foreach ($collection->all() as $route) {
            $compiledRoute = $route->getRoute()->compile();
            $regex = $compiledRoute->getRegex();
            $methods = $route->getRoute()->getMethods();
            if ($hasTrailingSlash = $supportsRedirections && $pos = strpos($regex, '/$')) {
                $regex = substr($regex, 0, $pos).'/?$'.substr($regex, $pos + 2);
            }
            if (!$compiledRoute->getPathVariables()) {
                $url = $route->getRoute()->getPath();
                if ($hasTrailingSlash) {
                    $url = rtrim($url, '/');
                }
                foreach ($dynamicRegex as $rx) {
                    if (preg_match($rx, $url)) {
                        $dynamicRegex[] = $regex;
                        $dynamicRoutes[] = $route;
                        continue 2;
                    }
                }

                $staticRoutes[$url][] = array($hasTrailingSlash, $route);
            } else {
                $dynamicRegex[] = $regex;
                $dynamicRoutes[] = $route;
            }
        }

        $collection->setAll($dynamicRoutes);

        if ($staticRoutes) {
            foreach ($staticRoutes as $url => $routes) {
                $code .= sprintf("        case %s:\n", var_export($url, true));
                foreach ($routes as list($hasTrailingSlash, $route)) {
                    $methods = $route->getRoute()->getMethods();
                    $supportsTrailingSlash = $supportsRedirections && (!$methods || in_array('HEAD', $methods) || in_array('GET', $methods));
                    $routeCode = $this->compileRoute($route->getRoute(), $route->getName(), $supportsRedirections, null, $hasTrailingSlash);
                    if ($route->getRoute()->getCondition() || ($hasTrailingSlash && !$supportsTrailingSlash)) {
                        $routeCode = preg_replace('/^.{2,}$/m', '    $0', $routeCode);
                    }
                    $code .= $routeCode;
                }
                $code .= "            break;\n";
            }
            $code = preg_replace('/^.{2,}$/m', '    $0', $code);
            $code = sprintf("        switch (%s) {\n{$code}        }\n\n", $supportsRedirections ? '$trimmedPathinfo' : '$pathinfo');
        }

        return $code;
    }

    /**
     * Generates PHP code to match a tree of routes.
     *
     * @param StaticPrefixCollection $collection           A StaticPrefixCollection instance
     * @param bool                   $supportsRedirections Whether redirections are supported by the base class
     * @param string                 $ifOrElseIf           either "if" or "elseif" to influence chaining
     *
     * @return string PHP code
     */
    private function compileStaticPrefixRoutes(StaticPrefixCollection $collection, $supportsRedirections, $ifOrElseIf = 'if')
    {
        $code = '';
        $prefix = $collection->getPrefix();

        if (!empty($prefix) && '/' !== $prefix) {
            $code .= sprintf("    %s (0 === strpos(\$pathinfo, %s)) {\n", $ifOrElseIf, var_export($prefix, true));
        }

        $ifOrElseIf = 'if';

        foreach ($collection->getItems() as $route) {
            if ($route instanceof StaticPrefixCollection) {
                $code .= $this->compileStaticPrefixRoutes($route, $supportsRedirections, $ifOrElseIf);
                $ifOrElseIf = 'elseif';
            } else {
                $code .= $this->compileRoute($route[1]->getRoute(), $route[1]->getName(), $supportsRedirections, $prefix)."\n";
                $ifOrElseIf = 'if';
            }
        }

        if (!empty($prefix) && '/' !== $prefix) {
            $code .= "    }\n\n";
            // apply extra indention at each line (except empty ones)
            $code = preg_replace('/^.{2,}$/m', '    $0', $code);
        }

        return $code;
    }

    /**
     * Compiles a single Route to PHP code used to match it against the path info.
     *
     * @param Route       $route                A Route instance
     * @param string      $name                 The name of the Route
     * @param bool        $supportsRedirections Whether redirections are supported by the base class
     * @param string|null $parentPrefix         The prefix of the parent collection used to optimize the code
     * @param bool|null   $hasTrailingSlash     Whether the path has a trailing slash when compiling a static route
     *
     * @return string PHP code
     *
     * @throws \LogicException
     */
    private function compileRoute(Route $route, $name, $supportsRedirections, $parentPrefix = null, bool $hasTrailingSlash = null)
    {
        $code = '';
        $compiledRoute = $route->compile();
        $conditions = array();
        $matches = false;
        $hostMatches = false;
        $methods = $route->getMethods();

        $supportsTrailingSlash = $supportsRedirections && (!$methods || in_array('HEAD', $methods) || in_array('GET', $methods));
        $regex = $compiledRoute->getRegex();

        if (null !== $hasTrailingSlash && (!$hasTrailingSlash || $supportsTrailingSlash)) {
            // no-op
        } elseif (!$compiledRoute->getPathVariables() && preg_match('#^(.)\^(?P<url>.*?)\$\1#'.('u' === $regex[-1] ? 'u' : ''), $regex, $m)) {
            if ($hasTrailingSlash = $supportsTrailingSlash && '/' === $m['url'][-1]) {
                $conditions[] = sprintf('%s === $trimmedPathinfo', var_export(rtrim(str_replace('\\', '', $m['url']), '/'), true));
            } else {
                $conditions[] = sprintf('%s === $pathinfo', var_export(str_replace('\\', '', $m['url']), true));
            }
        } else {
            if ($compiledRoute->getStaticPrefix() && $compiledRoute->getStaticPrefix() !== $parentPrefix) {
                $conditions[] = sprintf('0 === strpos($pathinfo, %s)', var_export($compiledRoute->getStaticPrefix(), true));
            }

            if ($hasTrailingSlash = $supportsTrailingSlash && $pos = strpos($regex, '/$')) {
                $regex = substr($regex, 0, $pos).'/?$'.substr($regex, $pos + 2);
            }
            $conditions[] = sprintf('preg_match(%s, $pathinfo, $matches)', var_export($regex, true));

            $matches = true;
        }

        if ($compiledRoute->getHostVariables()) {
            $hostMatches = true;
        }

        if ($route->getCondition()) {
            $conditions[] = $this->getExpressionLanguage()->compile($route->getCondition(), array('context', 'request'));
        }

        $conditions = implode(' && ', $conditions);

        if ($conditions) {
            $code .= <<<EOF
        // $name
        if ($conditions) {

EOF;
        } else {
            $code .= "            // {$name}\n";
        }

        $gotoname = 'not_'.preg_replace('/[^A-Za-z0-9_]/', '', $name);

        if ($methods) {
            if (1 === count($methods)) {
                if ('HEAD' === $methods[0]) {
                    $code .= <<<EOF
            if ('HEAD' !== \$requestMethod) {
                \$allow[] = 'HEAD';
                goto $gotoname;
            }


EOF;
                } else {
                    $code .= <<<EOF
            if ('$methods[0]' !== \$canonicalMethod) {
                \$allow[] = '$methods[0]';
                goto $gotoname;
            }


EOF;
                }
            } else {
                $methodVariable = 'requestMethod';

                if (in_array('GET', $methods)) {
                    // Since we treat HEAD requests like GET requests we don't need to match it.
                    $methodVariable = 'canonicalMethod';
                    $methods = array_values(array_filter($methods, function ($method) { return 'HEAD' !== $method; }));
                }

                if (1 === count($methods)) {
                    $code .= <<<EOF
            if ('$methods[0]' !== \$$methodVariable) {
                \$allow[] = '$methods[0]';
                goto $gotoname;
            }


EOF;
                } else {
                    $methods = implode("', '", $methods);
                    $code .= <<<EOF
            if (!in_array(\$$methodVariable, array('$methods'))) {
                \$allow = array_merge(\$allow, array('$methods'));
                goto $gotoname;
            }


EOF;
                }
            }
        }

        // the offset where the return value is appended below, with indendation
        $retOffset = 12 + strlen($code);

        // optimize parameters array
        if ($matches || $hostMatches) {
            $vars = array();
            if ($hostMatches) {
                $vars[] = '$hostMatches';
            }
            if ($matches) {
                $vars[] = '$matches';
            }
            $vars[] = "array('_route' => '$name')";

            $code .= sprintf(
                "            \$ret = \$this->mergeDefaults(array_replace(%s), %s);\n",
                implode(', ', $vars),
                self::export($route->getDefaults())
            );
        } elseif ($route->getDefaults()) {
            $code .= sprintf("            \$ret = %s;\n", self::export(array_replace($route->getDefaults(), array('_route' => $name))));
        } else {
            $code .= sprintf("            \$ret = array('_route' => '%s');\n", $name);
        }

        if ($hasTrailingSlash) {
            $code .= <<<EOF
            if ('/' === \$pathinfo[-1]) {
                // no-op
            } elseif ('GET' !== \$canonicalMethod) {
                goto $gotoname;
            } else {
                return array_replace(\$ret, \$this->redirect(\$rawPathinfo.'/', '$name'));
            }


EOF;
        }

        if ($schemes = $route->getSchemes()) {
            if (!$supportsRedirections) {
                throw new \LogicException('The "schemes" requirement is only supported for URL matchers that implement RedirectableUrlMatcherInterface.');
            }
            $schemes = self::export(array_flip($schemes));
            $code .= <<<EOF
            \$requiredSchemes = $schemes;
            if (!isset(\$requiredSchemes[\$context->getScheme()])) {
                return array_replace(\$ret, \$this->redirect(\$rawPathinfo, '$name', key(\$requiredSchemes)));
            }


EOF;
        }

        if ($hasTrailingSlash || $schemes) {
            $code .= "            return \$ret;\n";
        } else {
            $code = substr_replace($code, 'return', $retOffset, 6);
        }
        if ($conditions) {
            $code .= "        }\n";
        } elseif ($methods || $hasTrailingSlash) {
            $code .= '    ';
        }

        if ($methods || $hasTrailingSlash) {
            $code .= "        $gotoname:\n";
        }

        return $code;
    }

    /**
     * Groups consecutive routes having the same host regex.
     *
     * The result is a collection of collections of routes having the same host regex.
     *
     * @param RouteCollection $routes A flat RouteCollection
     *
     * @return DumperCollection A collection with routes grouped by host regex in sub-collections
     */
    private function groupRoutesByHostRegex(RouteCollection $routes)
    {
        $groups = new DumperCollection();
        $currentGroup = new DumperCollection();
        $currentGroup->setAttribute('host_regex', null);
        $groups->add($currentGroup);

        foreach ($routes as $name => $route) {
            $hostRegex = $route->compile()->getHostRegex();
            if ($currentGroup->getAttribute('host_regex') !== $hostRegex) {
                $currentGroup = new DumperCollection();
                $currentGroup->setAttribute('host_regex', $hostRegex);
                $groups->add($currentGroup);
            }
            $currentGroup->add(new DumperRoute($name, $route));
        }

        return $groups;
    }

    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
                throw new \RuntimeException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
            }
            $this->expressionLanguage = new ExpressionLanguage(null, $this->expressionLanguageProviders);
        }

        return $this->expressionLanguage;
    }

    /**
     * @internal
     */
    public static function export($value): string
    {
        if (null === $value) {
            return 'null';
        }
        if (!\is_array($value)) {
            return var_export($value, true);
        }
        if (!$value) {
            return 'array()';
        }

        $i = 0;
        $export = 'array(';

        foreach ($value as $k => $v) {
            if ($i === $k) {
                ++$i;
            } else {
                $export .= var_export($k, true).' => ';

                if (\is_int($k) && $i < $k) {
                    $i = 1 + $k;
                }
            }

            $export .= self::export($v).', ';
        }

        return substr_replace($export, ')', -2);
    }
}
