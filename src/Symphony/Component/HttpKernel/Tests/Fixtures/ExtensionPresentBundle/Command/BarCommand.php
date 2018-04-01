<?php

namespace Symphony\Component\HttpKernel\Tests\Fixtures\ExtensionPresentBundle\Command;

use Symphony\Component\Console\Command\Command;

/**
 * This command has a required parameter on the constructor and will be ignored by the default Bundle implementation.
 *
 * @see Bundle::registerCommands()
 */
class BarCommand extends Command
{
    public function __construct($example, $name = 'bar')
    {
    }
}
