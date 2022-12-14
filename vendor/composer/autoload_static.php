<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita7af5bcfe2d62f9de81c166e39e5f5d0
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
        ),
        'L' => 
        array (
            'Leandroferreirama\\PagamentoCnab240Retorno\\' => 42,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Leandroferreirama\\PagamentoCnab240Retorno\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInita7af5bcfe2d62f9de81c166e39e5f5d0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita7af5bcfe2d62f9de81c166e39e5f5d0::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita7af5bcfe2d62f9de81c166e39e5f5d0::$classMap;

        }, null, ClassLoader::class);
    }
}
