<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

/**
 * Users Controller
 * api requests for user resources are handled by this Controller. * 
 */

class Users extends REST_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }
	
	function users_post()
	{	
		try {
			$post_data = $this->post();
			
    		$this->Users_model->create_user($post_data);
     
    		$this->response(array('status' => 'success'));
		} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}				
	}
}
	