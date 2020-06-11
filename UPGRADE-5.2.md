UPGRADE FROM 5.1 to 5.2
=======================

Lock
----

 * Deprecated passing of `database` or `collection` to `MongoDbStore` via connection URI, use `$options` instead.

Mime
----

 * Deprecated `Address::fromString()`, use `Address::create()` instead

Validator
---------

 * Deprecated the `allowEmptyString` option of the `Length` constraint.

   Before:

   ```php
   use Symfony\Component\Validator\Constraints as Assert;

   /**
    * @Assert\Length(min=5, allowEmptyString=true)
    */
   ```

   After:

   ```php
   use Symfony\Component\Validator\Constraints as Assert;

   /**
    * @Assert\AtLeastOneOf({
    *     @Assert\Blank(),
    *     @Assert\Length(min=5)
    * })
    */
   ```
