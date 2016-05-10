<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SkillCategories extends CI_Controller {
		
	private $view_bag = array('success' => '', 'error' => '');
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Categories_model');
    }
	
	/**
	 * Index Page action for this controller.
	 *
	 * Maps to the following URL
	 * 		/index.php/admin/skillCategories
	 */
	public function index()
	{
		$this->view_bag['skillCategories'] = $this->Categories_model->get_categories();
		$this->view_bag['title'] = 'Skill Categories';
        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skillCategories/index', $this->view_bag);
	}
	
	/**
	 * Single attribute view page action
	 * Maps to /index.php/admin/skillCategories/:num
	 */
	public function view($id = NULL)
    {
		$this->view_bag['skillCategory'] = $this->Categories_model->get_categories($id);

        if (empty($this->view_bag['skillCategory']))
        {
                show_404();
        }
		
		$this->load->model('Portfolios_model');
		//get the skills under this category
		$this->view_bag['skills'] = $this->Portfolios_model->get_portfolios(null, $id);
		

        $this->view_bag['title'] = 'Skill Category - '.$this->view_bag['skillCategory']['name'];
		$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skillCategories/view', $this->view_bag);
    }
	
	/**
	 * Create action
	 * Maps to /index.php/admin/skillCategories/
	 */
	public function create()
	{
	    $this->load->helper('form');
	
	    $this->view_bag['title'] = 'Create a Skill Category';
	
	    if (count($_POST) === 0)
	    {
	        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skillCategories/create', $this->view_bag);	
	    }
	    else
	    {
	        $post_data = $this->input->post();
	        $result = $this->Categories_model->save_category($post_data);
			if($result['error']){
				$this->view_bag['error'] = $result['error'];
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skillCategories/create', $this->view_bag);	
			}else{
				$this->view_bag['success'] = 'Skill Category was successfully created.';
				$this->index();
			}	        	
	    }
	}
	
	/**
	 * Edit action
	 * Maps to /index.php/admin/skillCategories/:num/edit
	 */
	public function edit($id = NULL)
	{
	    $this->load->helper('form');
	
	    if (count($_POST) === 0)
	    {
	    	$this->view_bag['skillCategory'] = $this->Categories_model->get_categories($id);
			
	        if (empty($this->view_bag['skillCategory']))
	        {
	                show_404();
	        }
			
			$this->view_bag['title'] = 'Edit Skill Category - '.$this->view_bag['skillCategory']['name'];
			
	        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skillCategories/edit', $this->view_bag);	
	    }
	    else
	    {
	    	$post_data = $this->input->post();
	        $result= $this->Categories_model->save_category($post_data);
			
	        if($result['error']){
	        	$id = $this->input->post('id');
				$this->view_bag['skillCategory'] = $this->Categories_model->get_categories($id);
								
	        	$this->view_bag['title'] = 'Edit Skill Category - '.$this->view_bag['skillCategory']['name'];
				$this->view_bag['error'] = $result['error'];
				
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skillCategories/edit', $this->view_bag);	
			}else{
				$this->view_bag['success'] = 'Skill Category was successfully updated.';
				$this->index();
			}	        
	    }
	}

	public function delete($id = NULL)
	{
	    $this->view_bag['skillCategory'] = $this->Categories_model->get_categories($id);

        if (empty($this->view_bag['skillCategory']))
        {
                show_404();
        }
		
		$this->Categories_model->delete_category($id);
		$this->view_bag['success'] = 'Skill Category was successfully deleted.';
		$this->index();
	}
}
	