UPGRADE FROM 3.x to 4.0
=======================

DependencyInjection
-------------------

 * Using unsupported configuration keys in YAML configuration files raises an
   exception.

 * Using unsupported options to configure service aliases raises an exception.

Form
----

 * The `choices_as_values` option of the `ChoiceType` has been removed.

 * The `expanded` option of the `ChoiceType` has been removed.
   Use instead the `widget` option with `select`, `checkbox`, `radio`, `text` or `hidden`.

   Before:

   ```php
   use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

   $form = $this->createFormBuilder()
      ->add('isAttending', ChoiceType::class, array(
         'choices'  => array(
            'Yes' => true,
            'No' => false,
         ),
         'expanded' => true,
         'multiple' => false,
      ))
      ->getForm();
   ```

   After:

   ```php
   use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

   $form = $this->createFormBuilder()
      ->add('isAttending', ChoiceType::class, array(
         'choices'  => array(
            'Yes' => true,
            'No' => false,
         ),
         'widget' => 'radio',
      ))
      ->getForm();
   ```

Serializer
----------

 * The ability to pass a Doctrine `Cache` instance to the `ClassMetadataFactory`
   class has been removed. You should use the `CacheClassMetadataFactory` class
   instead.

Yaml
----

 * The `!!php/object` tag to indicate dumped PHP objects was removed in favor of
   the `!php/object` tag.
