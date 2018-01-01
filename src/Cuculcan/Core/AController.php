<?php
namespace Cuculcan\Core;

use Cuculcan\Core\ErrorController;
use Cuculcan\Core\Errors\BadRequestException;
use Cuculcan\Core\Errors\InternalException;
use Datetime;
use Cuculcan\Core\Request;
use Cuculcan\Core\Errors\UnauthorizedException;
use Cuculcan\Core\Errors\ForbiddentException;

abstract class AController
{
    protected $db;
    /**
     *
     * @var Request 
     */
    protected $request;
    protected $isProcessed;
    protected $log;
    protected $sessionUser;
    protected $sessionUserTimezone;
    protected $sessionDirectorUser;
    protected $sessionDirectorUserTimezone;
    protected $model = [];
    protected $language;
    
    private $projectNamespace;

    public function __construct($request, $namespace)
    {
        $this->request = $request;
        $this->projectNamespace = $namespace;
        $this->isProcessed = false;

        global $config;

        $host = $config['db']['server'];
        $dbname = $config['db']['name'];
        $user = $config['db']['user'];
        $pass = $config['db']['pass'];
        $this->db = new \PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $this->db->query('SET NAMES utf8mb4');
    }

    abstract protected function setActions();

    public function processRequest() {
        try {
            $this->setActions();

            if (!$this->isProcessed) {
                //can't find the right processor
                throw new BadRequestException("Bad request parameters!" );
            }
            
        } catch (InternalException $e) {
            $this->showError("500", $e->getMessage());
            
        } catch (BadRequestException $e) {
            $this->showError("400", $e->getMessage());
            
        } catch (ForbiddentException $e) {
            $this->showError("403", $e->getMessage());
            
        } catch (UnauthorizedException $e) {
            
            if ($e->isSowError) {
                $this->showError("401", $e->getMessage());
            }else{
                $redirectTo = "";
                if (isset($e->redirectUrl) && $e->redirectUrl != "") {
                    $redirectTo = "?redirect=" . $e->redirectUrl;
                }
                
                global $config;
                header("Location: " . $config['common']['login_url'] . $redirectTo);
            }
        } finally {
            $this->closeDBConnection();
        }
    }

    protected function getDateFromString($dateString) {

        $date = $dateString ? $dateString : '';
        $date = $date!='' ? explode('.', $date) : array();
        $date = count($date)==3 ? new DateTime($date[1].'/'.$date[0].'/'.$date[2]) : false;
        $date = $date ? intval($date->format('U')) : 0;

        return $date;
    }

    protected function showView($viewClass, $parameters = null) {

        $viewData = [];
        if (isset($parameters) && is_array($parameters)) {
            $viewData = $parameters;
        } else {
            $viewData = $this->model;
        }

        //set mandatory modelData for all Views  
        $viewData['session_user'] = $this->sessionUser;
        $viewData['language'] = $this->getLanguage();

        $viewClass = $this->projectNamespace."\\Views\\".$viewClass;
        if (!class_exists( $viewClass)) {
            throw new InternalException("View class \"" . $viewClass . "\" not found ! ");
        }

        $view = new $viewClass($viewData);
        $view->render();
    }

    private function showError($code, $text){
        $errorController = new ErrorController($this->request, $this->projectNamespace);
        $errorController->showErrorPage($code, $text , $this->request->isAjax);
    }

    private function closeDBConnection(){
//        if ($this->db) {
//            $this->db->Close();
//        }
        $this->db = null;
    }

    public function get($requestTemplate, $action){
        $this->processHttpRequestMethod("GET", $requestTemplate, $action);
    }

    public function post($requestTemplate, $action){
        $this->processHttpRequestMethod("POST", $requestTemplate, $action);
    }

    public function del($requestTemplate, $action){
        $this->processHttpRequestMethod("DELETE", $requestTemplate, $action);
    }

    public function put($requestTemplate, $action){
        $this->processHttpRequestMethod("PUT", $requestTemplate, $action);
    }

    private function processHttpRequestMethod($method, $requestTemplate, $action){
        if($this->request->method !== $method){
            return false;
        }
        return $this->executeAction($requestTemplate, $action);
    }

    private function executeAction($requestTemplate, $action){

        $params = explode('/', rtrim($requestTemplate,"/"));
        $urlParams = array();

        $paramsCount = count($params);
        if ($paramsCount -1 != count($this->request->urlElements)) {
            return false;
        }


        for ($i = 1; $i < $paramsCount; $i++) {
            if (strpos($params[$i], '{') !== false) {
                $paramName = str_replace(array("{", "}"), "", $params[$i]);
                $urlParams[$paramName] = $this->request->urlElements[$i-1];
                continue;
            }

            if (strtolower ($params[$i]) != strtolower ($this->request->urlElements[$i-1])) {

                return false;
            }
        }

        if($this-> isProcessed === true){
            throw new InternalException("Ambiguous controller action !");
        }

        $action($urlParams);
        $this-> isProcessed = true;
        return true;
    }

    public function getDb() {
        return $this->db;
    }

    public function setDb($db) {
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
   
    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }


}