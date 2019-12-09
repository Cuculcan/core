<?php

namespace Example\Views;

use Cuculcan\Core\AView;

class MainView extends AView
{
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName()
    {
        $this->templateName = '/main.php';
    }

    //--------------------------------- SEO --------------------------------
    protected function setTitle(){
        $this->title = "";
    }

    protected function setKeywords()    {
        $this->keywords="";
    }

    protected function setDescription(){
        $this->description="";
    }
    //----------------------------------------------------------------------------
    
    protected function setAdditionalCSS(){
        array_push($this->additionalCSS, '/css/main.css');

    }

    protected function setAdditionalJS(){
        array_push($this->additionalJS, '/js/main.js');
    }

    protected function setCustomHeaders(){
        array_push($this->customHeaders, "MY-PEW-HEADER: pew-pew-pew");
    }

}