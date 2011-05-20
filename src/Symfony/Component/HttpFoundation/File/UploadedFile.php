<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * A file uploaded through a form.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UploadedFile extends File
{
    /**
     * Whether the file has been persisted
     *
     * @var Boolean
     */
    protected $persisted = false;

    /**
     * The original name of the uploaded file.
     *
     * @var string
     */
    private $originalName;

    /**
     * The mime type provided by the uploader.
     *
     * @var string
     */
    private $mimeType;

    /**
     * The file size provided by the uploader.
     *
     * @var string
     */
    private $size;

    /**
     * The UPLOAD_ERR_XXX constant provided by the uploader.
     *
     * @var integer
     */
    private $error;

    /**
     * Accepts the information of the uploaded file as provided by the PHP global $_FILES.
     *
     * @param string  $path         The full temporary path to the file
     * @param string  $originalName The original file name
     * @param string  $mimeType     The type of the file as provided by PHP
     * @param integer $size         The file size
     * @param integer $error        The error constant of the upload (one of PHP's UPLOAD_ERR_XXX constants)
     *
     * @throws FileException         If file_uploads is disabled
     * @throws FileNotFoundException If the file does not exist
     */
    public function __construct($path, $originalName, $mimeType = null, $size = null, $error = null)
    {
        if (!ini_get('file_uploads')) {
            throw new FileException(sprintf('Unable to create UploadedFile because "file_uploads" is disabled in your php.ini file (%s)', get_cfg_var('cfg_file_path')));
        }

        $this->originalName = basename($originalName);
        $this->mimeType = $mimeType ?: 'application/octet-stream';
        $this->size = $size;
        $this->error = $error ?: UPLOAD_ERR_OK;

        parent::__construct($path);
    }

    /**
     * Returns the original file name.
     *
     * It is extracted from the request from which the file has been uploaded.
     * Then is should not be considered as a safe value.
     *
     * @return string|null The original name
     */
    public function getClientOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Returns the file mime type.
     *
     * It is extracted from the request from which the file has been uploaded.
     * Then is should not be considered as a safe value.
     *
     * @return string|null The mime type
     */
    public function getClientMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Returns the file size.
     *
     * It is extracted from the request from which the file has been uploaded.
     * Then is should not be considered as a safe value.
     *
     * @return integer|null The file size
     */
    public function getClientSize()
    {
        return $this->size;
    }

    /**
     * Returns the upload error.
     *
     * If the upload was successful, the constant UPLOAD_ERR_OK is returned.
     * Otherwise one of the other UPLOAD_ERR_XXX constants is returned.
     *
     * @return integer The upload error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Returns whether the file was uploaded successfully.
     *
     * @return Boolean  True if no error occurred during uploading
     */
    public function isValid()
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    /**
     * Moves the file to a new location.
     *
     * @param string $directory The destination folder
     * @param string $name      The new file name
     *
     * @return File A File object representing the new file
     *
     * @throws FileException if the file has not been uploaded via Http
     */
    public function move($directory, $name = null)
    {
        if (!$this->persisted && !is_uploaded_file($this->getPathname())) {
            throw new FileException(sprintf('The file "%s" has not been uploaded via Http', $this->getPathname()));
        }

        return parent::move($directory, $name);
    }
}
