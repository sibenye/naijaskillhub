<?php

defined('BASEPATH') OR exit('No direct script access allowed');
 
 class NSH_Exception extends Exception {
 	
	protected $http_status;
		
	function __construct($message, $code = 500, Exception $previous = null)
    {
    	$this->http_status = $code;
        parent::__construct($message, $code, $previous);		
	}
	
	public function getStatusCode()
	{
		return $this->http_status;
	}
	
	public function getErrorMessage()
	{
		return array('status' => $this->http_status, 'message' => $this->getMessage());
	}
 }
