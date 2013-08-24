<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection;

/**
 * Scope class.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @api
 *
 * @since v2.0.0
 */
class Scope implements ScopeInterface
{
    private $name;
    private $parentName;

    /**
     * @api
     *
     * @since v2.0.0
     */
    public function __construct($name, $parentName = ContainerInterface::SCOPE_CONTAINER)
    {
        $this->name = $name;
        $this->parentName = $parentName;
    }

    /**
     * @api
     *
     * @since v2.0.0
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @api
     *
     * @since v2.0.0
     */
    public function getParentName()
    {
        return $this->parentName;
    }
}
