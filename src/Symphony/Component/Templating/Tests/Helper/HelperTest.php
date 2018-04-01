<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Templating\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Symphony\Component\Templating\Helper\Helper;

class HelperTest extends TestCase
{
    public function testGetSetCharset()
    {
        $helper = new ProjectTemplateHelper();
        $helper->setCharset('ISO-8859-1');
        $this->assertSame('ISO-8859-1', $helper->getCharset(), '->setCharset() sets the charset set related to this helper');
    }
}

class ProjectTemplateHelper extends Helper
{
    public function getName()
    {
        return 'foo';
    }
}
