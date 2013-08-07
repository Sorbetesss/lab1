<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Scanner;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class Scanner implements \IteratorAggregate
{
    private $rootPath;
    private $constraints;
    private $ignoreAccessDenied;
    private $scannedFiles;

    public function __construct($rootPath, Constraints $constraints, $ignoreAccessDenied)
    {
        $this->rootPath = $rootPath;
        $this->constraints = $constraints;
        $this->ignoreAccessDenied = $ignoreAccessDenied;
        $this->scannedFiles = new \ArrayIterator();
    }

    public function getIterator()
    {
        $this->scanDirectory('', 0);

        return $this->scannedFiles;
    }

    private function scanDirectory($relativePath, $relativeDepth, $relativePathIncluded = false)
    {
        $rootPath = $relativePath ? $this->rootPath.'/'.$relativePath : $this->rootPath;

        if (false === $filenames = @scandir($rootPath)) {
            if ($this->ignoreAccessDenied) {
                return;
            }
            throw new AccessDeniedException(sprintf('Directory "%s" is not readable.', $rootPath));
        }

        $keepFiles = $this->constraints->isMinDepthRespected($relativeDepth);
        $relativeDepth = $relativeDepth + 1;

        foreach ($this->constraints->filterFilenames($filenames) as $filename) {
            $rootPathname = $rootPath.'/'.$filename;

            $relativePathname = $relativePath ? $relativePath.'/'.$filename : $filename;
            if ($this->constraints->isPathnameExcluded($relativePathname)) {
                continue;
            }

            $pathnameIncluded = $relativePathIncluded || $this->constraints->isPathnameIncluded($relativePathname);
            if (is_dir($rootPathname)) {
                if ($keepFiles && $pathnameIncluded && $this->constraints->isDirectoryKept($relativePathname, $filename)) {
                    $this->scannedFiles[$rootPathname] = new SplFileInfo($rootPathname, $relativePath, $relativePathname);
                }
                if (!$this->constraints->isMaxDepthExceeded($relativeDepth)) {
                    $this->scanDirectory($relativePathname, $relativeDepth, $pathnameIncluded);
                }
            } elseif($keepFiles && $pathnameIncluded && $this->constraints->isFileKept($relativePathname, $filename)) {
                $this->scannedFiles[$rootPathname] = new SplFileInfo($rootPathname, $relativePath, $relativePathname);
            }
        }
    }
}
