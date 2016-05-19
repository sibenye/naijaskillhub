<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once (APPPATH.'/config/errorCodes.php');
 
 class NSH_Exception extends Exception {
 	
	protected $http_status;
	protected $error_detail;
		
	function __construct($code, $errorDetail, $httpStatus = 500, Exception $previous = null)
    {
    	$this->http_status = $httpStatus;
    	$this->error_detail = $errorDetail;
    	$message = errorCodes::$codes[$code];
        parent::__construct($message, $code, $previous);		
	}
	
	public function getHttpStatusCode()
	{
		return $this->http_status;
	}
	
	public function getErrorDetail()
	{
		return $this->error_detail;
	}
	
	public function getErrorMessage()
	{
		return array('status' => $this->getCode(), 'message' => $this->getMessage(), 'errordetail' => $this->error_detail);
	}
 }
