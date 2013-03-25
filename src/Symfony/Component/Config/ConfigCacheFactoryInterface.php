<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config;

/**
 * Interface for ConfigCacheFactory
 *
 * @author Benjamin Klotz <bk@webfactory.de>
 */
interface ConfigCacheFactoryInterface
{

    /**
     * Factory Method
     *
     * @param  string               $file     The absolute cache path
     * @param  callable             $callable The callable to be executed when the cache needs to be filled (i. e. is not fresh). The cache will be passed as the only parameter.
     * @return ConfigCacheInterface $configCache
     */
    public function cache($file, $callable);

}
