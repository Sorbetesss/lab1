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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\EventListener\FixUrlProtocolListener;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @since v2.0.0
 */
class UrlType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new FixUrlProtocolListener($options['default_protocol']));
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'default_protocol' => 'http',
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.0.0
     */
    public function getName()
    {
        return 'url';
    }
}
