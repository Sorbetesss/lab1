<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\NameConverter;

/**
 * Defines the interface for property name converters.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface NameConverterInterface
{
    /**
     * Converts a property name to its normalized value.
     *
     * @param string|object $class
     * @param string        $propertyName
     *
     * @return string
     */
    public function normalize($class, $propertyName);

    /**
     * Converts a property name to its denormalized value.
     *
     * @param string|object $class
     * @param string        $propertyName
     *
     * @return string
     */
    public function denormalize($class, $propertyName);
}
