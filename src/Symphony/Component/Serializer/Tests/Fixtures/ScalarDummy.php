<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Serializer\Tests\Fixtures;

use Symphony\Component\Serializer\Normalizer\NormalizableInterface;
use Symphony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symphony\Component\Serializer\Normalizer\NormalizerInterface;
use Symphony\Component\Serializer\Normalizer\DenormalizerInterface;

class ScalarDummy implements NormalizableInterface, DenormalizableInterface
{
    public $foo;
    public $xmlFoo;

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        return 'xml' === $format ? $this->xmlFoo : $this->foo;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        if ('xml' === $format) {
            $this->xmlFoo = $data;
        } else {
            $this->foo = $data;
        }
    }
}
