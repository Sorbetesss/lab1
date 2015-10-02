<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Asset\VersionStrategy;

/**
 * Returns the same version for all assets.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class StaticVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $format;

    /**
     * @param string      $version
     * @param string|null $format
     */
    public function __construct($version, $format = null)
    {
        $this->version = $version;
        $this->format = $format ?: '%s?%s';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion($path)
    {
        $versionized = sprintf($this->format, ltrim($path, '/'), $this->getVersion($path));

        if ($path && '/' == $path[0]) {
            return '/'.$versionized;
        }

        return $versionized;
    }
}
