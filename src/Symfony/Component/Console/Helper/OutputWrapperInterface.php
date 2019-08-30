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

interface OutputWrapperInterface
{
    const TAG_INNER_REGEX = '[a-z][^<>]*+';

    public function wrap(string $text, int $width, string $break = "\n"): string;
}
