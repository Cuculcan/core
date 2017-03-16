<?php


abstract class AController
{
    protected $db;
    protected $request;
    protected $controllerName;
    protected $isProcessed;
    protected $log;
    protected $sessionUser;

    protected $documentRoot;
    
    protected $model = [];
    
    protected $languege;
    
    public function __construct($request , $name) {
        $this->request = $request;
        $this->controllerName = $name;
        $this->isProcessed = false;
        $this->documentRoot = $_SERVER["DOCUMENT_ROOT"];
       
        //initialize database connection
        global $config;
        $this->db = new MySQLClass($config['db']['server'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
    }
    abstract protected function setActions();

    public function processRequest() {
        try {
            $this->setActions();

            if (!$this->isProcessed) {
                //can't find the right processor
                throw new BadRequestException("Bad request parameters!");
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

    protected function showView($viewClass, $parameters = null) {

        $viewData = [];
        if (isset($parameters) && is_array($parameters)) {
            $viewData = $parameters;
        } else {
            $viewData = $this->model;
        }

        //set mandatory modelData for all Views  
        $viewData['session_user'] = $this->sessionUser;
        $viewData['languege'] = $this->getLanguege();

        if (!class_exists($viewClass)) {
            throw new InternalException("View class \"" . $viewClass . "\" not found ! ");
        }

        $view = new $viewClass($viewData);
        $view->render();
    }

    private function showError($code, $text){
        $errorController = new RCError($this->request);
        $errorController->showErrorPage($code, $text , $this->request->isAjax);
    }

    private function closeDBConnection(){
        if ($this->db) {
            $this->db->Close();
        }
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

    /**
     * @return mixed
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param mixed $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }
    public function getLanguege() {
        return $this->languege;
    }

    public function setLanguege($languege) {
        $this->languege = $languege;
    }


}