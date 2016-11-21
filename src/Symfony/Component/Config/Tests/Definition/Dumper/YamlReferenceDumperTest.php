<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Definition\Dumper;

use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Config\Tests\Fixtures\Configuration\ExampleConfiguration;

class YamlReferenceDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testDumper()
    {
        $configuration = new ExampleConfiguration();

        $dumper = new YamlReferenceDumper();

        $this->assertEquals($this->getConfigurationAsString(), $dumper->dump($configuration));
    }

    private function getConfigurationAsString()
    {
        return <<<'EOL'
acme_root:
    boolean:              true
    scalar_empty:         ~
    scalar_null:          null
    scalar_true:          true
    scalar_false:         false
    scalar_default:       default
    scalar_array_empty:   []
    scalar_array_defaults:

        # Defaults:
        - elem1
        - elem2
    scalar_required:      ~ # Required
    enum_with_default:    this # One of "this"; "that"
    enum:                 ~ # One of "this"; "that"
    datetime:             ~ # A timestamp or datetime string
    datetime_with_timezone:  ~ # A timestamp or datetime string (default timezone: "Japan")
    datetime_with_format_and_timezone:  ~ # A timestamp or datetime string matching the "d/m/Y" format (default timezone: "Japan")

    # some info
    array:
        child1:               ~
        child2:               ~

        # this is a long
        # multi-line info text
        # which should be indented
        child3:               ~ # Example: example setting
    scalar_prototyped:    []
    parameters:

        # Prototype: Parameter name
        name:                 ~
    connections:

        # Prototype
        -
            user:                 ~
            pass:                 ~
    cms_pages:

        # Prototype
        page:

            # Prototype
            locale:
                title:                ~ # Required
                path:                 ~ # Required
    pipou:

        # Prototype
        name:                 []

EOL;
    }
}
