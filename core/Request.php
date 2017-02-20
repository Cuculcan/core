<?php


class Request
{
    public $urlElements;        //массив частей урл запроса
    public $method;             //метод
    public $queryParameters;    //массив параметров запроса
    public $requestUri;         //строка запроса
    public $format;
    public $isAjax;
    public $headers;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];

        $urlElements = null;
        if(isset($_GET) && count($_GET)>0 &&  isset($_GET['handler'])) {
            $this->urlElements = explode('/', rtrim($_GET['handler'], "/"));
        }
//        if(isset($_POST) && count($_POST)>0 && isset($_POST['handler'])) {
//            $this->urlElements = explode('/', $_POST['handler']);
//        }
        $this->parseIncomingParams();

        // initialise json as default format
        $this->format = 'json';
        if(isset($this->parameters['format'])) {
            $this->format = $this->parameters['format'];
        }

        $this->isAjax = $this->check_is_ajax();

        $this->headers = getallheaders();

        $this->requestUri = $_SERVER['REQUEST_URI'];
        return true;
    }

    public function parseIncomingParams() {
        $parameters = array();

        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            unset($parameters['handler']);
        }

        // now how about PUT/POST bodies? These override what we got from GET
        $body = file_get_contents("php://input");
        $content_type = false;
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];
        }

        if(strpos($content_type, "application/json") !== false){
            $body_params = json_decode($body);
            if($body_params) {
                foreach($body_params as $param_name => $param_value) {
                    $parameters[$param_name] = $param_value;
                }
            }
            $this->format = "json";
        }

        if(strpos($content_type, "application/x-www-form-urlencoded") !== false){
            parse_str($body, $postvars);
            foreach($postvars as $field => $value) {
                $parameters[$field] = $value;

            }
            $this->format = "html";
        }
        $this->queryParameters = $parameters;
    }

    function check_is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    }

    function getHeaderWithName($headerName){
        foreach($this->headers as $name => $value){
            if(strtolower($headerName) == strtolower($name) ){
                return $value;
            }
        }

        return null;
    }

    public function getQueryParameter($name, $default=null){
        return (isset($this->queryParameters[$name])) ? $this->queryParameters[$name] : $default;
    }
    
    public function redirectTo($url){
        $server = $this->serverUrl();
        header("Location: ".$server."/user/login");
    }

    private function serverUrl(){
        $protocol ="";
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
       
        return $protocol . "://" . $_SERVER['HTTP_HOST'];
    }
}
