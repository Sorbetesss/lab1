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
 * EngineAwareTrait trait.
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 */
interface EngineAwareInterface
{
    /**
     * Sets the Engine
     *
     * @param EngineInterface $engine A EngineInterface instance
     */
    public function setEngine(EngineInterface $engine = null);
}
