<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\FeatureToggle\Provider;

use Symfony\Component\FeatureToggle\Feature;

interface ProviderInterface
{
    public function get(string $featureName): ?Feature;

    /**
     * @return list<string>
     */
    public function names(): array;
}
