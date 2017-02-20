<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
error_reporting(-1);
session_save_path($_SERVER['DOCUMENT_ROOT'].'/session');
session_start();
mb_internal_encoding("UTF-8");

ob_start();

date_default_timezone_set("Europe/Moscow");

//подключаем нужные классы
require_once('config/config.php');
require_once('core/Autoloader.php');
require_once('core/Request.php');
require_once('core/Router.php');
require_once('core/RCError.php');
require_once('core/MySQLClass.php');


// Include and configure log4php
require_once('third-party/log4php-2.3.0/php/Logger.php');
Logger::configure('config/log4php-config.xml');

//парсим запрос
$request = new Request();

//создаем маршрутизатор
$router = new Router($request);

//находим контроллер который обработает запрос
$controller = $router->getController();
if(!$controller){
    $errorController = new RCError();
    $errorController->showErrorPage("404", "It would be nice to have such a page. But this page does not exist!");
    exit;
}

//обрабатываем запрос
$controller->processRequest();

?>


