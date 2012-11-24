<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class Callback extends Constraint
{
    public $methods;

    /**
     * {@inheritDoc}
     */
    public function __construct($options = null)
    {
        if ($options instanceof \Closure) {
            $options = array($options);
        }

        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('methods');
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'methods';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return array(self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT);
    }
}
