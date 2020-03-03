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

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Voter is an abstract default implementation of a voter.
 *
 * @author Roman Marintšenko <inoryy@gmail.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
abstract class Voter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = $this->abstain();

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = $this->deny();

            $v = \is_bool($v = $this->voteOnAttribute($attribute, $subject, $token)) ? Vote::create($v) : $v; // BC layer
            if ($v->isGranted()) {
                // grant access as soon as at least one attribute returns a positive response
                return $v;
            } else {
                $vote->merge($v);
            }
        }

        return $vote;
    }

    /**
     * Creates a granted vote.
     */
    public function grant(string $reason = '', array $parameters = []): Vote
    {
        return Vote::createGranted($reason, $parameters);
    }

    /**
     * Creates an abstained vote.
     */
    public function abstain(string $reason = '', array $parameters = []): Vote
    {
        return Vote::createAbstrain($reason, $parameters);
    }

    /**
     * Creates an denied vote.
     */
    public function deny(string $reason = '', array $parameters = []): Vote
    {
        return Vote::createDenied($reason, $parameters);
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    abstract protected function supports(string $attribute, $subject);

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param mixed $subject
     *
     * @return bool|Vote
     */
    abstract protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token);
}
