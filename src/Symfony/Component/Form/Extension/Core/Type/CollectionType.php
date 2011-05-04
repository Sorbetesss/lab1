<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['modifiable'] && $options['prototype']) {
            $builder->add('$$name$$', $options['type'], array(
                'property_path' => false,
                'required' => false,
            ));
        }

        $listener = new ResizeFormListener($builder->getFormFactory(),
                $options['type'], $options['modifiable']);

        $builder->addEventSubscriber($listener);
    }

    public function buildViewBottomUp(FormView $view, FormInterface $form)
    {
        $children = $view->getChildren();

        // move the prototype aside so it's no longer a node in the view tree
        if (isset($children['$$name$$'])) {
            $view->set('prototype', $children['$$name$$']);
            unset($children['$$name$$']);
            $view->setChildren($children);
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'modifiable' => false,
            'prototype'  => true,
            'type' => 'text',
        );
    }

    public function getName()
    {
        return 'collection';
    }
}