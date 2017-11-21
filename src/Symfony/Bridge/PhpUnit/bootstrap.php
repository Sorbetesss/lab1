<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Bridge\PhpUnit\DeprecationErrorHandler;

// Replace the native phpunit Blacklist, it's a broken artifact from the past
if (!class_exists('Symfony\Bridge\PhpUnit\Blacklist', false)) {
    require_once __DIR__.'/Blacklist.php';
}

// Detect if we need to serialize deprecations to a file.
if ($file = getenv('SYMFONY_DEPRECATIONS_SERIALIZE')) {
    DeprecationErrorHandler::collectDeprecations($file);

    return;
}

// Detect if we're loaded by an actual run of phpunit
if (!defined('PHPUNIT_COMPOSER_INSTALL') && !class_exists('PHPUnit_TextUI_Command', false) && !class_exists('PHPUnit\TextUI\Command', false)) {
    return;
}

// Enforce a consistent locale
setlocale(LC_ALL, 'C');

if (!class_exists('Doctrine\Common\Annotations\AnnotationRegistry', false) && class_exists('Doctrine\Common\Annotations\AnnotationRegistry')) {
    AnnotationRegistry::registerLoader('class_exists');
}

if ('disabled' !== getenv('SYMFONY_DEPRECATIONS_HELPER')) {
    DeprecationErrorHandler::register(getenv('SYMFONY_DEPRECATIONS_HELPER'));
}
