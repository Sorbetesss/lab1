<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests\Extension\Csrf\Type;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\IdentityTranslator;

class FormTypeCsrfExtensionTest_ChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // The form needs a child in order to trigger CSRF protection by
        // default
        $builder->add('name', TextType::class);
    }
}

class FormTypeCsrfExtensionTest extends TypeTestCase
{
    /**
     * @var MockObject&CsrfTokenManagerInterface
     */
    protected $tokenManager;

    protected function setUp(): void
    {
        $this->tokenManager = $this->createMock(CsrfTokenManagerInterface::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        return array_merge(parent::getExtensions(), [
            new CsrfExtension($this->tokenManager, new IdentityTranslator()),
        ]);
    }

    public function testCsrfProtectionByDefaultIfRootAndCompound()
    {
        $view = $this->factory
            ->create(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'compound' => true,
            ])
            ->createView();

        $this->assertArrayHasKey('csrf', $view);
    }

    public function testNoCsrfProtectionByDefaultIfCompoundButNotRoot()
    {
        $view = $this->factory
            ->createNamedBuilder('root', FormType::class)
            ->add($this->factory
                ->createNamedBuilder('form', FormType::class, null, [
                    'csrf_field_name' => 'csrf',
                    'compound' => true,
                ])
            )
            ->getForm()
            ->get('form')
            ->createView();

        $this->assertArrayNotHasKey('csrf', $view);
    }

    public function testNoCsrfProtectionByDefaultIfRootButNotCompound()
    {
        $view = $this->factory
            ->create(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'compound' => false,
            ])
            ->createView();

