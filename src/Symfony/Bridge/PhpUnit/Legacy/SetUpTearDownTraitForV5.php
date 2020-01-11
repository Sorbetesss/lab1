<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\PhpUnit\Legacy;

/**
 * @internal
 */
trait SetUpTearDownTraitForV5
{
    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::doSetUpBeforeClass();
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass()
    {
        static::doTearDownAfterClass();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        static::doSetUp();
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        static::doTearDown();
    }

    private static function doSetUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    private static function doTearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    private function doSetUp()
    {
        parent::setUp();
    }

    private function doTearDown()
    {
        parent::tearDown();
    }
}
