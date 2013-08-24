<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MemoryDataCollector.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @since v2.0.0
 */
class MemoryDataCollector extends DataCollector
{
    /**
     * @since v2.2.0
     */
    public function __construct()
    {
        $this->data = array(
            'memory'       => 0,
            'memory_limit' => $this->convertToBytes(strtolower(ini_get('memory_limit'))),
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.0.0
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->updateMemoryUsage();
    }

    /**
     * Gets the memory.
     *
     * @return integer The memory
     *
     * @since v2.0.0
     */
    public function getMemory()
    {
        return $this->data['memory'];
    }

    /**
     * Gets the PHP memory limit.
     *
     * @return integer The memory limit
     *
     * @since v2.3.0
     */
    public function getMemoryLimit()
    {
        return $this->data['memory_limit'];
    }

    /**
     * Updates the memory usage data.
     *
     * @since v2.2.0
     */
    public function updateMemoryUsage()
    {
        $this->data['memory'] = memory_get_peak_usage(true);
    }

    /**
     * {@inheritdoc}
     *
     * @since v2.0.0
     */
    public function getName()
    {
        return 'memory';
    }

    /**
     * @since v2.3.0
     */
    private function convertToBytes($memoryLimit)
    {
        if ('-1' === $memoryLimit) {
            return -1;
        }

        if (preg_match('#^\+?(0x?)?(.*?)([kmg]?)$#', $memoryLimit, $match)) {
            $shifts = array('' => 0, 'k' => 10, 'm' => 20, 'g' => 30);
            $bases = array('' => 10, '0' => 8, '0x' => 16);

            return intval($match[2], $bases[$match[1]]) << $shifts[$match[3]];
        }

        return 0;
    }
}
