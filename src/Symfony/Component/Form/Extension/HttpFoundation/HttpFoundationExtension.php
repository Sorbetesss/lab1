<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\HttpFoundation;

use Symfony\Component\Form\AbstractExtension;

/**
 * Integrates the HttpFoundation component with the Form library.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @since v2.1.0
 */
class HttpFoundationExtension extends AbstractExtension
{
    /**
     * @since v2.1.0
     */
    protected function loadTypeExtensions()
    {
        return array(
            new Type\FormTypeHttpFoundationExtension(),
        );
    }
}
