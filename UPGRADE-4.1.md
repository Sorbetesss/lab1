UPGRADE FROM 4.0 to 4.1
=======================

Config
------

 * Implementing `ParentNodeDefinitionInterface` without the `getChildNodeDefinitions()` method
   is deprecated and will be unsupported in 5.0.

EventDispatcher
---------------

 * The `TraceableEventDispatcherInterface` has been deprecated and will be removed in 5.0.

FrameworkBundle
---------------

 * A `RouterInterface` that does not implement the `WarmableInterface` is deprecated and will not be supported in Symfony 5.0.

HttpFoundation
--------------

 * Passing the file size to the constructor of the `UploadedFile` class is deprecated and won't be
   supported anymore in 5.0.

 * The `getClientSize()` method of the `UploadedFile` class is deprecated. Use `getSize()` instead.

Security
--------

 * The `ContextListener::setLogoutOnUserChange()` method is deprecated and will be removed in 5.0.

SecurityBundle
--------------

 * The `logout_on_user_change` firewall option is deprecated and will be removed in 5.0.
 * The `SecurityUserValueResolver` class is deprecated and will be removed in 5.0, use
   `Symfony\Component\Security\Http\Controller\UserValueResolver` instead.

Translation
-----------

 * The `FileDumper::setBackup()` method is deprecated and will be removed in 5.0.
 * The `TranslationWriter::disableBackup()` method is deprecated and will be removed in 5.0.

Validator
--------

 * The `Email::__construct()` 'strict' property is deprecated and will be removed in 5.0. Use 'mode'=>"strict" instead.
 * Calling `EmailValidator::__construct()` method with a boolean parameter is deprecated and will be removed in 5.0, use `EmailValidator("strict")` instead.
 * Deprecated the `checkDNS` and `dnsMessage` options of the `Url` constraint. They will be removed in 5.0.

Form
----

 * Using callable strings as `empty_data` in `ChoiceType` has been deprecated in Symfony 4.1 use a `\Closure` instead.

  Before:
  
  ```php
  'empty_data' => 'SomeValueObject::getDefaultValue',
  ```

  After:
  
  ```php
  'empty_data' => function (FormInterface $form, $data) {
      return SomeValueObject::getDefaultValue();
  },
  ```

Workflow
--------

 * Deprecated the `add` method in favor of the `addWorkflow` method in `Workflow\Registry`.
 * Deprecated `SupportStrategyInterface` in favor of `WorkflowSupportStrategyInterface`.
 * Deprecated the class `ClassInstanceSupportStrategy` in favor of the class `InstanceOfSupportStrategy`.
