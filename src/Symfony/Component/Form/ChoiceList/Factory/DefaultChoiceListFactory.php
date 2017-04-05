<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\ChoiceList\Factory;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

/**
 * Default implementation of {@link ChoiceListFactoryInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DefaultChoiceListFactory implements ChoiceListFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListFromChoices($choices, $value = null)
    {
        return new ArrayChoiceList($choices, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function createListFromLoader(ChoiceLoaderInterface $loader, $value = null)
    {
        return new LazyChoiceList($loader, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function createView(ChoiceListInterface $list, $preferredChoices = null, $label = null, $index = null, $groupBy = null, $attr = null, $labelAttr = null)
    {
        $preferredViews = array();
        $otherViews = array();
        $choices = $list->getChoices();
        $keys = $list->getOriginalKeys();

        if (!is_callable($preferredChoices) && !empty($preferredChoices)) {
            $preferredChoices = function ($choice) use ($preferredChoices) {
                return false !== array_search($choice, $preferredChoices, true);
            };
        }

        // The names are generated from an incrementing integer by default
        if (null === $index) {
            $index = 0;
        }

        // If $groupBy is a callable, choices are added to the group with the
        // name returned by the callable. If the callable returns null, the
        // choice is not added to any group
        if (is_callable($groupBy)) {
            foreach ($choices as $value => $choice) {
                self::addChoiceViewGroupedBy(
                    $groupBy,
                    $choice,
                    (string) $value,
                    $label,
                    $keys,
                    $index,
                    $attr,
                    $labelAttr,
                    $preferredChoices,
                    $preferredViews,
                    $otherViews
                );
            }
        } else {
            // Otherwise use the original structure of the choices
            self::addChoiceViewsGroupedBy(
                $list->getStructuredValues(),
                $label,
                $choices,
                $keys,
                $index,
                $attr,
                $labelAttr,
                $preferredChoices,
                $preferredViews,
                $otherViews
            );
        }

        // Remove any empty group view that may have been created by
        // addChoiceViewGroupedBy()
        foreach ($preferredViews as $key => $view) {
            if ($view instanceof ChoiceGroupView && 0 === count($view->choices)) {
                unset($preferredViews[$key]);
            }
        }

        foreach ($otherViews as $key => $view) {
            if ($view instanceof ChoiceGroupView && 0 === count($view->choices)) {
                unset($otherViews[$key]);
            }
        }

        return new ChoiceListView($otherViews, $preferredViews);
    }

    private static function addChoiceView($choice, $value, $label, $keys, &$index, $attr, $labelAttr, $isPreferred, &$preferredViews, &$otherViews)
    {
        // $value may be an integer or a string, since it's stored in the array
        // keys. We want to guarantee it's a string though.
        $key = $keys[$value];
        $nextIndex = is_int($index) ? $index++ : call_user_func($index, $choice, $key, $value);

        // BC normalize label to accept a false value
        if (null === $label) {
            // If the labels are null, use the original choice key by default
            $label = (string) $key;
        } elseif (false !== $label) {
            // If "choice_label" is set to false and "expanded" is true, the value false
            // should be passed on to the "label" option of the checkboxes/radio buttons
            $dynamicLabel = call_user_func($label, $choice, $key, $value);
            $label = false === $dynamicLabel ? false : (string) $dynamicLabel;
        }

        // BC
        if (is_array($attr)) {
            if (isset($attr[$key])) {
                @trigger_error('Passing an array of arrays to the "choice_attr" option with choice keys as keys is deprecated since version 3.3 and will no longer be supported in 4.0. Use a "\Closure" instead.', E_USER_DEPRECATED);
                $attr = $attr[$key];
            } else {
                foreach ($attr as $a) {
                    if (is_array($a)) {
                        // Using the deprecated way of choice keys as keys allows to not define all choices.
                        // When $attr[$key] is not set for this one but is for another we need to
                        // prevent using an array as HTML attribute
                        $attr = array();

                        break;
                    }
                }
            }
        }

        $view = new ChoiceView(
            $choice,
            $value,
            $label,
            // The attributes may be a callable or an array
            is_callable($attr) ? call_user_func($attr, $choice, $key, $value) : (null !== $attr ? $attr : array()),
            is_callable($labelAttr) ? call_user_func($labelAttr, $choice, $key, $value) : (null !== $labelAttr ? $labelAttr : array())
        );

        // $isPreferred may be null if no choices are preferred
        if ($isPreferred && call_user_func($isPreferred, $choice, $key, $value)) {
            $preferredViews[$nextIndex] = $view;
        } else {
            $otherViews[$nextIndex] = $view;
        }
    }

    private static function addChoiceViewsGroupedBy($groupBy, $label, $choices, $keys, &$index, $attr, $labelAttr, $isPreferred, &$preferredViews, &$otherViews)
    {
        foreach ($groupBy as $key => $value) {
            if (null === $value) {
                continue;
            }

            // Add the contents of groups to new ChoiceGroupView instances
            if (is_array($value)) {
                $preferredViewsForGroup = array();
                $otherViewsForGroup = array();

                self::addChoiceViewsGroupedBy(
                    $value,
                    $label,
                    $choices,
                    $keys,
                    $index,
                    $attr,
                    $labelAttr,
                    $isPreferred,
                    $preferredViewsForGroup,
                    $otherViewsForGroup
                );

                if (count($preferredViewsForGroup) > 0) {
                    $preferredViews[$key] = new ChoiceGroupView($key, $preferredViewsForGroup);
                }

                if (count($otherViewsForGroup) > 0) {
                    $otherViews[$key] = new ChoiceGroupView($key, $otherViewsForGroup);
                }

                continue;
            }

            // Add ungrouped items directly
            self::addChoiceView(
                $choices[$value],
                $value,
                $label,
                $keys,
                $index,
                $attr,
                $labelAttr,
                $isPreferred,
                $preferredViews,
                $otherViews
            );
        }
    }

    private static function addChoiceViewGroupedBy($groupBy, $choice, $value, $label, $keys, &$index, $attr, $labelAttr, $isPreferred, &$preferredViews, &$otherViews)
    {
        $groupLabel = call_user_func($groupBy, $choice, $keys[$value], $value);

        if (null === $groupLabel) {
            // If the callable returns null, don't group the choice
            self::addChoiceView(
                $choice,
                $value,
                $label,
                $keys,
                $index,
                $attr,
                $labelAttr,
                $isPreferred,
                $preferredViews,
                $otherViews
            );

            return;
        }

        $groupLabel = (string) $groupLabel;

        // Initialize the group views if necessary. Unnecessarily built group
        // views will be cleaned up at the end of createView()
        if (!isset($preferredViews[$groupLabel])) {
            $preferredViews[$groupLabel] = new ChoiceGroupView($groupLabel);
            $otherViews[$groupLabel] = new ChoiceGroupView($groupLabel);
        }

        self::addChoiceView(
            $choice,
            $value,
            $label,
            $keys,
            $index,
            $attr,
            $labelAttr,
            $isPreferred,
            $preferredViews[$groupLabel]->choices,
            $otherViews[$groupLabel]->choices
        );
    }
}
