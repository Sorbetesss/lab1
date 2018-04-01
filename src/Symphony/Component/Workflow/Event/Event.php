<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Workflow\Event;

use Symphony\Component\EventDispatcher\Event as BaseEvent;
use Symphony\Component\Workflow\Exception\InvalidArgumentException;
use Symphony\Component\Workflow\Marking;
use Symphony\Component\Workflow\Transition;
use Symphony\Component\Workflow\WorkflowInterface;

/**
 * @author Fabien Potencier <fabien@symphony.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Event extends BaseEvent
{
    private $subject;
    private $marking;
    private $transition;
    private $workflow;
    private $workflowName;

    /**
     * @param object     $subject
     * @param Marking    $marking
     * @param Transition $transition
     * @param Workflow   $workflow
     */
    public function __construct($subject, Marking $marking, Transition $transition, $workflow = null)
    {
        $this->subject = $subject;
        $this->marking = $marking;
        $this->transition = $transition;
        if (is_string($workflow)) {
            @trigger_error(sprintf('Passing a string as 4th parameter of "%s" is deprecated since Symphony 4.1. Pass a %s instance instead.', __METHOD__, WorkflowInterface::class), E_USER_DEPRECATED);
            $this->workflowName = $workflow;
        } elseif ($workflow instanceof WorkflowInterface) {
            $this->workflow = $workflow;
        } else {
            throw new InvalidArgumentException(sprintf('The 4th parameter of "%s"  should be a "%s" instance instead.', __METHOD__, WorkflowInterface::class));
        }
    }

    public function getMarking()
    {
        return $this->marking;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getTransition()
    {
        return $this->transition;
    }

    public function getWorkflow(): WorkflowInterface
    {
        // BC layer
        if (!$this->workflow instanceof WorkflowInterface) {
            throw new \RuntimeException(sprintf('The 4th parameter of "%s"::__construct() should be a "%s" instance.', __CLASS__, WorkflowInterface::class));
        }

        return $this->workflow;
    }

    public function getWorkflowName()
    {
        // BC layer
        if ($this->workflowName) {
            return $this->workflowName;
        }

        // BC layer
        if (!$this->workflow instanceof WorkflowInterface) {
            throw new \RuntimeException(sprintf('The 4th parameter of "%s"::__construct() should be a "%s" instance.', __CLASS__, WorkflowInterface::class));
        }

        return $this->workflow->getName();
    }

    public function getMetadata(string $key, $subject)
    {
        // BC layer
        if (!$this->workflow instanceof WorkflowInterface) {
            throw new \RuntimeException(sprintf('The 4th parameter of "%s"::__construct() should be a "%s" instance.', __CLASS__, WorkflowInterface::class));
        }

        return $this->workflow->getMetadataStore()->getMetadata($key, $subject);
    }
}
