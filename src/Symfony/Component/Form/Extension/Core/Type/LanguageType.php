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
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @since v2.0.0
 */
class LanguageType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => Intl::getLanguageBundle()->getLanguageNames(),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.0.0
     */
    public function getName()
    {
        return 'language';
    }
}
