<?php

namespace Symfony\Component\Console\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\DumpCompletionCommand;
use Symfony\Component\Console\Tester\CommandCompletionTester;

class DumpCompletionCommandTest extends TestCase
{
    /**
     * @dataProvider provideCompletionSuggestions
     */
    public function testComplete(array $input, array $expectedSuggestions)
    {
        if (!class_exists(CommandCompletionTester::class)) {
            $this->markTestSkipped('Test command completion requires symfony/console 5.4+.');
        }

        $tester = new CommandCompletionTester(new DumpCompletionCommand());
        $suggestions = $tester->complete($input);

        $this->assertSame($expectedSuggestions, $suggestions);
    }

    public function provideCompletionSuggestions()
    {
        yield 'shell' => [
            [''],
            ['bash'],
        ];
    }
}
