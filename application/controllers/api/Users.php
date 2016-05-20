<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/api/NSH_Controller.php');

/**
 * Users Controller
 * api requests for user resources are handled by this Controller. * 
 */

class Users extends NSH_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }
	
	function users_post()
	{	
		try {
			$post_data = $this->post();
			
    		$userObject = $this->Users_model->create_user($post_data);
     
    		$this->successResponse($userObject);
		} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}		
	}
	
	function changeEmailAddress_post()
	{
		try {
			$post_data = $this->post();
		if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
				
			$this->Users_model->update_emailAddress($post_data);
			 
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	function changeUsername_post()
	{
		try {
			$post_data = $this->post();
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
	
			$this->Users_model->update_userName($post_data);
	
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
}
	