<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
error_reporting(-1);
session_save_path($_SERVER['DOCUMENT_ROOT'].'/session');
session_start();
mb_internal_encoding("UTF-8");

ob_start();

date_default_timezone_set("Europe/Moscow");


require 'vendor/autoload.php';

require 'config/config.php';
require 'third-party/log4php-2.3.0/php/Logger.php';
Logger::configure('config/log4php-config.xml');

$loader = new \Composer\Autoload\ClassLoader();
// register classes with namespaces
$loader->add('Cuculcan\Core', __DIR__."/src");
$loader->add('Example',      __DIR__.'/');

// activate the autoloader
$loader->register();

use Cuculcan\Core\Application;

(new Application("Example"))->run();

?>


