<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Descriptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Descriptor\DescriptorInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getDescribeInputArgumentTestData */
    public function testDescribeInputArgument(DescriptorInterface $descriptor, InputArgument $argument, $expectedDescription)
    {
        $this->assertEquals(trim($expectedDescription), trim($descriptor->describeInputArgument($argument)));
    }

    /** @dataProvider getDescribeInputOptionTestData */
    public function testDescribeInputOption(DescriptorInterface $descriptor, InputOption $option, $expectedDescription)
    {
        $this->assertEquals(trim($expectedDescription), trim($descriptor->describeInputOption($option)));
    }

    /** @dataProvider getDescribeInputDefinitionTestData */
    public function testDescribeInputDefinition(DescriptorInterface $descriptor, InputDefinition $definition, $expectedDescription)
    {
        $this->assertEquals(trim($expectedDescription), trim($descriptor->describeInputDefinition($definition)));
    }

    /** @dataProvider getDescribeCommandTestData */
    public function testDescribeCommand(DescriptorInterface $descriptor, Command $command, $expectedDescription)
    {
        $this->assertEquals(trim($expectedDescription), trim($descriptor->describeCommand($command)));
    }

    /** @dataProvider getDescribeApplicationTestData */
    public function testDescribeApplication(DescriptorInterface $descriptor, Application $application, $expectedDescription)
    {
        // Replaces the dynamic placeholders of the command help text with a static version.
        // The placeholder %command.full_name% includes the script path that is not predictable
        // and can not be tested against.
        foreach ($application->all() as $command) {
            $command->setHelp(str_replace('%command.full_name%', 'app/console %command.name%', $command->getHelp()));
        }

        $this->assertEquals(trim($expectedDescription), trim($descriptor->describeApplication($application)));
    }

    public function getDescribeInputArgumentTestData()
    {
        return $this->getDescriptionTestData(ObjectsProvider::getInputArguments());
    }

    public function getDescribeInputOptionTestData()
    {
        return $this->getDescriptionTestData(ObjectsProvider::getInputOptions());
    }

    public function getDescribeInputDefinitionTestData()
    {
        return $this->getDescriptionTestData(ObjectsProvider::getInputDefinitions());
    }

    public function getDescribeCommandTestData()
    {
        return $this->getDescriptionTestData(ObjectsProvider::getCommands());
    }

    public function getDescribeApplicationTestData()
    {
        return $this->getDescriptionTestData(ObjectsProvider::getApplications());
    }

    abstract protected function getDescriptor();
    abstract protected function getFormat();

    private function getDescriptionTestData(array $objects)
    {
        $data = array();
        foreach ($objects as $name => $object) {
            $description = file_get_contents(sprintf('%s/../Fixtures/%s.%s', __DIR__, $name, $this->getFormat()));
            $data[] = array($this->getDescriptor(), $object, $description);
        }

        return $data;
    }
}
