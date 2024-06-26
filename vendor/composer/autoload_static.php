<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf692c667b3fe03e657d40f77843dd61d
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'Oscurlo\\ComponentRenderer\\' => 26,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Oscurlo\\ComponentRenderer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf692c667b3fe03e657d40f77843dd61d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf692c667b3fe03e657d40f77843dd61d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf692c667b3fe03e657d40f77843dd61d::$classMap;

        }, null, ClassLoader::class);
    }
}
