UPGRADE FROM 6.1 to 6.2
=======================

Config
------

 * Deprecate calling `NodeBuilder::setParent()` without any arguments

Console
-------

 * Deprecate calling `*Command::setApplication()`, `*FormatterStyle::setForeground/setBackground()`, `Helper::setHelpSet()`, `Input*::setDefault()`, `Question::setAutocompleterCallback/setValidator()`without    any arguments
 * Change the signature of `OutputFormatterStyleInterface::setForeground/setBackground()` to `setForeground/setBackground(?string)`
 * Change the signature of `HelperInterface::setHelperSet()` to `setHelperSet(?HelperSet)`

DependencyInjection
-------------------

 * Change the signature of `ContainerAwareInterface::setContainer()` to `setContainer(?ContainerInterface)`
 * Deprecate calling `ContainerAwareTrait::setContainer()` without arguments

Form
----

 * Deprecate calling `Button/Form::setParent()`, `ButtonBuilder/FormConfigBuilder::setDataMapper()`, `TransformationFailedException::setInvalidMessage()` without arguments
 * Change the signature of `FormConfigBuilderInterface::setDataMapper()` to `setDataMapper(?DataMapperInterface)`
 * Change the signature of `FormInterface::setParent()` to `setParent(?self)`

FrameworkBundle
---------------

 * Deprecate the `Symfony\Component\Serializer\Normalizer\ObjectNormalizer` and
   `Symfony\Component\Serializer\Normalizer\PropertyNormalizer` autowiring aliases, type-hint against
   `Symfony\Component\Serializer\Normalizer\NormalizerInterface` or implement `NormalizerAwareInterface` instead
 * Deprecate `AbstractController::renderForm()`, use `render()` instead

HttpFoundation
--------------

 * Deprecate `Request::getContentType()`, use `Request::getContentTypeFormat()` instead
 * Deprecate calling `JsonResponse::setCallback()`, `Response::setExpires/setLastModified/setEtag()`, `MockArraySessionStorage/NativeSessionStorage::setMetadataBag()`, `NativeSessionStorage::setSaveHandler()`   without arguments

HttpKernel
----------

 * Deprecate `ArgumentValueResolverInterface`, use `ValueResolverInterface` instead
 * Deprecate calling `ConfigDataCollector::setKernel()`, `RouterListener::setCurrentRequest()` without arguments

Ldap
----

 * Deprecate `{username}` parameter use in favour of `{user_identifier}`

Mailer
------

 * Deprecate the `OhMySMTP` transport, use `MailPace` instead

Mime
----

 * Deprecate calling `Message::setBody()` without arguments

PropertyAccess
--------------

 * Deprecate calling `PropertyAccessorBuilder::setCacheItemPool()` without arguments

Security
--------

 * Add maximum username length enforcement of 4096 characters in `UserBadge` to
   prevent [session storage flooding](https://symfony.com/blog/cve-2016-4423-large-username-storage-in-session)
 * Deprecate the `Symfony\Component\Security\Core\Security` class and service, use `Symfony\Bundle\SecurityBundle\Security\Security` instead
 * Passing empty username or password parameter when using `JsonLoginAuthenticator` is not supported anymore
 * Add `$lifetime` parameter to `LoginLinkHandlerInterface::createLoginLink()`
 * Change the signature of `TokenStorageInterface::setToken()` to `setToken(?TokenInterface $token)`
 * Deprecate calling `TokenStorage::setToken()` or `UsageTrackingTokenStorage::setToken()` without arguments

Serializer
----------

 * Deprecate calling `AttributeMetadata::setSerializedName()`, `ClassMetadata::setClassDiscriminatorMapping()` without arguments
 * Change the signature of `AttributeMetadataInterface::setSerializedName()` to `setSerializedName(?string)`
 * Change the signature of `ClassMetadataInterface::setClassDiscriminatorMapping()` to `setClassDiscriminatorMapping(?ClassDiscriminatorMapping)`

Validator
---------

 * Deprecate the `loose` e-mail validation mode, use `html5` instead

VarDumper
---------

 * Deprecate calling `VarDumper::setHandler()` without arguments

Workflow
--------

 * The `Registry` is marked as internal and should not be used directly. use a tagged locator instead
    ```
    tagged_locator('workflow', 'name')
    ```
 * The first argument of `WorkflowDumpCommand` should be a `ServiceLocator` of
   all workflows indexed by names
 * Deprecate calling `Definition::setInitialPlaces()` without arguments
