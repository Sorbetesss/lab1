<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Templating;

/**
 * Internal representation of a template.
 *
 * @author Victor Berchet <victor@suumit.com>
 *
 * @api
 *
 * @since v2.0.0
 */
class TemplateReference implements TemplateReferenceInterface
{
    protected $parameters;

    /**
     * @since v2.0.0
     */
    public function __construct($name = null, $engine = null)
    {
        $this->parameters = array(
            'name'   => $name,
            'engine' => $engine,
        );
    }

    /**
     * @since v2.0.0
     */
    public function __toString()
    {
        return $this->getLogicalName();
    }

    /**
     * Sets a template parameter.
     *
     * @param string $name  The parameter name
     * @param string $value The parameter value
     *
     * @return TemplateReferenceInterface The TemplateReferenceInterface instance
     *
     * @throws  \InvalidArgumentException if the parameter is not defined
     *
     * @api
     *
     * @since v2.0.0
     */
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->parameters)) {
            $this->parameters[$name] = $value;
        } else {
            throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
        }

        return $this;
    }

    /**
     * Gets a template parameter.
     *
     * @param string $name The parameter name
     *
     * @return string The parameter value
     *
     * @throws  \InvalidArgumentException if the parameter is not defined
     *
     * @api
     *
     * @since v2.0.0
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
    }

    /**
     * Gets the template parameters.
     *
     * @return array An array of parameters
     *
     * @api
     *
     * @since v2.0.0
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Returns the path to the template.
     *
     * By default, it just returns the template name.
     *
     * @return string A path to the template or a resource
     *
     * @api
     *
     * @since v2.0.0
     */
    public function getPath()
    {
        return $this->parameters['name'];
    }

    /**
     * Returns the "logical" template name.
     *
     * The template name acts as a unique identifier for the template.
     *
     * @return string The template name
     *
     * @api
     *
     * @since v2.0.0
     */
    public function getLogicalName()
    {
        return $this->parameters['name'];
    }
}
