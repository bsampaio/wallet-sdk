<?php


namespace Lifepet\Wallet\SDK\Service;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class BasicService
{
    protected $validator;

    public function __construct()
    {
        // Create a Filesystem instance
        $filesystem = new Filesystem();
        // Create a new FileLoader instance specifying the translation path
        $loader = new FileLoader($filesystem, dirname(dirname(__FILE__)) . '/lang');
        // Specify the translation namespace
        $loader->addNamespace('lang', dirname(dirname(__FILE__)) . '/lang');
        // This is used to create the path to your validation.php file
        $loader->load($lang = 'en', $group = 'validation', $namespace = 'lang');

        $factory = new Translator($loader, 'en');

        $this->validator = new \Illuminate\Validation\Factory($factory);
    }
}