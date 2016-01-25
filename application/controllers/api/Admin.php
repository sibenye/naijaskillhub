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

            if (! $userAttribute)
            {
               $this->response(array('message' => 'UserAttribute not found'), 404);
            }
            
            $this->response($userAttribute, 200); 
            
        }
        
        $userAttributes = $this->UserAttributes_model->get_userAttributes();      
        
        if($userAttributes)
        {
            $this->response($userAttributes, 200); 
        } 
        else
        {
            $this->response(array('message' => 'No UserAttributes found'), 404);
        }
    }
    
    function userAttributes_post()
    {
    	$post_data = $this->post();
				
        $result = $this->UserAttributes_model->save_userAttribute($post_data);
         
        if($result['error'])
        {
            $this->response(array('status' => 'failed', 'message' => $result['error']));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
    }
	
	function userAttributes_delete()
    {
    	$id = $this->delete('id');
    	$result = $this->UserAttributes_model->delete_userAttribute($id);
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
    }
	
	
	/**
	 * CredentialTypes
	 */
	 function credentialTypes_get()
    {
        if($this->get('id'))
        {
            $credentialType = $this->CredentialTypes_model->get_credentialTypes($this->get('id'));

            if (! $credentialType)
            {
               $this->response(array('message' => 'CredentialType not found'), 404);
            }
            
            $this->response($credentialType, 200); 
            
        }
        
        $credentialTypes = $this->CredentialTypes_model->get_credentialTypes();      
        
        if($credentialTypes)
        {
            $this->response($credentialTypes, 200); 
        } 
        else
        {
            $this->response(array('message' => 'No CredentialTypes found'), 404);
        }
    }
	
	/**
	 * Skill Categories
	 */
	 function skillCategories_get()
	 {
	 	if($this->get('id'))
        {
            $skillCategory = $this->SkillCategories_model->get_skillCategories($this->get('id'));

            if (! $skillCategory)
            {
               $this->response(array('message' => 'Category not found'), 404);
            }
            
            $this->response($skillCategory, 200); 
            
        }
        
        $skillCategories = $this->SkillCategories_model->get_skillCategories();      
        
        if($skillCategories)
        {
            $this->response($skillCategories, 200); 
        } 
        else
        {
            $this->response(array('message' => 'No Categories found'), 404);
        }
	 }
	 
	 function skillCategories_post()
	 {
	 	$post_data = $this->post();
		
        $result = $this->SkillCategories_model->save_skillCategory($post_data);
         
        if($result['error'])
        {
            $this->response(array('status' => 'failed', 'message' => $result['error']));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
	 }
	 
	 function skillCategories_delete()
	 {
	 	$id = $this->delete('id');
    	$result = $this->SkillCategories_model->delete_skillCategory($id);
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
	 }
	 
	 /**
	  * Skills
	  */
     function skills_get()
	 {
	 	$skills = $this->Skills_model->get_skills($this->get('id'), $this->get('categoryId'));      
        
        if($skills)
        {
            $this->response($skills, 200); 
        } 
        else
        {
            $this->response(array('message' => 'No Skills found'), 404);
        }
	 }
	 
	 function skills_post()
	 {
	 	$post_data = $this->post();
		
        $result = $this->Skills_model->save_skill($post_data);
         
        if($result['error'])
        {
            $this->response(array('status' => 'failed', 'message' => $result['error']));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
	 }
	 
	 function skills_delete()
	 {
	 	$id = $this->delete('id');
    	$result = $this->Skills_model->delete_skill($id);
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
	 }
}
    