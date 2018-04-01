<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Form\Tests\Console\Descriptor;

use PHPUnit\Framework\TestCase;
use Symphony\Component\Console\Input\ArrayInput;
use Symphony\Component\Console\Output\BufferedOutput;
use Symphony\Component\Console\Style\SymphonyStyle;
use Symphony\Component\Form\AbstractType;
use Symphony\Component\Form\Extension\Core\Type\ChoiceType;
use Symphony\Component\Form\Extension\Core\Type\FormType;
use Symphony\Component\Form\Extension\Csrf\Type\FormTypeCsrfExtension;
use Symphony\Component\Form\FormInterface;
use Symphony\Component\Form\ResolvedFormType;
use Symphony\Component\Form\ResolvedFormTypeInterface;
use Symphony\Component\OptionsResolver\Options;
use Symphony\Component\OptionsResolver\OptionsResolver;
use Symphony\Component\Security\Csrf\CsrfTokenManager;

abstract class AbstractDescriptorTest extends TestCase
{
    /** @dataProvider getDescribeDefaultsTestData */
    public function testDescribeDefaults($object, array $options, $fixtureName)
    {
        $describedObject = $this->getObjectDescription($object, $options);
        $expectedDescription = $this->getExpectedDescription($fixtureName);

        if ('json' === $this->getFormat()) {
            $this->assertEquals(json_encode(json_decode($expectedDescription), JSON_PRETTY_PRINT), json_encode(json_decode($describedObject), JSON_PRETTY_PRINT));
        } else {
            $this->assertEquals(trim($expectedDescription), trim(str_replace(PHP_EOL, "\n", $describedObject)));
        }
    }

    /** @dataProvider getDescribeResolvedFormTypeTestData */
    public function testDescribeResolvedFormType(ResolvedFormTypeInterface $type, array $options, $fixtureName)
    {
        $describedObject = $this->getObjectDescription($type, $options);
        $expectedDescription = $this->getExpectedDescription($fixtureName);

        if ('json' === $this->getFormat()) {
            $this->assertEquals(json_encode(json_decode($expectedDescription), JSON_PRETTY_PRINT), json_encode(json_decode($describedObject), JSON_PRETTY_PRINT));
        } else {
            $this->assertEquals(trim($expectedDescription), trim(str_replace(PHP_EOL, "\n", $describedObject)));
        }
    }

    /** @dataProvider getDescribeOptionTestData */
    public function testDescribeOption(OptionsResolver $optionsResolver, array $options, $fixtureName)
    {
        $describedObject = $this->getObjectDescription($optionsResolver, $options);
        $expectedDescription = $this->getExpectedDescription($fixtureName);

        if ('json' === $this->getFormat()) {
            $this->assertEquals(json_encode(json_decode($expectedDescription), JSON_PRETTY_PRINT), json_encode(json_decode($describedObject), JSON_PRETTY_PRINT));
        } else {
            $this->assertStringMatchesFormat(trim($expectedDescription), trim(str_replace(PHP_EOL, "\n", $describedObject)));
        }
    }

    public function getDescribeDefaultsTestData()
    {
        $options['core_types'] = array('Symphony\Component\Form\Extension\Core\Type\FormType');
        $options['service_types'] = array('Symphony\Bridge\Doctrine\Form\Type\EntityType');
        $options['extensions'] = array('Symphony\Component\Form\Extension\Csrf\Type\FormTypeCsrfExtension');
        $options['guessers'] = array('Symphony\Component\Form\Extension\Validator\ValidatorTypeGuesser');
        $options['decorated'] = false;

        yield array(null, $options, 'defaults_1');
    }

    public function getDescribeResolvedFormTypeTestData()
    {
        $typeExtensions = array(new FormTypeCsrfExtension(new CsrfTokenManager()));
        $parent = new ResolvedFormType(new FormType(), $typeExtensions);

        yield array(new ResolvedFormType(new ChoiceType(), array(), $parent), array('decorated' => false), 'resolved_form_type_1');
        yield array(new ResolvedFormType(new FormType()), array('decorated' => false), 'resolved_form_type_2');
    }

    public function getDescribeOptionTestData()
    {
        $parent = new ResolvedFormType(new FormType());
        $options['decorated'] = false;

        $resolvedType = new ResolvedFormType(new ChoiceType(), array(), $parent);
        $options['type'] = $resolvedType->getInnerType();
        $options['option'] = 'choice_translation_domain';
        yield array($resolvedType->getOptionsResolver(), $options, 'default_option_with_normalizer');

        $resolvedType = new ResolvedFormType(new FooType(), array(), $parent);
        $options['type'] = $resolvedType->getInnerType();
        $options['option'] = 'foo';
        yield array($resolvedType->getOptionsResolver(), $options, 'required_option_with_allowed_values');

        $options['option'] = 'empty_data';
        yield array($resolvedType->getOptionsResolver(), $options, 'overridden_option_with_default_closures');
    }

    abstract protected function getDescriptor();

    abstract protected function getFormat();

    private function getObjectDescription($object, array $options)
    {
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, $options['decorated']);
        $io = new SymphonyStyle(new ArrayInput(array()), $output);

        $this->getDescriptor()->describe($io, $object, $options);

        return $output->fetch();
    }

    private function getExpectedDescription($name)
    {
        return file_get_contents($this->getFixtureFilename($name));
    }

    private function getFixtureFilename($name)
    {
        return sprintf('%s/../../Fixtures/Descriptor/%s.%s', __DIR__, $name, $this->getFormat());
    }
}

class FooType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('foo');
        $resolver->setDefault('empty_data', function (Options $options, $value) {
            $foo = $options['foo'];

            return function (FormInterface $form) use ($foo) {
                return $form->getConfig()->getCompound() ? array($foo) : $foo;
            };
        });
        $resolver->setAllowedTypes('foo', 'string');
        $resolver->setAllowedValues('foo', array('bar', 'baz'));
        $resolver->setNormalizer('foo', function (Options $options, $value) {
            return (string) $value;
        });
    }
}
