<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DefaultsConfigurator extends AbstractServiceConfigurator
{
    const FACTORY = 'defaults';

    use Traits\AutoconfigureTrait;
    use Traits\AutowireTrait;
    use Traits\BindTrait;
    use Traits\PublicTrait;

    /**
     * Adds a tag for this definition.
     *
     * @return $this
     *
     * @throws InvalidArgumentException when an invalid tag name or attribute is provided
     */
    final public function tag(string $name, array $attributes = array())
    {
        if (!is_string($name) || '' === $name) {
            throw new InvalidArgumentException('The tag name in "_defaults" must be a non-empty string.');
        }

        foreach ($attributes as $attribute => $value) {
            if (!is_scalar($value) && null !== $value) {
                throw new InvalidArgumentException(sprintf('Tag "%s", attribute "%s" in "_defaults" must be of a scalar-type.', $name, $attribute));
            }
        }

        $this->definition->addTag($name, $attributes);

        return $this;
    }

    /**
     * Defines an instanceof-conditional to be applied to following service definitions.
     *
     * @return InstanceofConfigurator
     */
    final public function instanceof(string $fqcn): InstanceofConfigurator
    {
        return $this->parent->instanceof($fqcn);
    }
}
