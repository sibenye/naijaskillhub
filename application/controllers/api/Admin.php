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
		$this->load->model('SkillCategories_model');
		$this->load->model('Skills_model');
    }
	
	/**
	 * UserAttributes 
	 */
        
    function userAttributes_get()
    {	
		if($this->get('id'))
        {
            $userAttribute = $this->UserAttributes_model->get_userAttributes($this->get('id'));

            $this->response($userAttribute, 200);             
        }
        
        $userAttributes = $this->UserAttributes_model->get_userAttributes(); 
		$this->response($userAttributes, 200);
    }
    
    function userAttributes_post()
    {	
		$post_data = $this->post();
			
        $this->UserAttributes_model->save_userAttribute($post_data);
         
        $this->response(array('status' => 'success'));	
    }
	
	function userAttributes_delete()
    {
    	$id = $this->delete('id');
    	$this->UserAttributes_model->delete_userAttribute($id);
         
        $this->response(array('status' => 'success'));
    }
	
	
	/**
	 * CredentialTypes
	 */
	 function credentialTypes_get()
    {
        if($this->get('id'))
        {
            $credentialType = $this->CredentialTypes_model->get_credentialTypes($this->get('id'));
            
            $this->response($credentialType, 200);            
        }
        
        $credentialTypes = $this->CredentialTypes_model->get_credentialTypes();      
        
        $this->response($credentialTypes, 200);
    }
	
	/**
	 * Skill Categories
	 */
	 function skillCategories_get()
	 {
	 	if($this->get('id'))
        {
            $skillCategory = $this->SkillCategories_model->get_skillCategories($this->get('id'));

            $this->response($skillCategory, 200);            
        }
        
        $skillCategories = $this->SkillCategories_model->get_skillCategories();      
        
        $this->response($skillCategories, 200); 
	 }
	 
	 function skillCategories_post()
	 {
	 	$post_data = $this->post();
		
        $this->SkillCategories_model->save_skillCategory($post_data);
         
        $this->response(array('status' => 'success'));
	 }
	 
	 function skillCategories_delete()
	 {
	 	$id = $this->delete('id');
    	$this->SkillCategories_model->delete_skillCategory($id);
         
        $this->response(array('status' => 'success'));
	 }
	 
	 /**
	  * Skills
	  */
     function skills_get()
	 {
	 	$skills = $this->Skills_model->get_skills($this->get('id'), $this->get('categoryId'));      
        
        $this->response($skills, 200); 
	 }
	 
	 function skills_post()
	 {
	 	$post_data = $this->post();
		
        $this->Skills_model->save_skill($post_data);
         
        $this->response(array('status' => 'success'));
	 }
	 
	 function skills_delete()
	 {
	 	$id = $this->delete('id');
    	$this->Skills_model->delete_skill($id);
         
        $this->response(array('status' => 'success'));
	 }
}
    