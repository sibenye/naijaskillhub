<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

/**
 * Portfolios Controller
 * api requests for skill resources are handled by this Controller. * 
 */

class Portfolios extends REST_Controller {
	
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
        
        	$this->response($portfolios, 200); 
	 	} catch (NSH_Exception $e){
    		$this->response($e->getErrorMessage(), $e->getHttpStatusCode());
    	}
	 	
	 }
	 
	 function portfolios_post()
	 {
	 	try {
	 		$post_data = $this->post();
		
        	$this->Portfolios_model->save_portfolio($post_data);
         
        	$this->response(array('status' => 'success'));
	 	} catch (NSH_Exception $e){
    		$this->response($e->getErrorMessage(), $e->getHttpStatusCode());
    	} 	
	 }
	 
	 function portfolios_delete()
	 {
	 	try {
	 		$id = $this->delete('id');
    		$this->Portfolios_model->delete_portfolio($id);
         
        	$this->response(array('status' => 'success'));
	 	} catch (NSH_Exception $e){
    		$this->response($e->getErrorMessage(), $e->getHttpStatusCode());
    	}	 	
	 }		
}

	