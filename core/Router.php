<?php


class Router
{
    private $request;
    public function __construct($request) {
        $this->request = $request;
    }

    public function getController(){
        // route the request to the right place
        $urlElementsLength = count($this->request->urlElements);

        $controller_name ="";
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

                if (class_exists($searcheded_controller_name)) {
                    $controller_name = $searcheded_controller_name;

                    break;
                }

                $urlElementsLengthTest--;
            }
        }
        else{
            $controller_name = "MainController";
            $this->request->urlElements =  array('main');
        }

        if (!$controller_name) {
            return null;
        }
        return new $controller_name($this->request);
    }

}

