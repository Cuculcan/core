<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
error_reporting(-1);
session_save_path(__DIR__ . '/../session');
session_start();
mb_internal_encoding("UTF-8");

ob_start();

date_default_timezone_set("Europe/Moscow");


require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../config/config.php');
//require(__DIR__ . '/../third-party/log4php-2.3.0/php/Logger.php');
Logger::configure(__DIR__ . '/../config/log4php-config.xml');


use Cuculcan\Core\Application;

(new Application("Example"))->run();

?>


