<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Bundle\TwigBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symphony\Bundle\TwigBundle\DependencyInjection\Configuration;
use Symphony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDoNoDuplicateDefaultFormResources()
    {
        $input = array(
            'strict_variables' => false, // to be removed in 5.0 relying on default
            'form_themes' => array('form_div_layout.html.twig'),
        );

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array($input));

        $this->assertEquals(array('form_div_layout.html.twig'), $config['form_themes']);
    }

    /**
     * @group legacy
     * @expectedDeprecation Relying on the default value ("false") of the "twig.strict_variables" configuration option is deprecated since Symphony 4.1. You should use "%kernel.debug%" explicitly instead, which will be the new default in 5.0.
     */
    public function testGetStrictVariablesDefaultFalse()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array(array()));

        $this->assertFalse($config['strict_variables']);
    }
}
