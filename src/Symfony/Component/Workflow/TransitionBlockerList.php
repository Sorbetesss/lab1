<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Workflow;

/**
 * A list of transition blockers.
 */
class TransitionBlockerList implements \IteratorAggregate, \Countable
{
    /** @var TransitionBlocker[] */
    private $blockers = array();

    public function __construct(array $blockers = array())
    {
        foreach ($blockers as $blocker) {
            $this->add($blocker);
        }
    }

    public function add(TransitionBlocker $blocker): void
    {
        $this->blockers[] = $blocker;
    }

    public function get(int $offset): TransitionBlocker
    {
        if (!isset($this->blockers[$offset])) {
            throw new \OutOfBoundsException(sprintf('The offset "%s" does not exist.', $offset));
        }

        return $this->blockers[$offset];
    }

    public function has(int $offset): bool
    {
        return isset($this->blockers[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @return \ArrayIterator|TransitionBlocker[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->blockers);
    }

    public function count(): int
    {
        return count($this->blockers);
    }

    public function findByCode(string $code): ?TransitionBlocker
    {
        foreach ($this as $transitionBlocker) {
            if ($transitionBlocker->getCode() === $code) {
                return $transitionBlocker;
            }
        }

        return null;
    }
}
