<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NSH_ResourceNotFoundException extends NSH_Exception {
		
	function __construct($message='')
    {
        parent::__construct($message, 404);		
	}
	
	/**
	 * Called when the resource requested for is not found.
	 */
	public function show_resourceNotFound_exception($message)
	{
		return $this->output_error($message, 404);
	}
}
	