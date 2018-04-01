<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Form\Test\Traits;

use Symphony\Component\Form\Extension\Validator\ValidatorExtension;
use Symphony\Component\Form\Test\TypeTestCase;
use Symphony\Component\Validator\Mapping\ClassMetadata;
use Symphony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorExtensionTrait
{
    protected $validator;

    protected function getValidatorExtension()
    {
        if (!interface_exists(ValidatorInterface::class)) {
            throw new \Exception('In order to use the "ValidatorExtensionTrait", the symphony/validator component must be installed');
        }

        if (!$this instanceof TypeTestCase) {
            throw new \Exception(sprintf('The trait "ValidatorExtensionTrait" can only be added to a class that extends %s', TypeTestCase::class));
        }

        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();
        $this->validator->expects($this->any())->method('getMetadataFor')->will($this->returnValue($metadata));
        $this->validator->expects($this->any())->method('validate')->will($this->returnValue(array()));

        return new ValidatorExtension($this->validator);
    }
}
