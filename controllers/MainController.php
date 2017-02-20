<?php

include 'controllers/base/BaseController.php';
class MainController extends BaseController
{
    public function __construct($request) {
        parent::__construct($request, "Main");
        $this->log = Logger::getLogger(__CLASS__);
    }

    protected function setActions() {
        $that = $this;

        $this->get('/main', function($urlParams) use ( &$that){
           
            echo $that->request->redirectTo("/bla/bla/bla");
            die();
            
            $that->prepareDataForMainPage();
            $that->showView("MainView");
        });
        
    }
    
    private function prepareDataForMainPage(){
        //echo "prepare data for main page!!";
    }

}