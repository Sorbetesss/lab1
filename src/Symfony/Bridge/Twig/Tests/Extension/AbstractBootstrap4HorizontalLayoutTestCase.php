<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Tests\Extension;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;

/**
 * Abstract class providing test cases for the Bootstrap 4 horizontal Twig form theme.
 *
 * @author Hidde Wieringa <hidde@hiddewieringa.nl>
 */
abstract class AbstractBootstrap4HorizontalLayoutTestCase extends AbstractBootstrap4LayoutTestCase
{
    public function testRow()
    {
        $form = $this->factory->createNamed('name', TextType::class);
        $form->addError(new FormError('[trans]Error![/trans]'));
        $view = $form->createView();
        $html = $this->renderRow($view);

        $this->assertMatchesXpath($html,
            '/div
    [
        ./label[@for="name"]
        [
            ./span[@class="alert alert-danger d-block"]
                [./span[@class="d-block"]
                    [./span[.="[trans]Error[/trans]"]]
                    [./span[.="[trans]Error![/trans]"]]
                ]
                [count(./span)=1]
        ]
        /following-sibling::div[./input[@id="name"]]
    ]
'
        );
    }

    public function testLabelOnForm()
    {
        $form = $this->factory->createNamed('name', DateType::class);
        $view = $form->createView();
        $this->renderWidget($view, ['label' => 'foo']);
        $html = $this->renderLabel($view);

        $this->assertMatchesXpath($html,
            '/legend
    [@class="col-form-label col-sm-2 col-form-label required"]
    [.="[trans]Name[/trans]"]
'
        );
    }

    public function testLabelDoesNotRenderFieldAttributes()
    {
        $form = $this->factory->createNamed('name', TextType::class);
        $html = $this->renderLabel($form->createView(), null, [
            'attr' => [
                'class' => 'my&class',
            ],
        ]);

        $this->assertMatchesXpath($html,
            '/label
    [@for="name"]
    [@class="col-form-label col-sm-2 required"]
'
        );
    }

    public function testLabelWithCustomAttributesPassedDirectly()
    {
        $form = $this->factory->createNamed('name', TextType::class);
        $html = $this->renderLabel($form->createView(), null, [
            'label_attr' => [
                'class' => 'my&class',
            ],
        ]);

        $this->assertMatchesXpath($html,
            '/label
    [@for="name"]
    [@class="my&class col-form-label col-sm-2 required"]
'
        );
    }

    public function testLabelWithCustomTextAndCustomAttributesPassedDirectly()
    {
        $form = $this->factory->createNamed('name', TextType::class);
        $html = $this->renderLabel($form->createView(), 'Custom label', [
            'label_attr' => [
                'class' => 'my&class',
            ],
        ]);

        $this->assertMatchesXpath($html,
            '/label
    [@for="name"]
    [@class="my&class col-form-label col-sm-2 required"]
    [.="[trans]Custom label[/trans]"]
'
        );
    }

    public function testLabelWithCustomTextAsOptionAndCustomAttributesPassedDirectly()
    {
        $form = $this->factory->createNamed('name', TextType::class, null, [
            'label' => 'Custom label',
        ]);
        $html = $this->renderLabel($form->createView(), null, [
            'label_attr' => [
                'class' => 'my&class',
            ],
        ]);

        $this->assertMatchesXpath($html,
            '/label
    [@for="name"]
    [@class="my&class col-form-label col-sm-2 required"]
    [.="[trans]Custom label[/trans]"]
'
        );
    }

    public function testLabelHtmlDefaultIsFalse()
    {
        $form = $this->factory->createNamed('name', TextType::class, null, [
            'label' => '<b>Bolded label</b>',
        ]);

        $html = $this->renderLabel($form->createView(), null, [
            'label_attr' => [
                'class' => 'my&class',
            ],
        ]);

        $this->assertMatchesXpath($html, '/label[@for="name"][@class="my&class col-form-label col-sm-2 required"][.="[trans]<b>Bolded label</b>[/trans]"]');
        $this->assertMatchesXpath($html, '/label[@for="name"][@class="my&class col-form-label col-sm-2 required"]/b[.="Bolded label"]', 0);
    }

