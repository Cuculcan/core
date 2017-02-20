<?php

include_once 'core/AView.php';

class Error404View extends AView
{
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName(){
        $this->templateName = '/views/errors/404.php';
    }

    protected function setTitle(){
        //$this->title = "404 Нет такого";
    }

    protected function setAdditionalCSS(){
        //array_push($this->additionalCSS, '/public/css/jquery.fancybox.css');
    }

    protected function setAdditionalJS(){
        //array_push($this->additionalJS, 'public/js/brands.js');
    }

    protected function setCustomHeaders(){
        array_push($this->customHeaders, 'HTTP/1.1 404 Not Found');
    }

}