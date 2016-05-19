<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/api/NSH_Controller.php');

/**
 * Admin Controller
 * api requests for admin resources are handled by this Controller. * 
 */

class Admin extends NSH_Controller {
    
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
			$this->successResponse($userAttributes);
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }
    
    function userAttributes_post()
    {
    	try {
    		$post_data = $this->post();
			
	        $this->UserAttributes_model->save_userAttribute($post_data);
	         
	        $this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}			
    }
	
	function userAttributes_delete()
    {
    	try{
    		$id = $this->delete('id');
	    	$this->UserAttributes_model->delete_userAttribute($id);
	         
	        $this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
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
	        
	        $this->successResponse($credentialTypes);
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
        
    }
	
	/**
	 * Skill Categories
	 */
	 function categories_get()
	 {
	 	try {
	 			        
	        $categories = $this->Categories_model->get_categories($this->get('id'), $this->get('parentId'));      
        
        	$this->successResponse($categories); 
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}	
	 }
	 
	 function categories_post()
	 {
	 	try {
	 		$post_data = $this->post();
		
        	$this->Categories_model->save_category($post_data);
         
        	$this->successResponse();
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
	 	
	 }
	 
	 function categories_delete()
	 {
	 	try {
	 		$id = $this->delete('id');
    		$this->Categories_model->delete_category($id);
        	 
        	$this->successResponse();
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}		 	
	 }	 
}
    