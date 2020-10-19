<?php

class ComposerLoaderFake
{
    public function getPrefixes()
    {
        return [];
    }

    public function getPrefixesPsr4()
    {
        return [
            'App\\Services\\' => [__DIR__.'/../../fake_app/'],
            'acme\\lib\\' => [__DIR__.'/../acme/lib/'],
        ];
    }

    public function loadClass($className)
    {
        foreach ($this->getPrefixesPsr4() as $prefix => $baseDirs) {
            if (strpos($className, $prefix) !== 0) {
                continue;
            }

            foreach ($baseDirs as $baseDir) {
                $file = str_replace([$prefix, '\\'], [$baseDir, '/'], $className.'.php');
                if (file_exists($file)) {
                    require $file;
                }
            }
        }
    }
}

class ComposerAutoloaderInitFake
{
    private static $loader;

    public static function getLoader()
    {
        if (null === self::$loader) {
            self::$loader = new ComposerLoaderFake();
            spl_autoload_register([self::$loader, 'loadClass']);
        }

        return self::$loader;
    }
}
