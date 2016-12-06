<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authorization;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AccessDecisionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUnsupportedStrategy()
    {
        new AccessDecisionManager(array($this->getVoter(VoterInterface::ACCESS_GRANTED)), 'fooBar');
    }

    /**
     * @dataProvider getStrategyTests
     */
    public function testStrategies($strategy, $voters, $allowIfAllAbstainDecisions, $allowIfEqualGrantedDeniedDecisions, $expected)
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $manager = new AccessDecisionManager($voters, $strategy, $allowIfAllAbstainDecisions, $allowIfEqualGrantedDeniedDecisions);

        $this->assertSame($expected, $manager->decide($token, array('ROLE_FOO')));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\RuntimeException
     */
    public function testInvalidWeight()
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $voter = $this->getWeightedVoter(VoterInterface::ACCESS_GRANTED, 0);
        $manager = new AccessDecisionManager(array($voter), 'consensus');
        $manager->decide($token, array('ROLE_FOO'));
    }

    /**
     * @dataProvider getStrategiesWith2RolesTests
     */
    public function testStrategiesWith2Roles($token, $strategy, $voter, $expected)
    {
        $manager = new AccessDecisionManager(array($voter), $strategy);

        $this->assertSame($expected, $manager->decide($token, array('ROLE_FOO', 'ROLE_BAR')));
    }

    public function getStrategiesWith2RolesTests()
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        return array(
            array($token, 'affirmative', $this->getVoter(VoterInterface::ACCESS_DENIED), false),
            array($token, 'affirmative', $this->getVoter(VoterInterface::ACCESS_GRANTED), true),

            array($token, 'consensus', $this->getVoter(VoterInterface::ACCESS_DENIED), false),
            array($token, 'consensus', $this->getVoter(VoterInterface::ACCESS_GRANTED), true),

            array($token, 'unanimous', $this->getVoterFor2Roles($token, VoterInterface::ACCESS_DENIED, VoterInterface::ACCESS_DENIED), false),
            array($token, 'unanimous', $this->getVoterFor2Roles($token, VoterInterface::ACCESS_DENIED, VoterInterface::ACCESS_GRANTED), false),
            array($token, 'unanimous', $this->getVoterFor2Roles($token, VoterInterface::ACCESS_GRANTED, VoterInterface::ACCESS_DENIED), false),
            array($token, 'unanimous', $this->getVoterFor2Roles($token, VoterInterface::ACCESS_GRANTED, VoterInterface::ACCESS_GRANTED), true),
        );
    }

    protected function getVoterFor2Roles($token, $vote1, $vote2)
    {
        $voter = $this->getMock('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface');
        $voter->expects($this->any())
              ->method('vote')
              ->will($this->returnValueMap(array(
                  array($token, null, array('ROLE_FOO'), $vote1),
                  array($token, null, array('ROLE_BAR'), $vote2),
              )))
        ;

        return $voter;
    }

    public function getStrategyTests()
    {
        return array(
            // affirmative
            array(AccessDecisionManager::STRATEGY_AFFIRMATIVE, $this->getVoters(1, 0, 0), false, true, true),
            array(AccessDecisionManager::STRATEGY_AFFIRMATIVE, $this->getVoters(1, 2, 0), false, true, true),
            array(AccessDecisionManager::STRATEGY_AFFIRMATIVE, $this->getVoters(0, 1, 0), false, true, false),
            array(AccessDecisionManager::STRATEGY_AFFIRMATIVE, $this->getVoters(0, 0, 1), false, true, false),
            array(AccessDecisionManager::STRATEGY_AFFIRMATIVE, $this->getVoters(0, 0, 1), true, true, true),

            // consensus
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(1, 0, 0), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(1, 2, 0), false, true, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(2, 1, 0), false, true, true),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(0, 0, 1), false, true, false),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(0, 0, 1), true, true, true),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(2, 2, 0), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(2, 2, 1), false, true, true),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(2, 2, 0), false, false, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getVoters(2, 2, 1), false, false, false),

            // weighted consensus
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,1), array()), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,1), array(), true), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,1), array(4)), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,1), array(4), true), false, true, true),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,4), array()), false, true, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,4), array(), true), false, true, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,4), array(6)), false, true, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,4), array(6), true), false, true, false),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array()), false, false, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array(), true), false, false, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array(4)), false, false, false),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array(4), true), false, false, false),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array()), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array(), true), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array(4)), false, true, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(3), array(1,2), array(4), true), false, true, true),

            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(), array(), array(1,2,4)), true, false, true),
            array(AccessDecisionManager::STRATEGY_CONSENSUS, $this->getWeightedVoters(array(), array(), array(1,2,4)), false, false, false),

            // unanimous
            array(AccessDecisionManager::STRATEGY_UNANIMOUS, $this->getVoters(1, 0, 0), false, true, true),
            array(AccessDecisionManager::STRATEGY_UNANIMOUS, $this->getVoters(1, 0, 1), false, true, true),
            array(AccessDecisionManager::STRATEGY_UNANIMOUS, $this->getVoters(1, 1, 0), false, true, false),

            array(AccessDecisionManager::STRATEGY_UNANIMOUS, $this->getVoters(0, 0, 2), false, true, false),
            array(AccessDecisionManager::STRATEGY_UNANIMOUS, $this->getVoters(0, 0, 2), true, true, true),
        );
    }

    protected function getVoters($grants, $denies, $abstains)
    {
        $voters = array();
        for ($i = 0; $i < $grants; ++$i) {
            $voters[] = $this->getVoter(VoterInterface::ACCESS_GRANTED);
        }
        for ($i = 0; $i < $denies; ++$i) {
            $voters[] = $this->getVoter(VoterInterface::ACCESS_DENIED);
        }
        for ($i = 0; $i < $abstains; ++$i) {
            $voters[] = $this->getVoter(VoterInterface::ACCESS_ABSTAIN);
        }

        return $voters;
    }

    protected function getWeightedVoters($grantWeights, $denyWeights, $abstainWeights, $mixInDefaultVoters = false)
    {
        $voters = array();
        $grants = count($grantWeights);
        $denies = count($denyWeights);
        $abstains = count($abstainWeights);

        for ($i = 0; $i < $grants; ++$i) {
            $voters[] = $this->getWeightedVoter(VoterInterface::ACCESS_GRANTED, $grantWeights[$i]);
        }
        for ($i = 0; $i < $denies; ++$i) {
            $voters[] = $this->getWeightedVoter(VoterInterface::ACCESS_DENIED, $denyWeights[$i]);
        }
        for ($i = 0; $i < $abstains; ++$i) {
            $voters[] = $this->getWeightedVoter(VoterInterface::ACCESS_ABSTAIN, $abstainWeights[$i]);
        }

        if (true === $mixInDefaultVoters) {
            $voters[] = $this->getVoter(VoterInterface::ACCESS_GRANTED);
            $voters[] = $this->getVoter(VoterInterface::ACCESS_DENIED);
            $voters[] = $this->getVoter(VoterInterface::ACCESS_ABSTAIN);
        }

        return $voters;
    }

    protected function getVoter($vote)
    {
        $voter = $this->getMock('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface');
        $voter->expects($this->any())
              ->method('vote')
              ->will($this->returnValue($vote));

        return $voter;
    }

    protected function getWeightedVoter($vote, $weight)
    {
        $voter = $this->getMock('Symfony\Component\Security\Core\Authorization\Voter\WeightedVoterInterface');
        $voter->expects($this->any())
            ->method('vote')
            ->will($this->returnValue($vote));

        $voter->expects($this->any())
            ->method('getWeight')
            ->will($this->returnValue($weight));

        return $voter;
    }
}
