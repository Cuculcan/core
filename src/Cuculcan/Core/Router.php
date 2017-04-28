<?php

namespace Cuculcan\Core;

class Router
{
    private $request;
    private $projectNamespace;
    
    public function __construct($request, $projectNamespace) {
        $this->request = $request;
        $this->projectNamespace = $projectNamespace;
    }

    public function getController(){
        // route the request to the right place
        $controller_name ="";
        $namespace = $this->projectNamespace."\Controllers";
        
        global $config;
        $languege = $this->detectLanguge( $config['langueges']['supported'], $config['langueges']['default']);
        
        $urlElementsLength = count($this->request->urlElements);
        if($urlElementsLength > 0){

            $urlElementsLengthTest = $urlElementsLength;
            for($i = 0; $i < $urlElementsLength; $i++){
                $searcheded_controller_name = "";
                for($j = 0; $j < $urlElementsLengthTest; $j++){

                    $searcheded_controller_name .= ucfirst($this->request->urlElements[$j]);
                    if($searcheded_controller_name == ""){
                        $searcheded_controller_name="Main";
                        $this->request->urlElements =  array('main');
                    }
                }

                $searcheded_controller_name .='Controller';

                $searcheded_controller_name = $namespace."\\".$searcheded_controller_name;
                if (class_exists($searcheded_controller_name)) {
                    $controller_name = $searcheded_controller_name;

                    break;
                }

                $urlElementsLengthTest--;
            }
        }
        else{
            $controller_name = $namespace."\\"."MainController";
            $this->request->urlElements =  array('main');
        }

        if (!$controller_name) {
            return null;
        }
        
        $controller =  new $controller_name($this->request, $this->projectNamespace);
        $controller->setLanguege($languege);
        return $controller;
    }
    
    private function detectLanguge( $supprted, $default){
       $urlParts = $this->request->getUrlElements();
       $urlLength = count($urlParts);
       if($urlLength == 0){
           return $default;
       }
       
       $langPart = $urlParts[0];
       if($langPart==""){
           return $default;
       }
       
       if(!in_array($langPart, $supprted)){
           return $default;
       }
       
       
       array_shift($urlParts);
       
       $this->request->setUrlElements($urlParts);
       return $langPart;
       
    }

}

