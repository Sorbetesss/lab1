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

use Symfony\Component\Form\ChoiceList\ArrayKeyChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface as LegacyChoiceListInterface;

/**
 * Default implementation of {@link ChoiceListFactoryInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DefaultChoiceListFactory implements ChoiceListFactoryInterface
{
    /**
     * Flattens an array into the given output variable.
     *
     * @param array $array  The array to flatten
     * @param array $output The flattened output
     *
     * @internal Should not be used by user-land code
     */
    public static function flatten(array $array, &$output)
    {
        if (null === $output) {
            $output = array();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::flatten($value, $output);
                continue;
            }

            $output[$key] = $value;
        }
    }

    /**
     * Flattens and flips an array into the given output variable.
     *
     * During the flattening, the keys and values of the input array are
     * flipped.
     *
     * @param array $array  The array to flatten
     * @param array $output The flattened output
     *
     * @internal Should not be used by user-land code
     */
    public static function flattenFlipped(array $array, &$output)
    {
        if (null === $output) {
            $output = array();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::flattenFlipped($value, $output);
                continue;
            }

            $output[$value] = $key;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createListFromChoices($choices, $value = null)
    {
        if (!is_array($choices) && !$choices instanceof \Traversable) {
            throw new UnexpectedTypeException($choices, 'array or \Traversable');
        }

        if (null !== $value && !is_callable($value)) {
            throw new UnexpectedTypeException($value, 'null or callable');
        }

        if ($choices instanceof \Traversable) {
            $choices = iterator_to_array($choices);
        }

        // If the choices are given as recursive array (i.e. with explicit
        // choice groups), flatten the array. The grouping information is needed
        // in the view only.
        self::flatten($choices, $flatChoices);

        // If no values are given, use incrementing integers as values
        // We can not use the choices themselves, because we don't know whether
        // choices can be converted to (duplicate-free) strings
        if (null === $value) {
            $values = $flatChoices;
            $i = 0;

            foreach ($values as $key => $value) {
                $values[$key] = (string) $i++;
            }

            return new ChoiceList($flatChoices, $values);
        }

        // Can't use array_map(), because array_map() doesn't pass the key
        // Can't use array_walk(), which ignores the return value of the
        // closure
        $values = array();
        foreach ($flatChoices as $key => $choice) {
            $values[$key] = call_user_func($value, $choice, $key);
        }

        return new ChoiceList($flatChoices, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function createListFromFlippedChoices($choices, $value = null)
    {
        if (!is_array($choices) && !$choices instanceof \Traversable) {
            throw new UnexpectedTypeException($choices, 'array or \Traversable');
        }

        if (null !== $value && !is_callable($value)) {
            throw new UnexpectedTypeException($value, 'null or callable');
        }

        if ($choices instanceof \Traversable) {
            $choices = iterator_to_array($choices);
        }

        // If the choices are given as recursive array (i.e. with explicit
        // choice groups), flatten the array. The grouping information is needed
        // in the view only.
        self::flattenFlipped($choices, $flatChoices);

        // If no values are given, use the choices as values
        // Since the choices are stored in the collection keys, i.e. they are
        // strings or integers, we are guaranteed to be able to convert them
        // to strings
        if (null === $value) {
            $values = array_map('strval', $flatChoices);

            return new ArrayKeyChoiceList($flatChoices, $values);
        }

        // Can't use array_map(), because array_map() doesn't pass the key
        // Can't use array_walk(), which ignores the return value of the
        // closure
        $values = array();
        foreach ($flatChoices as $key => $choice) {
            $values[$key] = call_user_func($value, $choice, $key);
        }

        return new ArrayKeyChoiceList($flatChoices, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function createListFromLoader(ChoiceLoaderInterface $loader, $value = null)
    {
        if (null !== $value && !is_callable($value)) {
            throw new UnexpectedTypeException($value, 'null or callable');
        }

        return new LazyChoiceList($loader, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function createView(ChoiceListInterface $list, $preferredChoices = null, $label = null, $index = null, $groupBy = null, $attr = null)
    {
        if (null !== $preferredChoices && !is_array($preferredChoices) && !is_callable($preferredChoices)) {
            throw new UnexpectedTypeException($preferredChoices, 'null, array or callable');
        }

        if (null !== $label && !is_callable($label)) {
            throw new UnexpectedTypeException($label, 'null or callable');
        }

        if (null !== $index && !is_callable($index)) {
            throw new UnexpectedTypeException($index, 'null or callable');
        }

        if (null !== $groupBy && !is_array($groupBy) && !$groupBy instanceof \Traversable && !is_callable($groupBy)) {
            throw new UnexpectedTypeException($groupBy, 'null, array, \Traversable or callable');
        }

        if (null !== $attr && !is_array($attr) && !is_callable($attr)) {
            throw new UnexpectedTypeException($attr, 'null, array or callable');
        }

        // Backwards compatibility
        if ($list instanceof LegacyChoiceListInterface && null === $preferredChoices
            && null === $label && null === $index && null === $groupBy && null === $attr) {
            return new ChoiceListView($list->getRemainingViews(), $list->getPreferredViews());
        }

        $preferredViews = array();
        $otherViews = array();
        $choices = $list->getChoices();
        $values = $list->getValues();

        // If the labels are null, use the choice key by default
        if (null === $label) {
            $label = function ($choice, $key) {
                return $key;
            };
        }

        if (!is_callable($preferredChoices) && !empty($preferredChoices)) {
            $preferredChoices = function ($choice) use ($preferredChoices) {
                return false !== array_search($choice, $preferredChoices, true);
            };
        }

        // The names are generated from an incrementing integer by default
        if (null === $index) {
            $i = 0;
            $index = function () use (&$i) {
                return $i++;
            };
        }

        // If $groupBy is not given, no grouping is done
        if (empty($groupBy)) {
            foreach ($choices as $key => $choice) {
                self::addChoiceView(
                    $choice,
                    $key,
                    $label,
                    $values,
                    $index,
                    $attr,
                    $preferredChoices,
                    $preferredViews,
                    $otherViews
                );
            }

            return new ChoiceListView($otherViews, $preferredViews);
        }

        // If $groupBy is a callable, choices are added to the group with the
        // name returned by the callable. If the callable returns null, the
        // choice is not added to any group
        if (is_callable($groupBy)) {
            foreach ($choices as $key => $choice) {
                self::addChoiceViewGroupedBy(
                    $groupBy,
                    $choice,
                    $key,
                    $label,
                    $values,
                    $index,
                    $attr,
                    $preferredChoices,
                    $preferredViews,
                    $otherViews
                );
            }
        } else {
            // If $groupBy is passed as array, use that array as template for
            // constructing the groups
            self::addChoiceViewsGroupedBy(
                $groupBy,
                $label,
                $choices,
                $values,
                $index,
                $attr,
                $preferredChoices,
                $preferredViews,
                $otherViews
            );
        }

        // Remove any empty group views that may have been created by
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

    private static function addChoiceView($choice, $key, $label, $values, $index, $attr, $isPreferred, &$preferredViews, &$otherViews)
    {
        $view = new ChoiceView(
            (string) call_user_func($label, $choice, $key),
            $values[$key],
            $choice,
            // The attributes may be a callable or a mapping from choice indices
            // to nested arrays
            is_callable($attr) ? call_user_func($attr, $choice, $key) : (isset($attr[$key]) ? $attr[$key] : array())
        );

        // $isPreferred may be null if no choices are preferred
        if ($isPreferred && call_user_func($isPreferred, $choice, $key)) {
            $preferredViews[call_user_func($index, $choice, $key)] = $view;
        } else {
            $otherViews[call_user_func($index, $choice, $key)] = $view;
        }
    }

    private static function addChoiceViewsGroupedBy($groupBy, $label, $choices, $values, $index, $attr, $isPreferred, &$preferredViews, &$otherViews)
    {
        foreach ($groupBy as $key => $content) {
            // Add the contents of groups to new ChoiceGroupView instances
            if (is_array($content)) {
                $preferredViewsForGroup = array();
                $otherViewsForGroup = array();

                self::addChoiceViewsGroupedBy(
                    $content,
                    $label,
                    $choices,
                    $values,
                    $index,
                    $attr,
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
                $choices[$key],
                $key,
                $label,
                $values,
                $index,
                $attr,
                $isPreferred,
                $preferredViews,
                $otherViews
            );
        }
    }

    private static function addChoiceViewGroupedBy($groupBy, $choice, $key, $label, $values, $index, $attr, $isPreferred, &$preferredViews, &$otherViews)
    {
        $groupLabel = call_user_func($groupBy, $choice, $key);

        if (null === $groupLabel) {
            // If the callable returns null, don't group the choice
            self::addChoiceView(
                $choice,
                $key,
                $label,
                $values,
                $index,
                $attr,
                $isPreferred,
                $preferredViews,
                $otherViews
            );

            return;
        }

        // Initialize the group views if necessary. Unnnecessarily built group
        // views will be cleaned up at the end of createView()
        if (!isset($preferredViews[$groupLabel])) {
            $preferredViews[$groupLabel] = new ChoiceGroupView($groupLabel);
            $otherViews[$groupLabel] = new ChoiceGroupView($groupLabel);
        }

        self::addChoiceView(
            $choice,
            $key,
            $label,
            $values,
            $index,
            $attr,
            $isPreferred,
            $preferredViews[$groupLabel]->choices,
            $otherViews[$groupLabel]->choices
        );
    }
}
