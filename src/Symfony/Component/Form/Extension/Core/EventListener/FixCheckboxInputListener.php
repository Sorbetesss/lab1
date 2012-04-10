<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\Core\EventListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\FilterDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

/**
 * Takes care of converting the input from a list of checkboxes to a correctly
 * indexed array.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class FixCheckboxInputListener implements EventSubscriberInterface
{
    private $choiceList;

    /**
     * Constructor.
     *
     * @param ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    public function onBindClientData(FilterDataEvent $event)
    {
        $values = (array) $event->getData();
        $indices = $this->choiceList->getIndicesForValues($values);

        $event->setData(array_combine($indices, $values));
    }

    static public function getSubscribedEvents()
    {
        return array(FormEvents::BIND_CLIENT_DATA => 'onBindClientData');
    }
}
