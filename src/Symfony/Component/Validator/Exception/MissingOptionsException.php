<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Exception;

/**
 * @since v2.0.0
 */
class MissingOptionsException extends ValidatorException
{
    private $options;

    /**
     * @since v2.0.0
     */
    public function __construct($message, array $options)
    {
        parent::__construct($message);

        $this->options = $options;
    }

    /**
     * @since v2.0.0
     */
    public function getOptions()
    {
        return $this->options;
    }
}
