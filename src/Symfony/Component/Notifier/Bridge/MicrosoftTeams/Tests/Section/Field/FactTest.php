<?php

namespace Symfony\Component\Notifier\Bridge\MicrosoftTeams\Tests\Section\Field;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Bridge\MicrosoftTeams\Section\Field\Fact;

final class FactTest extends TestCase
{
    public function testName()
    {
        $field = (new Fact())
            ->name($value = 'Current version');

        $this->assertSame($value, $field->toArray()['name']);
    }

    public function testTitle()
    {
        $field = (new Fact())
            ->value($value = '5.3');

        $this->assertSame($value, $field->toArray()['value']);
    }
}
