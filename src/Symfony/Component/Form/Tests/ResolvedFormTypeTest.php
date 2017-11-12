<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ResolvedFormTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataMapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormTypeInterface
     */
    private $parentType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormTypeInterface
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormTypeExtensionInterface
     */
    private $extension1;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormTypeExtensionInterface
     */
    private $extension2;

    /**
     * @var ResolvedFormType
     */
    private $parentResolvedType;

    /**
     * @var ResolvedFormType
     */
    private $resolvedType;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->factory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->getMock();
        $this->dataMapper = $this->getMockBuilder('Symfony\Component\Form\DataMapperInterface')->getMock();
        $this->parentType = $this->getMockFormType();
        $this->type = $this->getMockFormType();
        $this->extension1 = $this->getMockFormTypeExtension();
        $this->extension2 = $this->getMockFormTypeExtension();
        $this->parentResolvedType = new ResolvedFormType($this->parentType);
        $this->resolvedType = new ResolvedFormType($this->type, array($this->extension1, $this->extension2), $this->parentResolvedType);
    }

    public function testGetOptionsResolver(): void
    {
        $i = 0;

        $assertIndexAndAddOption = function ($index, $option, $default) use (&$i) {
            return function (OptionsResolver $resolver) use (&$i, $index, $option, $default): void {
                $this->assertEquals($index, $i, 'Executed at index '.$index);

                ++$i;

                $resolver->setDefaults(array($option => $default));
            };
        };

        // First the default options are generated for the super type
        $this->parentType->expects($this->once())
            ->method('configureOptions')
            ->will($this->returnCallback($assertIndexAndAddOption(0, 'a', 'a_default')));

        // The form type itself
        $this->type->expects($this->once())
            ->method('configureOptions')
            ->will($this->returnCallback($assertIndexAndAddOption(1, 'b', 'b_default')));

        // And its extensions
        $this->extension1->expects($this->once())
            ->method('configureOptions')
            ->will($this->returnCallback($assertIndexAndAddOption(2, 'c', 'c_default')));

        $this->extension2->expects($this->once())
            ->method('configureOptions')
            ->will($this->returnCallback($assertIndexAndAddOption(3, 'd', 'd_default')));

        $givenOptions = array('a' => 'a_custom', 'c' => 'c_custom');
        $resolvedOptions = array('a' => 'a_custom', 'b' => 'b_default', 'c' => 'c_custom', 'd' => 'd_default');

        $resolver = $this->resolvedType->getOptionsResolver();

        $this->assertEquals($resolvedOptions, $resolver->resolve($givenOptions));
    }

    public function testCreateBuilder(): void
    {
        $givenOptions = array('a' => 'a_custom', 'c' => 'c_custom');
        $resolvedOptions = array('a' => 'a_custom', 'b' => 'b_default', 'c' => 'c_custom', 'd' => 'd_default');
        $optionsResolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')->getMock();

        $this->resolvedType = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormType')
            ->setConstructorArgs(array($this->type, array($this->extension1, $this->extension2), $this->parentResolvedType))
            ->setMethods(array('getOptionsResolver'))
            ->getMock();

        $this->resolvedType->expects($this->once())
            ->method('getOptionsResolver')
            ->will($this->returnValue($optionsResolver));

        $optionsResolver->expects($this->once())
            ->method('resolve')
            ->with($givenOptions)
            ->will($this->returnValue($resolvedOptions));

        $factory = $this->getMockFormFactory();
        $builder = $this->resolvedType->createBuilder($factory, 'name', $givenOptions);

        $this->assertSame($this->resolvedType, $builder->getType());
        $this->assertSame($resolvedOptions, $builder->getOptions());
        $this->assertNull($builder->getDataClass());
    }

    public function testCreateBuilderWithDataClassOption(): void
    {
        $givenOptions = array('data_class' => 'Foo');
        $resolvedOptions = array('data_class' => '\stdClass');
        $optionsResolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')->getMock();

        $this->resolvedType = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormType')
            ->setConstructorArgs(array($this->type, array($this->extension1, $this->extension2), $this->parentResolvedType))
            ->setMethods(array('getOptionsResolver'))
            ->getMock();

        $this->resolvedType->expects($this->once())
            ->method('getOptionsResolver')
            ->will($this->returnValue($optionsResolver));

        $optionsResolver->expects($this->once())
            ->method('resolve')
            ->with($givenOptions)
            ->will($this->returnValue($resolvedOptions));

        $factory = $this->getMockFormFactory();
        $builder = $this->resolvedType->createBuilder($factory, 'name', $givenOptions);

        $this->assertSame($this->resolvedType, $builder->getType());
        $this->assertSame($resolvedOptions, $builder->getOptions());
        $this->assertSame('\stdClass', $builder->getDataClass());
    }

    public function testBuildForm(): void
    {
        $i = 0;

        $assertIndex = function ($index) use (&$i) {
            return function () use (&$i, $index): void {
                $this->assertEquals($index, $i, 'Executed at index '.$index);

                ++$i;
            };
        };

        $options = array('a' => 'Foo', 'b' => 'Bar');
        $builder = $this->getMockBuilder('Symfony\Component\Form\Test\FormBuilderInterface')->getMock();

        // First the form is built for the super type
        $this->parentType->expects($this->once())
            ->method('buildForm')
            ->with($builder, $options)
            ->will($this->returnCallback($assertIndex(0)));

        // Then the type itself
        $this->type->expects($this->once())
            ->method('buildForm')
            ->with($builder, $options)
            ->will($this->returnCallback($assertIndex(1)));

        // Then its extensions
        $this->extension1->expects($this->once())
            ->method('buildForm')
            ->with($builder, $options)
            ->will($this->returnCallback($assertIndex(2)));

        $this->extension2->expects($this->once())
            ->method('buildForm')
            ->with($builder, $options)
            ->will($this->returnCallback($assertIndex(3)));

        $this->resolvedType->buildForm($builder, $options);
    }

    public function testCreateView(): void
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();

        $view = $this->resolvedType->createView($form);

        $this->assertInstanceOf('Symfony\Component\Form\FormView', $view);
        $this->assertNull($view->parent);
    }

    public function testCreateViewWithParent(): void
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $parentView = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();

        $view = $this->resolvedType->createView($form, $parentView);

        $this->assertInstanceOf('Symfony\Component\Form\FormView', $view);
        $this->assertSame($parentView, $view->parent);
    }

    public function testBuildView(): void
    {
        $options = array('a' => '1', 'b' => '2');
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $view = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();

        $i = 0;

        $assertIndex = function ($index) use (&$i) {
            return function () use (&$i, $index): void {
                $this->assertEquals($index, $i, 'Executed at index '.$index);

                ++$i;
            };
        };

        // First the super type
        $this->parentType->expects($this->once())
            ->method('buildView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(0)));

        // Then the type itself
        $this->type->expects($this->once())
            ->method('buildView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(1)));

        // Then its extensions
        $this->extension1->expects($this->once())
            ->method('buildView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(2)));

        $this->extension2->expects($this->once())
            ->method('buildView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(3)));

        $this->resolvedType->buildView($view, $form, $options);
    }

    public function testFinishView(): void
    {
        $options = array('a' => '1', 'b' => '2');
        $form = $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
        $view = $this->getMockBuilder('Symfony\Component\Form\FormView')->getMock();

        $i = 0;

        $assertIndex = function ($index) use (&$i) {
            return function () use (&$i, $index): void {
                $this->assertEquals($index, $i, 'Executed at index '.$index);

                ++$i;
            };
        };

        // First the super type
        $this->parentType->expects($this->once())
            ->method('finishView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(0)));

        // Then the type itself
        $this->type->expects($this->once())
            ->method('finishView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(1)));

        // Then its extensions
        $this->extension1->expects($this->once())
            ->method('finishView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(2)));

        $this->extension2->expects($this->once())
            ->method('finishView')
            ->with($view, $form, $options)
            ->will($this->returnCallback($assertIndex(3)));

        $this->resolvedType->finishView($view, $form, $options);
    }

    public function testGetBlockPrefix(): void
    {
        $this->type->expects($this->once())
            ->method('getBlockPrefix')
            ->willReturn('my_prefix');

        $resolvedType = new ResolvedFormType($this->type);

        $this->assertSame('my_prefix', $resolvedType->getBlockPrefix());
    }

    /**
     * @dataProvider provideTypeClassBlockPrefixTuples
     */
    public function testBlockPrefixDefaultsToFQCNIfNoName($typeClass, $blockPrefix): void
    {
        $resolvedType = new ResolvedFormType(new $typeClass());

        $this->assertSame($blockPrefix, $resolvedType->getBlockPrefix());
    }

    public function provideTypeClassBlockPrefixTuples()
    {
        return array(
            array(__NAMESPACE__.'\Fixtures\FooType', 'foo'),
            array(__NAMESPACE__.'\Fixtures\Foo', 'foo'),
            array(__NAMESPACE__.'\Fixtures\Type', 'type'),
            array(__NAMESPACE__.'\Fixtures\FooBarHTMLType', 'foo_bar_html'),
            array(__NAMESPACE__.'\Fixtures\Foo1Bar2Type', 'foo1_bar2'),
            array(__NAMESPACE__.'\Fixtures\FBooType', 'f_boo'),
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockFormType($typeClass = 'Symfony\Component\Form\AbstractType'): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder($typeClass)->setMethods(array('getBlockPrefix', 'configureOptions', 'finishView', 'buildView', 'buildForm'))->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockFormTypeExtension(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder('Symfony\Component\Form\AbstractTypeExtension')->setMethods(array('getExtendedType', 'configureOptions', 'finishView', 'buildView', 'buildForm'))->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockFormFactory(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->getMock();
    }

    /**
     * @param string $name
     *
     * @return FormBuilder
     */
    protected function getBuilder(string $name = 'name', array $options = array()): FormBuilder
    {
        return new FormBuilder($name, null, $this->dispatcher, $this->factory, $options);
    }
}
