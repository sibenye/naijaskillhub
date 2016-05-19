<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NSH_ResourceNotFoundException extends NSH_Exception {
		
	function __construct($errorCode, $errorDetail = '')
    {
        parent::__construct($errorCode, $errorDetail, 404);		
	}	
}
	