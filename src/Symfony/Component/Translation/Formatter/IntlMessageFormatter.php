<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Formatter;

/**
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
class IntlMessageFormatter implements MessageFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($locale, $id, array $parameters = array())
    {
        if (!$parameters) {
            return $id;
        }

        $formatter = new \MessageFormatter($locale, $id);
        if (null === $formatter) {
            throw new \InvalidArgumentException(sprintf('Invalid message format. Reason: %s (error #%d)', intl_get_error_message(), intl_get_error_code()));
        }

        $message = $formatter->format($parameters);
        if ($formatter->getErrorCode() !== U_ZERO_ERROR) {
            throw new \InvalidArgumentException(sprintf('Unable to format message. Reason: %s (error #%s)', $formatter->getErrorMessage(), $formatter->getErrorCode()));
        }

        if (!$formatter->parse($message) && $formatter->getErrorCode() === U_ZERO_ERROR) {
            @trigger_error('Passing a MessageSelector instance into the '.__METHOD__.' as a second argument is deprecated since version 2.8 and will be removed in 3.0. Inject a MessageFormatterInterface instance instead.', E_USER_DEPRECATED);

            return strtr($message, $parameters);
        }

        return $message;
    }
}
