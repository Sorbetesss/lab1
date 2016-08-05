<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\AstGenerator\Tests\Hydrate;

use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\Argument;
use Symfony\Component\AstGenerator\AstGeneratorInterface;
use Symfony\Component\AstGenerator\Hydrate\ObjectHydrateFromArrayGenerator;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class ObjectHydrateFromArrayGeneratorTest extends AbstractHydratorTest
{
    public function testHydrateGenerator()
    {
        $propertyInfoExtractor = $this->getPropertyInfoExtractor(DummyObjectArray::class);
        $hydrateGenerator = new ObjectHydrateFromArrayGenerator($propertyInfoExtractor, new DummyObjectArrayTypeGenerator());

        $this->assertTrue($hydrateGenerator->supportsGeneration(DummyObjectArray::class));

        $array = [
            'bar' => 'test'
        ];

        eval($this->printer->prettyPrint($hydrateGenerator->generate(DummyObjectArray::class, [
            'input' => new Expr\Variable('array'),
            'output' => new Expr\Variable('object'),
        ])));

        $this->assertInstanceOf(DummyObjectArray::class, $object);
        $this->assertEquals('test', $object->bar);
    }

    /**
     * @expectedException \Symfony\Component\AstGenerator\Exception\MissingContextException
     */
    public function testNoInput()
    {
        $propertyInfoExtractor = $this->getMockBuilder(PropertyInfoExtractorInterface::class)->getMock();
        $hydrateGenerator = new ObjectHydrateFromArrayGenerator($propertyInfoExtractor, new DummyObjectArrayTypeGenerator());
        $hydrateGenerator->generate(DummyObjectArray::class);
    }

    /**
     * @expectedException \Symfony\Component\AstGenerator\Exception\MissingContextException
     */
    public function testNoOutput()
    {
        $propertyInfoExtractor = $this->getMockBuilder(PropertyInfoExtractorInterface::class)->getMock();
        $hydrateGenerator = new ObjectHydrateFromArrayGenerator($propertyInfoExtractor, new DummyObjectArrayTypeGenerator());
        $hydrateGenerator->generate(DummyObjectArray::class, ['input' => new Expr\Variable('test')]);
    }
}

class DummyObjectArray
{
    public $foo;

    public $bar;

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param mixed $bar
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }
}

class DummyObjectArrayTypeGenerator implements AstGeneratorInterface
{
    public function generate($object, array $context = [])
    {
        if (!isset($context['input'])) {
            throw new \Exception('no input');
        }

        if (!isset($context['output'])) {
            throw new \Exception('no output');
        }

        return [new Expr\Assign($context['output'], $context['input'])];
    }

    public function supportsGeneration($object)
    {
        return true;
    }
}
