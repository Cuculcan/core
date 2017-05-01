<?php

namespace Example\Services;

class MyAop {

    //put your code here
    public function init($param) {

        //var_dump(__DIR__ . '/../src/');
        
        // Initialize an application aspect container
        $applicationAspectKernel = ApplicationAspectKernel::getInstance();
        $applicationAspectKernel->init(array(
            'debug' => true, // Use 'false' for production mode
            // Cache directory
            'cacheDir' => $_SERVER["DOCUMENT_ROOT"] . '/cache/', // Adjust this path if needed
            // Include paths restricts the directories where aspects should be applied, or empty for all source files
            'includePaths' => array(
                $_SERVER["DOCUMENT_ROOT"]. '/src/Cuculcan/Core/',
                $_SERVER["DOCUMENT_ROOT"]. '/Example/Services/'
            )
        ));
        
        return $applicationAspectKernel;
       
    }

}
