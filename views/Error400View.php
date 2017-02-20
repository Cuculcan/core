<?php

include_once 'core/AView.php';

class Error400View extends AView
{
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName(){
        $this->templateName = '/views/errors/400.php';
    }

    protected function setTitle(){
        $this->title = "400 Bad request";
    }

    protected function setAdditionalCSS(){

    }

    protected function setAdditionalJS(){
        //array_push($this->additionalJS, 'public/js/brands.js');
    }

    protected function setCustomHeaders(){
        array_push($this->customHeaders, 'HTTP/1.1 400 Bad request');
    }

}