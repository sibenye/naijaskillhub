<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/api/NSH_Controller.php');

class Passwords extends NSH_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('UserCredentials_model');
    }
    
    function reset_post()
    {
    	try {
    		$post_data = $this->post();
    		    	
    		$this->UserCredentials_model->reset_password($post_data);
    	
    		$this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }
    
    function change_post()
    {
    	try {
    		$post_data = $this->post();
    			
    		$this->UserCredentials_model->change_password($post_data);
    		 
    		$this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }
}