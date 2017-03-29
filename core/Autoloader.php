<?php
//===============autoloader===========================

spl_autoload_register('core_autoload');
function core_autoload($classname)
{
    $documentRoot = $_SERVER["DOCUMENT_ROOT"];
    $classFile = "";
    if (preg_match('/[a-zA-Z]+Controller$/', $classname)) {
        $classFile = $documentRoot. '/controllers/' . $classname . '.php';
    } elseif (preg_match('/[a-zA-Z]+Model$/', $classname)) {
        $classFile =  $documentRoot.'/models/' . $classname . '.php';
    } elseif (preg_match('/[a-zA-Z0-9]+View$/', $classname)) {
        $classFile = $documentRoot. '/views/' . $classname . '.php';
    }

    if (file_exists($classFile)) {
        include $classFile;
    }
}