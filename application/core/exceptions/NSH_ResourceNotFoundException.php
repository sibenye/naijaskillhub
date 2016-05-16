<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NSH_ResourceNotFoundException extends NSH_Exception {
		
	function __construct($message='')
    {
        parent::__construct($message, 404);		
	}	
}
	