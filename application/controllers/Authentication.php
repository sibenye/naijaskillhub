<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');

/**
 * Authentication Controller
 * api requests for handling user authentication and authorization *
 */

class Authentication extends NSH_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Authentication_model');
	}
	
	function login_post()
	{
		try {
			$post_data = $this->post();
					
			$response = $this->Authentication_model->login($post_data);
		
			$this->successResponse($response);
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
}