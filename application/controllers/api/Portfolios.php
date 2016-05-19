<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/api/NSH_Controller.php');

/**
 * Portfolios Controller
 * api requests for skill resources are handled by this Controller. * 
 */

class Portfolios extends NSH_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Portfolios_model');
    }
	
	/**
	  * Portfolios
	  */
     function portfolios_get()
	 {
	 	try {
	 		$portfolios = $this->Portfolios_model->get_portfolios($this->get('id'), $this->get('categoryId'));      
        
        	$this->successResponse($portfolios); 
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
	 	
	 }
	 
	 function portfolios_post()
	 {
	 	try {
	 		$post_data = $this->post();
		
        	$this->Portfolios_model->save_portfolio($post_data);
         
        	$this->successResponse();
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	} 	
	 }
	 
	 function portfolios_delete()
	 {
	 	try {
	 		$id = $this->delete('id');
    		$this->Portfolios_model->delete_portfolio($id);
         
        	$this->successResponse();
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}	 	
	 }		
}

	