        $this->assertArrayNotHasKey('csrf', $view);
    }

    public function testCsrfProtectionCanBeDisabled()
    {
        $view = $this->factory
            ->create(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_protection' => false,
                'compound' => true,
            ])
            ->createView();

        $this->assertArrayNotHasKey('csrf', $view);
    }

    public function testGenerateCsrfToken()
    {
        $this->tokenManager->expects($this->once())
            ->method('getToken')
            ->with('TOKEN_ID')
            ->willReturn(new CsrfToken('TOKEN_ID', 'token'));

        $view = $this->factory
            ->create(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'csrf_token_id' => 'TOKEN_ID',
                'compound' => true,
            ])
            ->createView();

        $this->assertEquals('token', $view['csrf']->vars['value']);
    }

    public function testGenerateCsrfTokenUsesFormNameAsIntentionByDefault()
    {
        $this->tokenManager->expects($this->once())
            ->method('getToken')
            ->with('FORM_NAME')
            ->willReturn(new CsrfToken('TOKEN_ID', 'token'));

        $view = $this->factory
            ->createNamed('FORM_NAME', FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'compound' => true,
            ])
            ->createView();

        $this->assertEquals('token', $view['csrf']->vars['value']);
    }

    public function testGenerateCsrfTokenUsesTypeClassAsIntentionIfEmptyFormName()
    {
        $this->tokenManager->expects($this->once())
            ->method('getToken')
            ->with(FormType::class)
            ->willReturn(new CsrfToken('TOKEN_ID', 'token'));

        $view = $this->factory
            ->createNamed('', FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'compound' => true,
            ])
            ->createView();

        $this->assertEquals('token', $view['csrf']->vars['value']);
    }

    public static function provideBoolean(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testValidateTokenOnSubmitIfRootAndCompound($valid)
    {
        $this->tokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with(new CsrfToken('TOKEN_ID', 'token'))
            ->willReturn($valid);

        $form = $this->factory
            ->createBuilder(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'csrf_token_id' => 'TOKEN_ID',
                'compound' => true,
            ])
            ->add('child', TextType::class)
            ->getForm();

        $form->submit([
            'child' => 'foobar',
            'csrf' => 'token',
        ]);

        // Remove token from data
        $this->assertSame(['child' => 'foobar'], $form->getData());

        // Validate accordingly
        $this->assertSame($valid, $form->isValid());
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testValidateTokenOnSubmitIfRootAndCompoundUsesFormNameAsIntentionByDefault($valid)
    {
        $this->tokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with(new CsrfToken('FORM_NAME', 'token'))
            ->willReturn($valid);

        $form = $this->factory
            ->createNamedBuilder('FORM_NAME', FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'compound' => true,
            ])
            ->add('child', TextType::class)
            ->getForm();

        $form->submit([
            'child' => 'foobar',
            'csrf' => 'token',
        ]);

        // Remove token from data
        $this->assertSame(['child' => 'foobar'], $form->getData());

        // Validate accordingly
        $this->assertSame($valid, $form->isValid());
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testValidateTokenOnSubmitIfRootAndCompoundUsesTypeClassAsIntentionIfEmptyFormName($valid)
    {
        $this->tokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with(new CsrfToken(FormType::class, 'token'))
            ->willReturn($valid);

        $form = $this->factory
            ->createNamedBuilder('', FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'compound' => true,
            ])
            ->add('child', TextType::class)
            ->getForm();

        $form->submit([
            'child' => 'foobar',
            'csrf' => 'token',
        ]);

        // Remove token from data
        $this->assertSame(['child' => 'foobar'], $form->getData());

        // Validate accordingly
        $this->assertSame($valid, $form->isValid());
    }

    public function testFailIfRootAndCompoundAndTokenMissing()
    {
        $this->tokenManager->expects($this->never())
            ->method('isTokenValid');

        $form = $this->factory
            ->createBuilder(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'csrf_token_id' => 'TOKEN_ID',
                'compound' => true,
            ])
            ->add('child', TextType::class)
            ->getForm();

        $form->submit([
            'child' => 'foobar',
            // token is missing
        ]);

        // Remove token from data
        $this->assertSame(['child' => 'foobar'], $form->getData());

        // Validate accordingly
        $this->assertFalse($form->isValid());
    }

    public function testDontValidateTokenIfCompoundButNoRoot()
    {
        $this->tokenManager->expects($this->never())
            ->method('isTokenValid');

        $form = $this->factory
            ->createNamedBuilder('root', FormType::class)
            ->add($this->factory
                ->createNamedBuilder('form', FormType::class, null, [
                    'csrf_field_name' => 'csrf',
                    'csrf_token_manager' => $this->tokenManager,
                    'csrf_token_id' => 'TOKEN_ID',
                    'compound' => true,
                ])
            )
            ->getForm()
            ->get('form');

        $form->submit([
            'child' => 'foobar',
            'csrf' => 'token',
        ]);
    }

    public function testDontValidateTokenIfRootButNotCompound()
    {
        $this->tokenManager->expects($this->never())
            ->method('isTokenValid');

        $form = $this->factory
            ->create(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'csrf_token_id' => 'TOKEN_ID',
                'compound' => false,
            ]);

        $form->submit([
            'csrf' => 'token',
        ]);
    }

    public function testNoCsrfProtectionOnPrototype()
    {
        $prototypeView = $this->factory
            ->create(CollectionType::class, null, [
                'entry_type' => __CLASS__.'_ChildType',
                'entry_options' => [
                    'csrf_field_name' => 'csrf',
                ],
                'prototype' => true,
                'allow_add' => true,
            ])
            ->createView()
            ->vars['prototype'];

        $this->assertArrayNotHasKey('csrf', $prototypeView);
        $this->assertCount(1, $prototypeView);
    }

    public function testsTranslateCustomErrorMessage()
    {
        $csrfToken = new CsrfToken('TOKEN_ID', 'token');
        $this->tokenManager->expects($this->once())
            ->method('isTokenValid')
            ->with($csrfToken)
            ->willReturn(false);

        $form = $this->factory
            ->createBuilder(FormType::class, null, [
                'csrf_field_name' => 'csrf',
                'csrf_token_manager' => $this->tokenManager,
                'csrf_message' => '[trans]Foobar[/trans]',
                'csrf_token_id' => 'TOKEN_ID',
                'compound' => true,
            ])
            ->getForm();

        $form->submit([
            'csrf' => 'token',
        ]);

        $errors = $form->getErrors();
        $expected = new FormError('[trans]Foobar[/trans]', null, [], null, $csrfToken);
        $expected->setOrigin($form);

        $this->assertGreaterThan(0, \count($errors));
        $this->assertEquals($expected, $errors[0]);
    }
}
