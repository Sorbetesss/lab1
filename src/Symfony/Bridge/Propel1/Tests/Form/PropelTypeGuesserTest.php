<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Propel1\Tests\Form;

use Symfony\Bridge\Propel1\Form\PropelTypeGuesser;
use Symfony\Bridge\Propel1\Tests\Propel1TestCase;
use Symfony\Component\Form\Guess\Guess;

class PropelTypeGuesserTest extends Propel1TestCase
{
    const CLASS_NAME = 'Symfony\Bridge\Propel1\Tests\Fixtures\Item';

    const UNKNOWN_CLASS_NAME = 'Symfony\Bridge\Propel1\Tests\Fixtures\UnknownItem';

    private $guesser;

    public function setUp()
    {
        $this->guesser = new PropelTypeGuesser();
    }

    public function testGuessMaxLengthWithText()
    {
        $attributes = $this->guesser->guessAttributes(self::CLASS_NAME, 'value');

        $this->assertArrayHasKey('maxlength', $attributes);
        $this->assertEquals(255, $attributes['maxlength']->getValue());
    }

    public function testGuessMaxLengthWithFloat()
    {
        $attributes = $this->guesser->guessAttributes(self::CLASS_NAME, 'price');

        $this->assertArrayHasKey('maxlength', $attributes);
        $this->assertEquals(null, $attributes['maxlength']->getValue());
    }

    public function testGuessMinLengthWithText()
    {
        $attributes = $this->guesser->guessAttributes(self::CLASS_NAME, 'price');

        $this->assertArrayHasKey('maxlength', $attributes);
        $this->assertEquals(null, $attributes['maxlength']->getValue());
    }

    public function testGuessMinLengthWithFloat()
    {
        $attributes = $this->guesser->guessAttributes(self::CLASS_NAME, 'price');

        $this->assertArrayHasKey('maxlength', $attributes);
        $this->assertEquals(null, $attributes['maxlength']->getValue());
    }

    public function testGuessRequired()
    {
        $attributes = $this->guesser->guessAttributes(self::CLASS_NAME, 'id');

        $this->assertArrayHasKey('required', $attributes);
        $this->assertEquals(true, $attributes['required']->getValue());
    }

    public function testGuessRequiredWithNullableColumn()
    {
        $attributes = $this->guesser->guessAttributes(self::CLASS_NAME, 'value');

        $this->assertArrayHasKey('required', $attributes);
        $this->assertEquals(false, $attributes['required']->getValue());
    }

    public function testGuessTypeWithoutTable()
    {
        $value = $this->guesser->guessType(self::UNKNOWN_CLASS_NAME, 'property');

        $this->assertNotNull($value);
        $this->assertEquals('text', $value->getType());
        $this->assertEquals(Guess::LOW_CONFIDENCE, $value->getConfidence());
    }

    public function testGuessTypeWithoutColumn()
    {
        $value = $this->guesser->guessType(self::CLASS_NAME, 'property');

        $this->assertNotNull($value);
        $this->assertEquals('text', $value->getType());
        $this->assertEquals(Guess::LOW_CONFIDENCE, $value->getConfidence());
    }

    /**
     * @dataProvider dataProviderForGuessType
     */
    public function testGuessType($property, $type, $confidence, $multiple = null)
    {
        $value = $this->guesser->guessType(self::CLASS_NAME, $property);

        $this->assertNotNull($value);
        $this->assertEquals($type, $value->getType());
        $this->assertEquals($confidence, $value->getConfidence());

        if ($type === 'model') {
            $options = $value->getOptions();

            $this->assertSame($multiple, $options['multiple']);
        }
    }

    public static function dataProviderForGuessType()
    {
        return array(
            array('is_active',  'checkbox', Guess::HIGH_CONFIDENCE),
            array('enabled',    'checkbox', Guess::HIGH_CONFIDENCE),
            array('id',         'integer',  Guess::MEDIUM_CONFIDENCE),
            array('value',      'text',     Guess::MEDIUM_CONFIDENCE),
            array('price',      'number',   Guess::MEDIUM_CONFIDENCE),
            array('updated_at', 'datetime', Guess::HIGH_CONFIDENCE),

            array('isActive',   'checkbox', Guess::HIGH_CONFIDENCE),
            array('updatedAt',  'datetime', Guess::HIGH_CONFIDENCE),

            array('Authors',    'model',    Guess::HIGH_CONFIDENCE,     true),
            array('Resellers',  'model',    Guess::HIGH_CONFIDENCE,     true),
            array('MainAuthor', 'model',    Guess::HIGH_CONFIDENCE,     false),
        );
    }
}
