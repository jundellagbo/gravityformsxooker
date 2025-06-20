<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb5bbe07dcebc0c606d467c86334147d2
{
    public static $files = array (
        'f6d4f6bcee7247df6b777884c3e22f98' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v5p6.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb5bbe07dcebc0c606d467c86334147d2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb5bbe07dcebc0c606d467c86334147d2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb5bbe07dcebc0c606d467c86334147d2::$classMap;

        }, null, ClassLoader::class);
    }
}
