<?php

namespace Example\Views;

use Cuculcan\Core\AView;

class Error500View extends AView
{
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName(){
        $this->templateName = '/errors/500.php';
    }

    protected function setTitle(){
        $this->title = "500 Internal server error";
    }

    protected function setAdditionalCSS(){

    }

    protected function setAdditionalJS(){
        //array_push($this->additionalJS, 'public/js/brands.js');
    }

    protected function setCustomHeaders(){
        array_push($this->customHeaders, 'HTTP/1.1 500 Internal server error');
    }

}