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
        $this->templateName = '/Example/Views/templates/main.php';
    }

    //---------------------------------блок для SEO --------------------------------
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
        array_push($this->additionalCSS, 'Example/web/css/main.css');

    }

    protected function setAdditionalJS(){
        array_push($this->additionalJS, 'Example/web/js/main.js');
    }

    protected function setCustomHeaders(){
        //array_push($this->customHeaders, "MY-PEW-HEADER: pew-pew-pew");
    }

}