<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Filesystem\Exception;

/**
 * Exception class thrown when a filesystem operation failure happens
 *
 * @author Romain Neutron <imprec@gmail.com>
 * @author Christian Gärtner <christiangaertner.film@googlemail.com>
 *
 * @api
 */
class IOException extends \RuntimeException implements IOExceptionInterface
{

    private $path;

    /**
     * Set the path associated with this IOException
     * @param string $path The path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}
