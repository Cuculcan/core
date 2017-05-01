<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
error_reporting(-1);
session_save_path($_SERVER['DOCUMENT_ROOT'].'/session');
session_start();
mb_internal_encoding("UTF-8");

ob_start();

date_default_timezone_set("Europe/Moscow");


//require 'src/Cuculcan/Core/autoload.php';
//require 'Example/autoload.php';
require 'vendor/autoload.php';

require 'config/config.php';
require 'third-party/log4php-2.3.0/php/Logger.php';
Logger::configure('config/log4php-config.xml');

// instantiate the loader
//$loader = new \Cuculcan\Core\Psr4AutoloaderClass;
//$loader = new \Cuculcan\Core\ClassLoader;
//$loader->register();
//$loader->addNamespace('Cuculcan\\Core', __DIR__."/src/Cuculcan/Core");
//$loader->addNamespace('Example', __DIR__."/Example");

$loader = new \Composer\Autoload\ClassLoader();
      // register classes with namespaces
$loader->add('Cuculcan\Core', __DIR__."/src");
$loader->add('Example',      __DIR__.'/');

// activate the autoloader
$loader->register();

// to enable searching the include path (eg. for PEAR packages)
//$loader->setUseIncludePath(true);


use Cuculcan\Core\Application;
use Cuculcan\Core\Errors\BadRequestException;

//use Example\Services\MyAop;
use Example\Services\ApplicationAspectKernel;
use Example\Services\aopClass;




$applicationAspectKernel = ApplicationAspectKernel::getInstance();
$applicationAspectKernel->init(array(
    'debug' => true, // Use 'false' for production mode
    // Cache directory
    'cacheDir' => $_SERVER["DOCUMENT_ROOT"] . '/cache/', // Adjust this path if needed
    // Include paths restricts the directories where aspects should be applied, or empty for all source files
    'includePaths' => array(
        $_SERVER["DOCUMENT_ROOT"]. '/src/Cuculcan/Core',
        $_SERVER["DOCUMENT_ROOT"]. '/Example/Services'
    )
));

$aop = new aopClass();
$aop->doAop("bzzzz");

(new Application("Example"))->run();

?>


