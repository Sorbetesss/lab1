<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow\MarkingStore;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Workflow\Marking;

/**
 * MultipleStateMarkingStore stores the marking into a property of the
 * subject.
 *
 * This store deals with a "multiple state" Marking. It means a subject can be
 * in many states at the same time.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class MultipleStateMarkingStore implements MarkingStoreInterface
{
    /** @var string the marking property name */
    private $property;
    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor a propertyaccessor to access the marking property */
    private $propertyAccessor;

    /** @var string the optional timestamp property name */
    private $timestampProperty;
    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor|PropertyAccessorInterface a propertyaccessor to access the timestamp field */
    private $timestampPropertyAccessor;

    public function __construct(string $property = 'marking', PropertyAccessorInterface $propertyAccessor = null, string $timestampProperty = null, PropertyAccessorInterface $timestampPropertyAccessor = null)
    {
        $this->property = $property;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();

        $this->timestampProperty = $timestampProperty;
        $this->timestampPropertyAccessor = $timestampPropertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getMarking($subject)
    {
        return new Marking($this->propertyAccessor->getValue($subject, $this->property) ?: array());
    }

    /**
     * {@inheritdoc}
     */
    public function setMarking($subject, Marking $marking)
    {
        $this->propertyAccessor->setValue($subject, $this->property, $marking->getPlaces());

        // set date / time
        if (null !== $this->timestampProperty) {
            $this->timestampPropertyAccessor->setValue($subject, $this->timestampProperty, new \DateTime());
        }
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
