<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

/**
 * Admin Controller
 * api requests for admin resources are handled by this Controller. * 
 */

class Admin extends REST_Controller {
    
    private $validation;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserAttributes_model');
		$this->load->model('CredentialTypes_model');
		$this->load->model('Categories_model');
    }
	
	/**
	 * UserAttributes 
	 */
        
    function userAttributes_get()
    {
    	try {
    		if($this->get('id'))
	        {
	            $userAttribute = $this->UserAttributes_model->get_userAttributes($this->get('id'));
	
	            $this->response($userAttribute, 200);             
	        }
	        
	        $userAttributes = $this->UserAttributes_model->get_userAttributes(); 
			$this->response($userAttributes, 200);
    	} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (NSH_ResourceNotFoundException $e){
    		show_resourceNotFound_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}		
    }
    
    function userAttributes_post()
    {
    	try {
    		$post_data = $this->post();
			
	        $this->UserAttributes_model->save_userAttribute($post_data);
	         
	        $this->response(array('status' => 'success'));
    	} catch (NSH_Exception $e){
    		show_nsh_exception($e->getMessage());
    	}			
    }
	
	function userAttributes_delete()
    {
    	try{
    		$id = $this->delete('id');
	    	$this->UserAttributes_model->delete_userAttribute($id);
	         
	        $this->response(array('status' => 'success'));
    	} catch (NSH_Exception $e){
    		show_nsh_exception($e->getMessage());
    	}    	
    }
	
	
	/**
	 * CredentialTypes
	 */
	 function credentialTypes_get()
    {
    	try {
    		if($this->get('id'))
	        {
	            $credentialType = $this->CredentialTypes_model->get_credentialTypes($this->get('id'));
	            
	            $this->response($credentialType, 200);            
	        }
	        
	        $credentialTypes = $this->CredentialTypes_model->get_credentialTypes();      
	        
	        $this->response($credentialTypes, 200);
    	} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (NSH_ResourceNotFoundException $e){
    		show_resourceNotFound_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}	
        
    }
	
	/**
	 * Skill Categories
	 */
	 function categories_get()
	 {
	 	try {
	 		if($this->get('id'))
	        {
	            $category = $this->Categories_model->get_categories($this->get('id'));
	
	            $this->response($category, 200);            
	        }
	        
	        $categories = $this->Categories_model->get_categories();      
        
        	$this->response($categories, 200); 
	 	} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (NSH_ResourceNotFoundException $e){
    		show_resourceNotFound_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}	 	
	 }
	 
	 function categories_post()
	 {
	 	try {
	 		$post_data = $this->post();
		
        	$this->Categories_model->save_category($post_data);
         
        	$this->response(array('status' => 'success'));
	 	} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}	
	 	
	 }
	 
	 function categories_delete()
	 {
	 	try {
	 		$id = $this->delete('id');
    		$this->Categories_model->delete_category($id);
        	 
        	$this->response(array('status' => 'success'));
	 	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}		 	
	 }	 
}
    