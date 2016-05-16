<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NSH_ValidationException extends NSH_Exception {
		
	function __construct($message='')
    {
        parent::__construct(json_encode($message), 400);		
	}	
}
	