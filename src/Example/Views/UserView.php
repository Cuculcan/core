<?php
namespace Example\Views;

use Cuculcan\Core\AView;

class UserView extends AView
{
    public function __construct($model) {
        parent::__construct();
        $this->model = $model;
    }

    protected function setTemplateName(){
        $this->templateName = '/../src/Example/views/templates/user.php';
    }

    protected function setTitle(){
        $this->title = "";
    }

    protected function setKeywords()    {
        $this->keywords="";
    }

    protected function setDescription(){
        $this->description="";
    }

    protected function setAdditionalCSS(){

    }

    protected function setAdditionalJS(){

    }

    protected function setCustomHeaders(){
        //array_push($this->customHeaders, "MY-PEW-HEADER: pew-pew-pew");
    }

}