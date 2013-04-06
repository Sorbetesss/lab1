<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor\Json;

use Symfony\Component\Console\Descriptor\DescriptorInterface;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
abstract class AbstractJsonDescriptor implements DescriptorInterface
{
    /**
     * @var int
     */
    private $encodingOptions;

    /**
     * @param int $encodingOptions
     */
    public function __construct($encodingOptions = 0)
    {
        $this->encodingOptions = $encodingOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $options)
    {
        if (isset($options['json_encoding'])) {
            $this->encodingOptions = $options['json_encoding'];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function describe($object)
    {
        return json_encode($this->getData($object), $this->encodingOptions);
    }

    /**
     * Returns object data to encode.
     *
     * @param object $object
     *
     * @return array
     */
    abstract public function getData($object);

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function useFormatting()
    {
        return false;
    }
}
