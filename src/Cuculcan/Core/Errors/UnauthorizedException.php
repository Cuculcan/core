<?php


namespace Cuculcan\Core\Errors;

class UnauthorizedException extends \Exception
{
    public $isSowError;
    public $redirectUrl;
    public function __construct($redirectUrl="", $showError=false, $message=""){
        parent::__construct($message);
        $this->isSowError = $showError;
        $this->redirectUrl = $redirectUrl;
    }
}