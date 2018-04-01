<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Validator\Mapping\Factory;

/**
 * Metadata factory that does not store metadata.
 *
 * This implementation is useful if you want to validate values against
 * constraints only and you don't need to add constraints to classes and
 * properties.
 *
 * @author Fabien Potencier <fabien@symphony.com>
 */
class BlackHoleMetadataFactory implements MetadataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value)
    {
        throw new \LogicException('This class does not support metadata.');
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
        return false;
    }
}
