<?php

namespace Cuculcan\Core;

class Application {

    private $projectNamespace;
    public function __construct($projectNamespace) {
        $this->projectNamespace = $projectNamespace;
    }

    public function run() {
        //prepare request data
        $request = new Request();
       
        //create router
        $router = new Router($request, $this->projectNamespace);

        //search fo destination controller
        $controller = $router->getController();
        if(!$controller){
            $errorController = new ErrorController($request, $this->projectNamespace);
            $errorController->showErrorPage("404", "");
            exit;
        }

        //process request
        $controller->processRequest();
    }

}
