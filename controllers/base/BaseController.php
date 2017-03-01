<?php
/**
 * Controller implements the common behavior for all controllers
 * For example: user session
 * It depends on the specific project
 *
 */
include 'core/AController.php';
abstract class BaseController extends AController{
    
    
    public function __construct($request, $controllerName) {
        parent::__construct($request,$controllerName);
        $this->log = Logger::getLogger(__CLASS__);

        $this->initSessionUser();
    }
    
    
    private function initSessionUser() {

        //if session is empty
        if(!isset($_SESSION['USER_ID'])){
            $this->sessionUser = null;
            return;
        }

        //if  HTTP_USER_AGENT not identical
        if (!isset($_SESSION['HTTP_USER_AGENT']) || $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
            $this->sessionUser = null;
            return;
        }

        //find user into Database
        $user = $this->getUserWithId($_SESSION['USER_ID']);
        
        if(!isset($user)) {
            $this->sessionUser = null;
            return;
        }

        //fill user data
        $this->sessionUser = array(
            "id"=> $_SESSION['USER_ID'],
            "name"=>$user['name'],
            "role"=>$user['role']
        );

    }

    protected function getUserWithId($userID){
        $query = 'SELECT * FROM users u WHERE u.id ='.$userID;
        $result =  $this->db->Query($query);

        if($this->db->NumRows($result)!=0){
            $row = $this->db->FetchArray($result);
            return $row;
        }
        return null;
    }

    protected function saveSessionUser($userId, $userName){

        $_SESSION['USER_ID'] = $userId;
        $_SESSION['USER_NAME'] = $userName;
        $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

    }

    protected function clearSessionUser(){
        $_SESSION['USER_ID'] = null;
        $_SESSION['USER_NAME'] = "";
        $_SESSION['HTTP_USER_AGENT'] = "";


    }

    protected function checkAuthorization($redirect=""){
        if($this->sessionUser == null) {
            if($this->request->isAjax || $this->request->method!="GET"){
                throw new UnauthorizedException("", true, "401 - Unauthorized ");
            }
            else{
                $redirectUrl = (isset($redirect) && $redirect!="")? $redirect : $this->request->requestUri;
                throw new UnauthorizedException($redirectUrl);
            }
        }
        return true;
    }

}
