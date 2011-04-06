<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\Form\Type;

use Symfony\Component\Form\CountryField;
use Symfony\Component\Form\FormContext;

require_once __DIR__.'/TestCase.php';

class CountryTypeTest extends TestCase
{
    public function testCountriesAreSelectable()
    {
        \Locale::setDefault('de_AT');

        $form = $this->factory->create('country');
        $renderer = $this->factory->createRenderer($form, 'stub');
        $choices = $renderer->getVar('choices');

        $this->assertArrayHasKey('DE', $choices);
        $this->assertEquals('Deutschland', $choices['DE']);
        $this->assertArrayHasKey('GB', $choices);
        $this->assertEquals('Vereinigtes Königreich', $choices['GB']);
        $this->assertArrayHasKey('US', $choices);
        $this->assertEquals('Vereinigte Staaten', $choices['US']);
        $this->assertArrayHasKey('FR', $choices);
        $this->assertEquals('Frankreich', $choices['FR']);
        $this->assertArrayHasKey('MY', $choices);
        $this->assertEquals('Malaysia', $choices['MY']);
    }

    public function testUnknownCountryIsNotIncluded()
    {
        $form = $this->factory->create('country', 'country');
        $renderer = $this->factory->createRenderer($form, 'stub');
        $choices = $renderer->getVar('choices');

        $this->assertArrayNotHasKey('ZZ', $choices);
    }
}
