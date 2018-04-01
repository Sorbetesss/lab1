<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Serializer\Encoder;

/**
 * Adds the support of an extra $context parameter for the supportsDecoding method.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface ContextAwareDecoderInterface extends DecoderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param array $context options that decoders have access to
     */
    public function supportsDecoding($format, array $context = array());
}
