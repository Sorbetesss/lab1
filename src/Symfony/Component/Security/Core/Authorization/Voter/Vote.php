<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authorization\Voter;

final class Vote
{
    use AccessTrait;

    private $reason;
    private $parameters;

    private function __construct(int $access, string $reason = '', array $parameters = [])
    {
        $this->access = $access;
        $this->reason = $reason;
        $this->parameters = $parameters;
    }

    public static function create(int $access, string $reason = '', array $parameters = []): self
    {
        return new self($access, $reason, $parameters);
    }

    public static function createGranted(string $reason, array $parameters = []): self
    {
        return new self(VoterInterface::ACCESS_GRANTED, $reason, $parameters);
    }

    public static function createAbstrain(string $reason, array $parameters = []): self
    {
        return new self(VoterInterface::ACCESS_ABSTAIN, $reason, $parameters);
    }

    public static function createDenied(string $reason, array $parameters = []): self
    {
        return new self(VoterInterface::ACCESS_DENIED, $reason, $parameters);
    }

    public function merge(self $vote): void
    {
        $this->reason .= trim(' '.$vote->getReason());
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
