<?php
namespace Cuculcan\Core;

abstract class AView
{
    protected $documentRoot;
    protected $templateName;
    protected $model;
    protected $additionalJS;
    protected $additionalCSS;
    protected $customHeaders;
    protected $title;
    protected $keywords;
    protected $description;
    protected $language;


    public function __construct() {
        $this->documentRoot = $_SERVER["DOCUMENT_ROOT"];
        $this->model = [];
        $this->additionalJS = [];
        $this->additionalCSS = [];
        $this->customHeaders = [];
        $this->title="";
        $this->keywords = "";
        $this->description="";
        $this->language="";
    }

    /**
     * @return array
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    //Обязательные функции
    abstract protected function setTemplateName();

    abstract protected function setAdditionalCSS();

    abstract protected function setAdditionalJS();

    abstract protected function setCustomHeaders();

    abstract protected function setTitle();

    //Не обязательные функции
    protected function setKeywords(){
    }

    protected function setDescription(){
    }

    protected function setLanguage(){
    }

    public function render(){

        $this->setCustomHeaders();
        foreach($this->customHeaders as $customHeader){
            header($customHeader);
        }

        $this->setTitle();
        $this->setKeywords();
        $this->setDescription();
        $this->setAdditionalCSS();
        $this->setAdditionalJS();
        $this->setTemplateName();
        $this->setLanguage();

        $this->import($this->templateName);
    }

    public function import($templateName, $values = [])
    {
        foreach($values AS $key=>$value) {
            $$key = $value;
        }

        global $config;
        include $this->documentRoot.$config['common']['template_path'].$templateName;
    }

    public function renderText($templateName, $values = [])
    {
        ob_start();
        $this->import($templateName, $values);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}