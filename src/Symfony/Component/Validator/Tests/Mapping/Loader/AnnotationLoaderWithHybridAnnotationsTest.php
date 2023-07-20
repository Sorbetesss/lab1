<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Mapping\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class AnnotationLoaderWithHybridAnnotationsTest extends AnnotationLoaderTest
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testLoadClassMetadataReturnsTrueIfSuccessful()
    {
        $this->expectDeprecation('Since symfony/validator 6.4: Passing a "Doctrine\Common\Annotations\AnnotationReader" instance as argument 1 to "Symfony\Component\Validator\Mapping\Loader\AnnotationLoader::__construct()" is deprecated, pass null or omit the parameter instead.');
        $this->expectDeprecation('Since symfony/validator 6.4: Class "Symfony\Component\Validator\Tests\Fixtures\Attribute\Entity" uses Doctrine Annotations to configure validation constraints, which is deprecated. Use PHP attributes instead.');
        $this->expectDeprecation('Since symfony/validator 6.4: Property "Symfony\Component\Validator\Tests\Fixtures\Attribute\Entity::$firstName" uses Doctrine Annotations to configure validation constraints, which is deprecated. Use PHP attributes instead.');

        parent::testLoadClassMetadataReturnsTrueIfSuccessful();
    }

    /**
     * @group legacy
     */
    public function testLoadClassMetadataReturnsFalseIfNotSuccessful()
    {
        $this->expectDeprecation('Since symfony/validator 6.4: Passing a "Doctrine\Common\Annotations\AnnotationReader" instance as argument 1 to "Symfony\Component\Validator\Mapping\Loader\AnnotationLoader::__construct()" is deprecated, pass null or omit the parameter instead.');

        parent::testLoadClassMetadataReturnsFalseIfNotSuccessful();
    }

    /**
     * @group legacy
     */
    public function testLoadClassMetadata()
    {
        $this->expectDeprecation('Since symfony/validator 6.4: Passing a "Doctrine\Common\Annotations\AnnotationReader" instance as argument 1 to "Symfony\Component\Validator\Mapping\Loader\AnnotationLoader::__construct()" is deprecated, pass null or omit the parameter instead.');
        $this->expectDeprecation('Since symfony/validator 6.4: Class "Symfony\Component\Validator\Tests\Fixtures\Attribute\Entity" uses Doctrine Annotations to configure validation constraints, which is deprecated. Use PHP attributes instead.');
        $this->expectDeprecation('Since symfony/validator 6.4: Property "Symfony\Component\Validator\Tests\Fixtures\Attribute\Entity::$firstName" uses Doctrine Annotations to configure validation constraints, which is deprecated. Use PHP attributes instead.');

        parent::testLoadClassMetadata();
    }

    /**
     * @group legacy
     */
    public function testLoadParentClassMetadata()
    {
        $this->expectDeprecation('Since symfony/validator 6.4: Passing a "Doctrine\Common\Annotations\AnnotationReader" instance as argument 1 to "Symfony\Component\Validator\Mapping\Loader\AnnotationLoader::__construct()" is deprecated, pass null or omit the parameter instead.');

        parent::testLoadParentClassMetadata();
    }

    /**
     * @group legacy
     */
    public function testLoadClassMetadataAndMerge()
    {
        $this->expectDeprecation('Since symfony/validator 6.4: Passing a "Doctrine\Common\Annotations\AnnotationReader" instance as argument 1 to "Symfony\Component\Validator\Mapping\Loader\AnnotationLoader::__construct()" is deprecated, pass null or omit the parameter instead.');
        $this->expectDeprecation('Since symfony/validator 6.4: Class "Symfony\Component\Validator\Tests\Fixtures\Attribute\Entity" uses Doctrine Annotations to configure validation constraints, which is deprecated. Use PHP attributes instead.');
        $this->expectDeprecation('Since symfony/validator 6.4: Property "Symfony\Component\Validator\Tests\Fixtures\Attribute\Entity::$firstName" uses Doctrine Annotations to configure validation constraints, which is deprecated. Use PHP attributes instead.');

        parent::testLoadClassMetadataAndMerge();
    }

    /**
     * @group legacy
     */
    public function testLoadGroupSequenceProviderAnnotation()
    {
        $this->expectDeprecation('Since symfony/validator 6.4: Passing a "Doctrine\Common\Annotations\AnnotationReader" instance as argument 1 to "Symfony\Component\Validator\Mapping\Loader\AnnotationLoader::__construct()" is deprecated, pass null or omit the parameter instead.');

        parent::testLoadGroupSequenceProviderAnnotation();
    }

    protected function createAnnotationLoader(): AnnotationLoader
    {
        return new AnnotationLoader(new AnnotationReader());
    }

    protected function getFixtureNamespace(): string
    {
        return 'Symfony\Component\Validator\Tests\Fixtures\Attribute';
    }
}
