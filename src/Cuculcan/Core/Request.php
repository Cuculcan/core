<?php

namespace Cuculcan\Core;

class Request
{
    public $urlElements;        //массив частей урл запроса
    public $method;             //метод
    public $queryParameters;    //массив параметров запроса
    public $requestUri;         //строка запроса
    public $format;
    public $isAjax;
    public $headers;

    private $inpitStream = 'php://input';

    public function __construct($inpitStream = 'php://input') {
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->urlElements = [];
        if(isset($_GET) && count($_GET)>0 &&  isset($_GET['handler'])) {
            $this->urlElements = explode('/', rtrim($_GET['handler'], '/'));
        }


        $this->inpitStream = $inpitStream;
        $this->parseIncomingParams();
        $this->isAjax = $this->isAJAX();
        $this->headers = $this->getAllHeaders();
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    private function parseIncomingParams() {

        $parameters = array();

        // first of all, pull the GET vars
        if(isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
            unset($parameters['handler']);
        }

        // now how about PUT/POST bodies? These override what we got from GET
        $body = file_get_contents($this->inpitStream);

        $content_type = false;
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];
        }

        // initialise json as default format
        $this->format = 'json';

        if(strpos($content_type, 'application/json') !== false) {
            $body_params = json_decode($body, true);
            if($body_params) {
                foreach($body_params AS $param_name=>$param_value) {
                    $parameters[$param_name] = $param_value;
                }
            }
            $this->format = 'json';
        }

        if(strpos($content_type, 'application/x-www-form-urlencoded') !== false) {
            parse_str($body, $postvars);
            foreach($postvars AS $field=>$value) {
                $parameters[$field] = $value;

            }
            $this->format = 'html';
        }

        $this->queryParameters = $parameters;
    }

    function isAJAX() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    function getHeaderWithName($headerName) {

        foreach($this->headers AS $name=>$value) {
            if(strtolower($headerName) == strtolower($name)) {
                return $value;
            }
        }

        return null;
    }
    public function getQueryParameters() {
        return $this->queryParameters;
    }

    public function getQueryParameter($name, $default = null) {
        return (isset($this->queryParameters[$name])) ? $this->queryParameters[$name] : $default;
    }

    public function redirectTo($url) {
        $server = $this->serverUrl();
        header('Location: '.$server.$url);
    }

    private function serverUrl() {

        if(isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        }
        else {
            $protocol = 'http';
        }

        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }

    function getUrlElements() {
        return $this->urlElements;
    }

    function getIsAjax() {
        return $this->isAjax;
    }

    function getHeaders() {
        return $this->headers;
    }

    function setUrlElements($urlElements) {
        $this->urlElements = $urlElements;
    }

    function setIsAjax($isAjax) {
        $this->isAjax = $isAjax;
    }

    function setHeaders($headers) {
        $this->headers = $headers;
    }

    public function getAllHeaders() {

        if(!function_exists('getallheaders') && !function_exists('Cuculcan\Core\getallheaders')) {
            function getallheaders() {
                $headers = [];
                foreach($_SERVER AS $name=>$value) {
                    if(substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
            return getallheaders();
        }

        return getallheaders();
    }

    function getRequestUri() {
        return $this->requestUri;
    }
}
