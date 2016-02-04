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

Serializer
----------

 * The ability to pass a Doctrine `Cache` instance to the `ClassMetadataFactory`
   class has been removed. You should use the `CacheClassMetadataFactory` class
   instead.

Yaml
----

 * The `!!php/object` tag to indicate dumped PHP objects was removed in favor of
   the `!php/object` tag.

Doctrine Bridge
---

 * The `UniqueEntity` constraints now requires a `target` property when loaded
   with the `StaticMethodLoader`.
