<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Helper\Formatter\BlockFormatter;
use Symfony\Component\Console\Helper\Formatter\FormatterInterface;
use Symfony\Component\Console\Helper\Formatter\SectionFormatter;

/**
 * The Formatter class provides helpers to format messages.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FormatterHelper extends Helper
{
    /**
     * @param FormatterInterface $formatter
     *
     * @return array|string
     */
    public function format(FormatterInterface $formatter)
    {
        return $formatter->format();
    }

    /**
     * Formats a message within a section.
     *
     * @param string $section The section name
     * @param string $message The message
     * @param string $style   The style to apply to the section
     *
     * @return string The format section
     */
    public function formatSection($section, $message, $style = 'info')
    {
        return $this->format(new SectionFormatter($section, $message, $style));
    }

    /**
     * Formats a message as a block of text.
     *
     * @param string|array $messages  The message to write in the block
     * @param string       $style     The style to apply to the whole block
     * @param bool         $large     Whether to return a large block
     * @param int          $padLength Length to pad the messages
     *
     * @return string The formatter message
     */
    public function formatBlock($messages, $style, $large = false, $padLength = 0)
    {
        return $this->format(new BlockFormatter($messages, $style, $large, $padLength));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'formatter';
    }
}
