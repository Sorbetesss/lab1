<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\Finder\Iterator;

use Symfony\Component\Finder\Iterator\DepthRangeFilterIterator;
use Symfony\Component\Finder\Comparator\NumberComparator;

require_once __DIR__.'/RealIteratorTestCase.php';

class DepthRangeFilterIteratorTest extends RealIteratorTestCase
{
    /**
     * @dataProvider getAcceptData
     */
    public function testAccept($size, $expected)
    {
        $inner = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getAbsolutePath(''), \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

        $iterator = new DepthRangeFilterIterator($inner, $size);

        $actual = array_keys(iterator_to_array($iterator));
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    public function getAcceptData()
    {
        return array(
            array(array(new NumberComparator('< 1')), array($this->getAbsolutePath('/.git'), $this->getAbsolutePath('/test.py'), $this->getAbsolutePath('/foo'), $this->getAbsolutePath('/test.php'), $this->getAbsolutePath('/toto'))),
            array(array(new NumberComparator('<= 1')), array($this->getAbsolutePath('/.git'), $this->getAbsolutePath('/test.py'), $this->getAbsolutePath('/foo'), $this->getAbsolutePath('/foo/bar.tmp'), $this->getAbsolutePath('/test.php'), $this->getAbsolutePath('/toto'))),
            array(array(new NumberComparator('> 1')), array()),
            array(array(new NumberComparator('>= 1')), array($this->getAbsolutePath('/foo/bar.tmp'))),
            array(array(new NumberComparator('1')), array($this->getAbsolutePath('/foo/bar.tmp'))),
        );
    }

    protected function getAbsolutePath($path)
    {
        return sys_get_temp_dir().'/symfony2000_finder'.str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
