<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Normalizer;

/**
 * Converts between objects and arrays by mapping properties.
 *
 * The normalization process looks for all the object's properties (public and private).
 * The result is a map from property names to property values. Property values
 * are normalized through the serializer.
 *
 * The denormalization first looks at the constructor of the given class to see
 * if any of the parameters have the same name as one of the properties. The
 * constructor is then called with all parameters or an exception is thrown if
 * any required parameters were not present as properties. Then the denormalizer
 * walks through the given map of property names to property values to see if a
 * property with the corresponding name exists. If found, the property gets the value.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class PropertyNormalizer extends AbstractObjectNormalizer
{
    private $cache = array();

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return parent::supportsNormalization($data, $format) && (isset($this->cache[$type = \get_class($data)]) ? $this->cache[$type] : $this->cache[$type] = $this->supports($type));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return parent::supportsDenormalization($data, $type, $format) && (isset($this->cache[$type]) ? $this->cache[$type] : $this->cache[$type] = $this->supports($type));
    }

    /**
     * Checks if the given class has any non-static property.
     *
     * @param string $class
     *
     * @return bool
     */
    private function supports($class)
    {
        $class = new \ReflectionClass($class);

        // We look for at least one non-static property
        foreach ($class->getProperties() as $property) {
            if (!$property->isStatic()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowedAttribute($classOrObject, $attribute, $format = null, array $context = array())
    {
        if (!parent::isAllowedAttribute($classOrObject, $attribute, $format, $context)) {
            return false;
        }

        try {
            $reflectionProperty = new \ReflectionProperty(is_string($classOrObject) ? $classOrObject : get_class($classOrObject), $attribute);
            if ($reflectionProperty->isStatic()) {
                return false;
            }
        } catch (\ReflectionException $reflectionException) {
            $className = is_string($classOrObject) ? $classOrObject : get_class($classOrObject);
            $class = new \ReflectionClass($className);

            while ($parent = $class->getParentClass()) {
                try {
                    $reflectionProperty = new \ReflectionProperty($parent->getName(), $attribute);
                    if ($reflectionProperty) {
                        return true;
                    }
                } catch (\Exception $e) {
                }

                $class = $parent;
            }

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function extractAttributes($object, $format = null, array $context = array())
    {
        $reflectionObject = new \ReflectionObject($object);
        $attributes = array();

        do {
            foreach ($reflectionObject->getProperties() as $property) {
                if (!$this->isAllowedAttribute($reflectionObject->getName(), $property->name)) {
                    continue;
                }

                $attributes[] = $property->name;
            }
        } while ($reflectionObject = $reflectionObject->getParentClass());

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeValue($object, $attribute, $format = null, array $context = array())
    {
        try {
            $reflectionProperty = new \ReflectionProperty(get_class($object), $attribute);
        } catch (\ReflectionException $reflectionException) {
            $class = get_parent_class($object);
            do {
                $reflectionProperty = new \ReflectionProperty(get_parent_class($object), $attribute);
                if (null === $reflectionProperty) {
                    return;
                }
            } while ($class = get_parent_class($class));
        }

        // Override visibility
        if (!$reflectionProperty->isPublic()) {
            $reflectionProperty->setAccessible(true);
        }

        return $reflectionProperty->getValue($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = array())
    {
        try {
            $reflectionProperty = new \ReflectionProperty(get_class($object), $attribute);
        } catch (\ReflectionException $reflectionException) {
            $class = get_parent_class($object);
            do {
                $reflectionProperty = new \ReflectionProperty(get_parent_class($object), $attribute);
                if (null === $reflectionProperty) {
                    return;
                }
            } while ($class = get_parent_class($class));
        }

        if ($reflectionProperty->isStatic()) {
            return;
        }

        // Override visibility
        if (!$reflectionProperty->isPublic()) {
            $reflectionProperty->setAccessible(true);
        }

        $reflectionProperty->setValue($object, $value);
    }
}
