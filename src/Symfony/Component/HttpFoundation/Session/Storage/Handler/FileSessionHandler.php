<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * FileSessionHandler.
 *
 * @author Drak <drak@zikula.org>
 */
class FileSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var string
     */
    private $savePath;

    /**
     * @var string
     */
    private $prefix;

    /**
     * Constructor.
     *
     * @param string $savePath Path of directory to save session files.
     * @param string $prefix
     */
    public function __construct($savePath = null, $prefix = 'sess_')
    {
        if (null === $savePath) {
            $savePath = sys_get_temp_dir();
        }

        $this->savePath = $savePath;
        if (false === is_dir($this->savePath)) {
            mkdir($this->savePath, 0777, true);
        }

        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($id)
    {
        $path = $this->getPath().$id;
        $data = '';
        if (is_readable($path)) {
            $handle = fopen($path, 'r');
            flock($handle, LOCK_SH);
            if (0 < $size = filesize($path)) {
                $data = fread($handle, $size);
            }
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function write($id, $data)
    {
        $path = $this->getPath().$id;
        $handle = fopen($path, 'a');
        flock($handle, LOCK_EX);
        ftruncate($handle, 0);
        $success = (bool) fwrite($handle, $data);
        flock($handle, LOCK_UN);
        fclose($handle);

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($id)
    {
        $file = $this->getPath().$id;
        if (is_file($file)) {
            unlink($file);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        foreach (glob($this->getPath().'*') as $file) {
            if ((filemtime($file) + $maxlifetime) < time()) {
                unlink($file);
            }
        }

        return true;
    }

    private function getPath()
    {
        return $this->savePath.'/'.$this->prefix;
    }
}
