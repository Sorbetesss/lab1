<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Attribute;

use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Attribute to tell a parameter how to be autowired.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Autowire
{
    use Traits\ValueTrait;

    public readonly string|array|Expression|Reference $value;

    /**
     * Use only ONE of the following.
     *
     * @param string|array|null $value      Parameter value (ie "%kernel.project_dir%/some/path")
     * @param string|null       $service    Service ID (ie "some.service")
     * @param string|null       $expression Expression (ie 'service("some.service").someMethod()')
     * @param string|null       $env        Environment variable name (ie 'SOME_ENV_VARIABLE')
     * @param string|null       $param      Parameter name (ie 'some.parameter.name')
     */
    public function __construct(
        string|array $value = null,
        string $service = null,
        string $expression = null,
        string $env = null,
        string $param = null,
    ) {
        $this->value = $this->normalizeValue($value, $service, $expression, $env, $param);
    }
}
