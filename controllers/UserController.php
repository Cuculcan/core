<?php

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
        $that = $this;
        
        //process GET request http://{my site name}/user
        $this->get('/user', function ($urlParams) use (&$that) {
            
            $that->model['param'] = 'param from user controller';
            $that->showView("UserView");
        });
       
        //process GET request http://{my site name}/user/set_name?name=VasiliyPupkin
        $this->get('/user/set_name', function ($urlParams) use (&$that) {
            
            $that->model['param'] = $that->request->getQueryParameter("name", "");           
            $that->showView("UserView");
        });
        
        //process GET request http://{my site name}/user/12345/get_name
        $this->get('/user/{userId}/get_name', function ($urlParams) use (&$that) {
            
            $userId = isset($urlParams['userId']) ? $urlParams['userId']: 0;
            
            $that->model['param'] = "User ID =".$userId;            
            $that->showView("UserView");
        });
        
        //process POST request http://{my site name}/user/create 
        //with Form parameters name='nameOfUser'&email='email@example.com'
        $this->post('/user/create', function ($urlParams) use (&$that) {
            $userData = [];
            //get post parameters from request
            $userData['name'] = $that->request->getQueryParameter("name", "");
            $userData['email'] = $that->request->getQueryParameter("email", "");
            
            $userInfo = $that->saveUser($userData);
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