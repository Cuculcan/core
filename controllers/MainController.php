<?php
/**
 * Controller for process site root url
 * 
 * if domain name example.com 
 * controller process urls like a
 * http://example.com or  http://example.com/main which is same
 * 
 * !! must be present in all projects !!
 */

include 'controllers/base/BaseController.php';
class MainController extends BaseController
{
    public function __construct($request) {
        parent::__construct($request, "Main");
        $this->log = Logger::getLogger(__CLASS__);
    }

    protected function setActions() {
      
        $this->get('/main', function($urlParams){

            $this->prepareDataForMainPage();
            $this->showView("MainView");
        });
        
    }
    
    private function prepareDataForMainPage(){
        //echo "prepare data for main page!!";
    }

}