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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @since v2.0.0
 */
class BirthdayType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'years' => range(date('Y') - 120, date('Y')),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.1.0
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.0.0
     */
    public function getName()
    {
        return 'birthday';
    }
}