    public function testLabelHtmlIsTrue()
    {
        $form = $this->factory->createNamed('name', TextType::class, null, [
            'label' => '<b>Bolded label</b>',
            'label_html' => true,
        ]);

        $html = $this->renderLabel($form->createView(), null, [
            'label_attr' => [
                'class' => 'my&class',
            ],
        ]);

        $this->assertMatchesXpath($html, '/label[@for="name"][@class="my&class col-form-label col-sm-2 required"][.="[trans]<b>Bolded label</b>[/trans]"]', 0);
        $this->assertMatchesXpath($html, '/label[@for="name"][@class="my&class col-form-label col-sm-2 required"]/b[.="Bolded label"]');
    }

    public function testLegendOnExpandedType()
    {
        $form = $this->factory->createNamed('name', ChoiceType::class, null, [
            'label' => 'Custom label',
            'expanded' => true,
            'choices' => ['Choice&A' => '&a', 'Choice&B' => '&b'],
        ]);
        $view = $form->createView();
        $this->renderWidget($view);
        $html = $this->renderLabel($view);

        $this->assertMatchesXpath($html,
            '/legend
    [@class="col-sm-2 col-form-label required"]
    [.="[trans]Custom label[/trans]"]
'
        );
    }

    public function testStartTag()
    {
        $form = $this->factory->create(FormType::class, null, [
            'method' => 'get',
            'action' => 'http://example.com/directory',
        ]);

        $html = $this->renderStart($form->createView());

        $this->assertSame('<form name="form" method="get" action="http://example.com/directory">', $html);
    }

    public function testStartTagWithOverriddenVars()
    {
        $form = $this->factory->create(FormType::class, null, [
            'method' => 'put',
            'action' => 'http://example.com/directory',
        ]);

        $html = $this->renderStart($form->createView(), [
            'method' => 'post',
            'action' => 'http://foo.com/directory',
        ]);

        $this->assertSame('<form name="form" method="post" action="http://foo.com/directory">', $html);
    }

    public function testStartTagForMultipartForm()
    {
        $form = $this->factory->createBuilder(FormType::class, null, [
                'method' => 'get',
                'action' => 'http://example.com/directory',
            ])
            ->add('file', FileType::class)
            ->getForm();

        $html = $this->renderStart($form->createView());

        $this->assertSame('<form name="form" method="get" action="http://example.com/directory" enctype="multipart/form-data">', $html);
    }

    public function testStartTagWithExtraAttributes()
    {
        $form = $this->factory->create(FormType::class, null, [
            'method' => 'get',
            'action' => 'http://example.com/directory',
        ]);

        $html = $this->renderStart($form->createView(), [
            'attr' => ['class' => 'foobar'],
        ]);

        $this->assertSame('<form name="form" method="get" action="http://example.com/directory" class="foobar">', $html);
    }

    public function testCheckboxRow()
    {
        $form = $this->factory->createNamed('name', CheckboxType::class);
        $view = $form->createView();
        $html = $this->renderRow($view, ['label' => 'foo']);

        $this->assertMatchesXpath($html, '/div[@class="form-group row"]/div[@class="col-sm-2" or @class="col-sm-10"]', 2);
    }

    public function testCheckboxRowWithHelp()
    {
        $form = $this->factory->createNamed('name', CheckboxType::class);
        $view = $form->createView();
        $html = $this->renderRow($view, ['label' => 'foo', 'help' => 'really helpful text']);

        $this->assertMatchesXpath($html,
            '/div
    [@class="form-group row"]
    [
        ./div[@class="col-sm-2" or @class="col-sm-10"]
        /following-sibling::div[@class="col-sm-2" or @class="col-sm-10"]
        [
            ./small[text() = "[trans]really helpful text[/trans]"]
        ]
    ]
'
        );
    }

    public function testRadioRowWithHelp()
    {
        $form = $this->factory->createNamed('name', RadioType::class, false);
        $html = $this->renderRow($form->createView(), ['label' => 'foo', 'help' => 'really helpful text']);

        $this->assertMatchesXpath($html,
            '/div
    [@class="form-group row"]
    [
        ./div[@class="col-sm-2" or @class="col-sm-10"]
        /following-sibling::div[@class="col-sm-2" or @class="col-sm-10"]
        [
            ./small[text() = "[trans]really helpful text[/trans]"]
        ]
    ]
'
        );
    }
}
