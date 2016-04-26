<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Adapter;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @group time-sensitive
 */
class FilesystemAdapterTest extends TagsInvalidatingAdapterTestCase
{
    public function createCachePool()
    {
        if (defined('HHVM_VERSION')) {
            $this->skippedTests['testDeferredSaveWithoutCommit'] = 'Fails on HHVM';
        }

        return new FilesystemAdapter('sf-cache');
    }

    public static function tearDownAfterClass()
    {
        self::rmdir(sys_get_temp_dir().'/symfony-cache');
    }

    public static function rmdir($dir)
    {
        if (!file_exists($dir)) {
            return;
        }
        if (!$dir || 0 !== strpos(dirname($dir), sys_get_temp_dir())) {
            throw new \Exception(__METHOD__."() operates only on subdirs of system's temp dir");
        }
        $children = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($children as $child) {
            if ($child->isDir()) {
                rmdir($child);
            } else {
                unlink($child);
            }
        }
        rmdir($dir);
    }
}
