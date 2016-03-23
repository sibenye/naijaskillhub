<?php

defined('BASEPATH') OR exit('No direct script access allowed');
 
 class NSH_Exception extends Exception {
 	
	private $api_error_template = 'error_api';
	private $exception_response = array(
		'message' => ''
		);
		
	function __construct($message = null, $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);		
	}	
	
	/**
	 * Called when a known or expected exception occurs
	 * apart from a validation or a resource not found exception
	 */
	public function show_nsh_exception($message)
	{
		return $this->output_error($message, 500);		
	}
	
	protected function output_error($message, $status_code)
	{
		$this->exception_response['message'] = $message;
		$_error =& load_class('Exceptions', 'core');
		$response_str = json_encode($this->exception_response);
		$api_respone = $_error->show_error('', $response_str, $this->api_error_template, $status_code);
		return trim($api_respone, '<p/>');
	}
	
 }
