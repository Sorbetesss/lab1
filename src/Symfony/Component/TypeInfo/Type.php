<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\TypeInfo;

use Symfony\Component\TypeInfo\Type\CompositeTypeInterface;
use Symfony\Component\TypeInfo\Type\WrappingTypeInterface;

/**
 * @author Mathias Arlaud <mathias.arlaud@gmail.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 *
 * @experimental
 */
abstract class Type implements \Stringable
{
    use TypeFactoryTrait;

    /**
     * @param callable(self): bool $specification
     */
    public function satisfy(callable $specification): bool
    {
        return $specification($this);
    }

    /**
     * Tells if the type (or one of its wrapped/composed parts) is identified by one of the $identifiers.
     */
    public function isIdentifiedBy(TypeIdentifier|string ...$identifiers): bool
    {
        $specification = static function (Type $type) use (&$specification, $identifiers): bool {
            if ($type instanceof WrappingTypeInterface) {
                return $type->wrappedTypeSatisfy($specification);
            }

            if ($type instanceof CompositeTypeInterface) {
                return $type->composedTypesSatisfy($specification);
            }

            return $type->isIdentifiedBy(...$identifiers);
        };

        return $this->satisfy($specification);
    }

    public function isNullable(): bool
    {
        return false;
    }
}
