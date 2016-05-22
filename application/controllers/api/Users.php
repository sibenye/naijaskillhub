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
		$this->load->model('UserAttributeValues_model');
		$this->load->model('UserCredentials_model');
    }
    
    function users_get()
    {
    	try {
    		$get_data = $this->get();
    		$user = $this->Users_model->get_user($get_data);
    		
    		$this->successResponse($user);
    	} catch (NSH_Exception $e) {
    		$this->errorResponse($e);
    	}
    }
    
    function attributes_get()
    {
    	try {
    		$userAttributes = $this->UserAttributeValues_model->get_userAttributes($this->get('id'));
    	
    		$this->successResponse($userAttributes);
    	} catch (NSH_Exception $e) {
    		$this->errorResponse($e);
    	}
    }
    
    function users_post()
	{	
		try {
			$post_data = $this->post();
			
    		$userObject = $this->Users_model->save_user($post_data);
     
    		$this->successResponse($userObject);
		} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}		
	}
	
	function attributes_post()
	{
		try {
			$post_data = $this->post();
			
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
				
			$this->UserAttributeValues_model->save_userAttributes($post_data);
			 
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	function credentials_post()
	{
		try {
			$post_data = $this->post();
				
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
		
			$this->UserCredentials_model->add_userCredential($post_data);
		
			$this->successResponse();
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
	
	function credentials_delete()
	{
		try {
			$delete_data = $this->query();
			if (!array_key_exists('id', $delete_data)){
				//if the user id is not in the post body get it from the request url
				$delete_data['id'] = $this->get('id');
			}
	
			$this->UserCredentials_model->delete_userCredential($delete_data);
	
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
}
	