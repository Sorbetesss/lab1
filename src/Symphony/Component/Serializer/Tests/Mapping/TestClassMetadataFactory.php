<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Serializer\Tests\Mapping;

use Symphony\Component\Serializer\Mapping\AttributeMetadata;
use Symphony\Component\Serializer\Mapping\ClassMetadata;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class TestClassMetadataFactory
{
    public static function createClassMetadata($withParent = false, $withInterface = false)
    {
        $expected = new ClassMetadata('Symphony\Component\Serializer\Tests\Fixtures\GroupDummy');

        $foo = new AttributeMetadata('foo');
        $foo->addGroup('a');
        $expected->addAttributeMetadata($foo);

        $bar = new AttributeMetadata('bar');
        $bar->addGroup('b');
        $bar->addGroup('c');
        $bar->addGroup('name_converter');
        $expected->addAttributeMetadata($bar);

        $fooBar = new AttributeMetadata('fooBar');
        $fooBar->addGroup('a');
        $fooBar->addGroup('b');
        $fooBar->addGroup('name_converter');
        $expected->addAttributeMetadata($fooBar);

        $symphony = new AttributeMetadata('symphony');
        $expected->addAttributeMetadata($symphony);

        if ($withParent) {
            $kevin = new AttributeMetadata('kevin');
            $kevin->addGroup('a');
            $expected->addAttributeMetadata($kevin);

            $coopTilleuls = new AttributeMetadata('coopTilleuls');
            $coopTilleuls->addGroup('a');
            $coopTilleuls->addGroup('b');
            $expected->addAttributeMetadata($coopTilleuls);
        }

        if ($withInterface) {
            $symphony->addGroup('a');
            $symphony->addGroup('name_converter');
        }

        // load reflection class so that the comparison passes
        $expected->getReflectionClass();

        return $expected;
    }

    public static function createXmlCLassMetadata()
    {
        $expected = new ClassMetadata('Symphony\Component\Serializer\Tests\Fixtures\GroupDummy');

        $foo = new AttributeMetadata('foo');
        $foo->addGroup('group1');
        $foo->addGroup('group2');
        $expected->addAttributeMetadata($foo);

        $bar = new AttributeMetadata('bar');
        $bar->addGroup('group2');
        $expected->addAttributeMetadata($bar);

        return $expected;
    }
}
