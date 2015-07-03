<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Request;


use Symfony\Component\HttpFoundation\Header\IfNoneMatch;
use Symfony\Component\HttpFoundation\Request;

class CacheHelper
{
    /**
     * Gets the Etags.
     *
     * @param Request $request
     * @return array The entity tags
     */
    public function getETags(Request $request)
    {
        return IfNoneMatch::fromString($request->headers->get('if_none_match'))->getETags();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isNoCache(Request $request)
    {
        return $request->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $request->headers->get('Pragma');
    }
}