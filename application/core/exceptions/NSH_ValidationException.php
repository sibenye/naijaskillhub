<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NSH_ValidationException extends NSH_Exception {
		
	function __construct($errorCode, $errorDetail = '')
    {
        parent::__construct($errorCode, json_encode($errorDetail), 400);		
	}	
}
	