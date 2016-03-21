<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * This Class is an extension of the CodeIgniter Core Exceptions Class
 */
 
 class NSH_Exceptions extends CI_Exceptions {
 	
	private $api_error_template = 'error_api';
	private $exception_response = array(
		'message' => ''
		);
		
	function __construct()
    {
        parent::__construct();		
	}
	
	/**
	 * This is called when there is something
	 * wrong with the client's request.
	 */
 	public function show_validation_exception($message)
	{
		return $this->output_error($message, 400);		
	}
	
	/**
	 * Called when the resource requested for is not found.
	 */
	public function show_resourceNotFound_exception($message)
	{
		return $this->output_error($message, 404);
	}
	
	/**
	 * Called when a known or expected exception occurs
	 * apart from a validation or a resource not found exception
	 */
	public function show_nsh_exception($message)
	{
		return $this->output_error($message, 500);		
	}
	
	private function output_error($message, $status_code)
	{
		$this->exception_response['message'] = $message;
		
		$api_respone = $this->show_error('', json_encode($this->exception_response), $this->api_error_template, $status_code);
		return trim($api_respone, '<p/>');
	}
	
 }
