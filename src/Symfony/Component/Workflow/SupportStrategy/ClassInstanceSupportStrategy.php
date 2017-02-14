<?php

namespace Symfony\Component\Workflow\SupportStrategy;

use Symfony\Component\Workflow\Workflow;

class ClassInstanceSupportStrategy implements SupportStrategyInterface
{
    private $className;

    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Workflow $workflow, $subject)
    {
        return $subject instanceof $this->className;
    }
}
