<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests\Extension\Core\Type;

use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Intl\Util\IntlTestHelper;

class CurrencyTypeTest extends BaseTypeTest
{
    const TESTED_TYPE = 'Symfony\Component\Form\Extension\Core\Type\CurrencyType';

    /**
     * @before
     */
    protected function before()
    {
        IntlTestHelper::requireIntl($this, false);

    }

    public function testCurrenciesAreSelectable()
    {
        $choices = $this->factory->create(static::TESTED_TYPE)
            ->createView()->vars['choices'];

        $this->assertContains(new ChoiceView('EUR', 'EUR', 'Euro'), $choices, '', false, false);
        $this->assertContains(new ChoiceView('USD', 'USD', 'US Dollar'), $choices, '', false, false);
        $this->assertContains(new ChoiceView('SIT', 'SIT', 'Slovenian Tolar'), $choices, '', false, false);
    }

    public function testSubmitNull($expected = null, $norm = null, $view = null)
    {
        parent::testSubmitNull($expected, $norm, '');
    }

    public function testSubmitNullUsesDefaultEmptyData($emptyData = 'EUR', $expectedData = 'EUR')
    {
        parent::testSubmitNullUsesDefaultEmptyData($emptyData, $expectedData);
    }

    public function testInvalidChoiceValuesAreDropped()
    {
        $type = new CurrencyType();

        $this->assertSame([], $type->loadChoicesForValues(['foo']));
    }
}
