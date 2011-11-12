#!/usr/bin/env php
<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*

CAUTION: This file installs the dependencies needed to run the Symfony2 test suite.
If you want to create a new project, download the Symfony Standard Edition instead:

http://symfony.com/download

*/

set_time_limit(0);

if (!is_dir($vendorDir = dirname(__FILE__).'/vendor')) {
    mkdir($vendorDir, 0777, true);
}

$transport = isset($argv[1]) && in_array($argv[1], array('http', 'https', 'git')) ? $argv[1] : 'http';

$deps = array(
    array('doctrine', "{$transport}://github.com/doctrine/doctrine2.git", 'origin/master'),
    array('doctrine-dbal', "{$transport}://github.com/doctrine/dbal.git", 'origin/master'),
    array('doctrine-common', "{$transport}://github.com/doctrine/common.git", 'origin/master'),
    array('monolog', "{$transport}://github.com/Seldaek/monolog.git", '1.0.2'),
    array('swiftmailer', "{$transport}://github.com/swiftmailer/swiftmailer.git", 'origin/master'),
    array('twig', "{$transport}://github.com/fabpot/Twig.git", 'origin/master'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;

    $installDir = $vendorDir.'/'.$name;
    $install = false;
    if (!is_dir($installDir)) {
        $install = true;
        echo "> Installing $name\n";
        system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    if (!$install) {
        echo "> Updating $name\n";
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
