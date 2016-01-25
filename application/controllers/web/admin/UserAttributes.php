<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserAttributes extends CI_Controller {
    
    private $view_bag = array('success' => '', 'error' => '');
	public function __construct()
    {
        parent::__construct();
        $this->load->model('UserAttributes_model');
    }

	/**
	 * Index Page action for this controller.
	 *
	 * Maps to the following URL
	 * 		/index.php/admin/userAttributes
	 */
	public function index()
	{
		$this->view_bag['user_attributes'] = $this->UserAttributes_model->get_userAttributes();
		$this->view_bag['title'] = 'User Attributes';
        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/userAttributes/index', $this->view_bag);
	}
	
	/**
	 * Single attribute view page action
	 * Maps to /index.php/admin/userAttributes/:num
	 */
	public function view($id = NULL)
    {
		$this->view_bag['user_attribute'] = $this->UserAttributes_model->get_userAttributes($id);

        if (empty($this->view_bag['user_attribute']))
        {
                show_404();
        }

        $this->view_bag['title'] = 'User Attribute - '.$this->view_bag['user_attribute']['name'];
		$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/userAttributes/view', $this->view_bag);
    }
	
	/**
	 * Create action
	 * Maps to /index.php/admin/userAttributes/create
	 */
	public function create()
	{
	    $this->load->helper('form');
	
	    $this->view_bag['title'] = 'Create a User Attribute';
	
	    if (count($_POST) === 0)
	    {
	        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/UserAttributes/create', $this->view_bag);	
	    }
	    else
	    {
	        $post_data = $this->input->post();
	        $result = $this->UserAttributes_model->save_userAttribute($post_data);
			if($result['error']){
				$this->view_bag['error'] = $result['error'];
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/UserAttributes/create', $this->view_bag);	
			}else{
				$this->view_bag['success'] = 'UserAttribute was successfully created.';
				$this->index();
			}	        	
	    }
	}
	
	/**
	 * Edit action
	 * Maps to /index.php/admin/userAttributes/:num/edit
	 */
	public function edit($id = NULL)
	{
	    $this->load->helper('form');
	
	    if (count($_POST) === 0)
	    {
	    	$this->view_bag['user_attribute'] = $this->UserAttributes_model->get_userAttributes($id);
			
	        if (empty($this->view_bag['user_attribute']))
	        {
	                show_404();
	        }
			
			$this->view_bag['title'] = 'Edit User Attribute - '.$this->view_bag['user_attribute']['name'];
			
	        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/UserAttributes/edit', $this->view_bag);	
	    }
	    else
	    {
	    	$post_data = $this->input->post();
	        $result= $this->UserAttributes_model->save_userAttribute($post_data);
			
	        if($result['error']){
	        	$id = $this->input->post('id');
				$this->view_bag['user_attribute'] = $this->UserAttributes_model->get_userAttributes($id);
								
	        	$this->view_bag['title'] = 'Edit User Attribute - '.$this->view_bag['user_attribute']['name'];
				$this->view_bag['error'] = $result['error'];
				
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/UserAttributes/edit', $this->view_bag);	
			}else{
				$this->view_bag['success'] = 'UserAttribute was successfully updated.';
				$this->index();
			}	        
	    }
	}
	
	public function delete($id = NULL)
	{
	    $this->view_bag['user_attribute'] = $this->UserAttributes_model->get_userAttributes($id);

        if (empty($this->view_bag['user_attribute']))
        {
                show_404();
        }
		
		$this->UserAttributes_model->delete_userAttribute($id);
		$this->view_bag['success'] = 'UserAttribute was successfully deleted.';
		$this->index();
	}
}