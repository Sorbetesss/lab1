<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Form\Console\Descriptor;

use Symphony\Component\Form\ResolvedFormTypeInterface;
use Symphony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 *
 * @internal
 */
class JsonDescriptor extends Descriptor
{
    protected function describeDefaults(array $options)
    {
        $data['builtin_form_types'] = $options['core_types'];
        $data['service_form_types'] = $options['service_types'];
        $data['type_extensions'] = $options['extensions'];
        $data['type_guessers'] = $options['guessers'];

        $this->writeData($data, $options);
    }

    protected function describeResolvedFormType(ResolvedFormTypeInterface $resolvedFormType, array $options = array())
    {
        $this->collectOptions($resolvedFormType);

        $formOptions = array(
            'own' => $this->ownOptions,
            'overridden' => $this->overriddenOptions,
            'parent' => $this->parentOptions,
            'extension' => $this->extensionOptions,
            'required' => $this->requiredOptions,
        );
        $this->sortOptions($formOptions);

        $data = array(
            'class' => get_class($resolvedFormType->getInnerType()),
            'block_prefix' => $resolvedFormType->getInnerType()->getBlockPrefix(),
            'options' => $formOptions,
            'parent_types' => $this->parents,
            'type_extensions' => $this->extensions,
        );

        $this->writeData($data, $options);
    }

    protected function describeOption(OptionsResolver $optionsResolver, array $options)
    {
        $definition = $this->getOptionDefinition($optionsResolver, $options['option']);

        $map = array(
            'required' => 'required',
            'default' => 'default',
            'allowed_types' => 'allowedTypes',
            'allowed_values' => 'allowedValues',
        );
        foreach ($map as $label => $name) {
            if (array_key_exists($name, $definition)) {
                $data[$label] = $definition[$name];

                if ('default' === $name) {
                    $data['is_lazy'] = isset($definition['lazy']);
                }
            }
        }
        $data['has_normalizer'] = isset($definition['normalizer']);

        $this->writeData($data, $options);
    }

    private function writeData(array $data, array $options)
    {
        $flags = isset($options['json_encoding']) ? $options['json_encoding'] : 0;
        $this->output->write(json_encode($data, $flags | JSON_PRETTY_PRINT)."\n");
    }

    private function sortOptions(array &$options)
    {
        foreach ($options as &$opts) {
            $sorted = false;
            foreach ($opts as &$opt) {
                if (is_array($opt)) {
                    sort($opt);
                    $sorted = true;
                }
            }
            if (!$sorted) {
                sort($opts);
            }
        }
    }
}
