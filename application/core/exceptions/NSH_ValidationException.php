<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NSH_ValidationException extends NSH_Exception {
		
	function __construct($message='')
    {
        parent::__construct(json_encode($message, 400));		
	}
	
	/**
	 * This is called when there is something
	 * wrong with the client's request.
	 */
 	public function show_validation_exception($message)
	{
		$message_obj = $this->isJson($message) ? json_decode($message) : $message;
		return $this->output_error($message_obj, 400);		
	}
	
	private function isJson($string) {
	 	json_decode($string);
	 	return (json_last_error() == JSON_ERROR_NONE);
	}
}
	