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

namespace Example\Controllers;

use Example\Controllers\Base\BaseController;

//include 'controllers/base/BaseController.php';
class MainController extends BaseController
{

    public function __construct($request, $namespce)
    {
        parent::__construct($request, $namespce);
        $this->log = \Logger::getLogger(__CLASS__);
    }

    protected function setActions()
    {

        $this->get('/main', function($urlParams) {

//            $this->prepareDataForMainPage();
//            $this->showView("MainView");
            
            echo json_encode([
                'aaa'=>'ololo',
                'bbb'=> htmlspecialchars("<p>bskdjaslkd</p>"),
                'ccc'=>htmlspecialchars('<a href="zzzzz">pew-pew</a>')
            ]);
            
        
        });
    }

    private function prepareDataForMainPage()
    {
        //echo "prepare data for main page!!";
    }

}
