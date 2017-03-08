<?php
/**
 * Controller for process urls which beginning with /user
 *
 * If domain name example.com 
 * controller will process urls like a
 * http://example.com/user
 * 
 * 
 */

include 'controllers/base/BaseController.php';

class UserController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request, "User");
        $this->log = Logger::getLogger(__CLASS__);
    }

    protected function setActions()
    {
        //process GET request http://{my site name}/user
        $this->get('/user', function ($urlParams)  {
            
           $this->model['param'] = 'param from user controller';
           $this->showView("UserView");
        });
       
        //process GET request http://{my site name}/user/set_name?name=VasiliyPupkin
        $this->get('/user/set_name', function ($urlParams)  {
            
           $this->model['param'] =$this->request->getQueryParameter("name", "");           
           $this->showView("UserView");
        });
        
        //process GET request http://{my site name}/user/12345/get_name
        $this->get('/user/{userId}/get_name', function ($urlParams)  {
            
            $userId = isset($urlParams['userId']) ? $urlParams['userId']: 0;
            
           $this->model['param'] = "User ID =".$userId;            
           $this->showView("UserView");
        });
        
        //process POST request http://{my site name}/user/create 
        //with Form parameters name='nameOfUser'&email='email@example.com'
        $this->post('/user/create', function ($urlParams)  {
            $userData = [];
            //get post parameters from request
            $userData['name'] =$this->request->getQueryParameter("name", "");
            $userData['email'] =$this->request->getQueryParameter("email", "");
            
            $userInfo =$this->saveUser($userData);
            echo json_encode($userInfo);
        });
        
        
    }
    
    private function saveUser($userData){
        //Create new user code here;
        //.....
        //.....
        $userInfo = [
            'id' => 1,
            'name' => "userName",
            'email'=> "userEmail"
        ];
        return $userInfo;
    }
}