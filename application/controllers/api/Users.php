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
	
	function users_post(){
		$post_data = $this->post();
				
        $result = $this->Users_model->create_user($post_data);
         
        if($result['error'])
        {
            $this->response(array('status' => 'failed', 'message' => $result['error']));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
	}
}
	