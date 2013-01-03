<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

/**
 * UriSigner.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UriSigner
{
    private $secret;

    /**
     * Constructor.
     *
     * @param string $secret A secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Signs a URI.
     *
     * The given URI is signed by adding a _hash query string parameter
     * which value depends on the URI and the secret.
     *
     * @param string $uri A URI to sign
     *
     * @return string The signed URI
     */
    public function sign($uri)
    {
        return $uri.(false === (strpos($uri, '?')) ? '?' : '&').'_hash='.$this->computeHash($uri);
    }

    /**
     * Checks that a URI contains the correct hash.
     *
     * @param string $uri A signed URI
     *
     * @return Boolean True if the URI is signed correctly, false otherwise
     */
    public function check($uri)
    {
        if (!preg_match('/(\?|&)_hash=(.+?)(&|$)/', $uri, $matches, PREG_OFFSET_CAPTURE)) {
            return false;
        }

        // the naked URI is the URI without the _hash parameter (we need to keep the ? if there is some other parameters after)
        $offset = ('?' == $matches[1][0] && '&' != $matches[3][0]) ? 0 : 1;
        $nakedUri = substr($uri, 0, $matches[0][1] + $offset).substr($uri, $matches[0][1] + strlen($matches[0][0]));

        return $this->computeHash($nakedUri) === $matches[2][0];
    }

    private function computeHash($uri)
    {
        return urlencode(base64_encode(hash_hmac('sha1', $uri, $this->secret, true)));
    }
}
