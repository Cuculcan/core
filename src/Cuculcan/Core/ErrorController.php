<?php
namespace Cuculcan\Core;

class ErrorController
{
    private $projectNamespace;
    
    public function __construct($request, $projectNamespace)
    {
        $this->projectNamespace = $projectNamespace."\\Views\\";
    }

    public function showErrorPage($type, $error, $isAjax = false)
    {
        if (!$isAjax) {
            $this->renderErrorView($type, $error);
            return;
        }

        $this->renderErrorJSON($type, $error);
    }

    protected function renderErrorView($type, $error)
    {
        ob_clean();
        $errorView = $this->projectNamespace."Error".$type."View";
        $view  = new $errorView(['error' => $error]);
        $view->render();
    }

    protected function renderErrorJSON($type, $error)
    {
        ob_clean();
        header('Content-Type: application/json');
        header('HTTP/1.1 '.$type);
        $err_resp = array(
            'error' => $type,
            'message' => $error
        );
        echo json_encode($err_resp);
    }

}