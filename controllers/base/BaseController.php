<?php
/**
 * контроллер реализует общее поведение для всех контроллеров
 * например: сессии пользователей
 * зависит от конкретного проекта
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

        //если сессия пустая
        if(!isset($_SESSION['USER_ID'])){
            $this->sessionUser = null;
            return;
        }

        //если не совпадает HTTP_USER_AGENT
        if (!isset($_SESSION['HTTP_USER_AGENT']) || $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
            $this->sessionUser = null;
            return;
        }

        //находим пользователя в БД
        $user = $this->getUserWithId($_SESSION['USER_ID']);
        //если не нашли
        if(!isset($user)) {
            $this->sessionUser = null;
            return;
        }

        //заполняем данные пользователя
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
                throw new UnauthorizedException("", true, "401 - пользователь не авторизован ");
            }
            else{
                $redirectUrl = (isset($redirect) && $redirect!="")? $redirect : $this->request->requestUri;
                throw new UnauthorizedException($redirectUrl);
            }
        }
        return true;
    }

}
