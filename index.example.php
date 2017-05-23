<?php
echo "not here!!!!"; die();

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

use Cuculcan\Core\Application;

(new Application("Example"))->run();

?>


