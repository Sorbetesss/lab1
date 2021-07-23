<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Adapter;

use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class DebugTagAwareAdapter extends DebugAdapter implements  TagAwareAdapterInterface, TagAwareCacheInterface
{
    public function invalidateTags(array $tags): bool
    {
        return $this->decoratedAdapter->invalidateTags($tags);
    }
}
