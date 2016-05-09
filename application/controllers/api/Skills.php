<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

/**
 * Skills Controller
 * api requests for skill resources are handled by this Controller. * 
 */

class Skills extends REST_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Skills_model');
    }
	
	/**
	  * Skills
	  */
     function skills_get()
	 {
	 	try {
	 		$skills = $this->Skills_model->get_skills($this->get('id'), $this->get('categoryId'));      
        
        	$this->response($skills, 200); 
	 	} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (NSH_ResourceNotFoundException $e){
    		show_resourceNotFound_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}	 
	 	
	 }
	 
	 function skills_post()
	 {
	 	try {
	 		$post_data = $this->post();
		
        	$this->Skills_model->save_skill($post_data);
         
        	$this->response(array('status' => 'success'));
	 	} catch (NSH_ValidationException $e){
    		show_validation_exception($e->getMessage());
    	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}	 	
	 }
	 
	 function skills_delete()
	 {
	 	try {
	 		$id = $this->delete('id');
    		$this->Skills_model->delete_skill($id);
         
        	$this->response(array('status' => 'success'));
	 	} catch (Exception $e){
    		show_nsh_exception($e->getMessage());
    	}	 	
	 }		
}

	