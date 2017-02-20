<?php

class ClassNotFoundException extends Exception
{
}

class InternalException extends Exception
{
}

class UnauthorizedException extends Exception
{
    public $isSowError;
    public $redirectUrl;
    public function __construct($redirectUrl="", $showError=false, $message=""){
        parent::__construct($message);
        $this->isSowError = $showError;
        $this->redirectUrl = $redirectUrl;
    }
}
class BadRequestException extends Exception
{
}

class ForbiddentException extends Exception
{
}

class RCError
{
    public function __construct($request)
    {
        $this->documentRoot = $_SERVER["DOCUMENT_ROOT"];
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
        switch ($type) {
            case "404":
                (new Error404View(['error' => $error]))->render();
                break;
            case "403":
                (new Error404View(['error' => $error]))->render();
                break;
            case "400":
                (new Error400View(['error' => $error]))->render();
                break;
            case "500":
                (new Error500View(['error' => $error]))->render();
                break;
            case "401":
                (new Error401View(['error' => $error]))->render();
                break;
        }
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