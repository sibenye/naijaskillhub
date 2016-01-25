<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once (BASEPATH.'vendor/autoloader.php');
use \YaLinqo\Enumerable;

class Skills extends CI_Controller {
		
	private $view_bag = array('success' => '', 'error' => '');
	public function __construct()
    {
        parent::__construct();
        $this->load->model('SkillCategories_model');
		$this->load->model('Skills_model');
    }
	
	/**
	 * Index Page action for this controller.
	 *
	 * Maps to the following URL
	 * 		/index.php/admin/skills
	 */
	public function index()
	{
		$skillCategories = $this->SkillCategories_model->get_skillCategories();
		$skills = $this->Skills_model->get_skills();
		
		$skillsByCategory = Enumerable::from($skillCategories)
	    ->orderBy('$cat ==> $cat["name"]')
	    ->groupJoin(
	        from($skills)
	            ->orderBy('$skill ==> $skill["name"]'),
	        '$cat ==> $cat["id"]', '$skill ==> $skill["categoryId"]',
	        '($cat, $sks) ==> array(
	            "name" => $cat["name"],
	            "skills" => $sks
	        )'
	    );
		$this->view_bag['skillsByCategory'] = $skillsByCategory;
		$this->view_bag['title'] = 'Skills';
        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/index', $this->view_bag);
	}	
	
	
	/**
	 * Create action
	 * Maps to /index.php/admin/skills/
	 */
	public function create()
	{
	    $this->load->helper('form');
	
	    $this->view_bag['title'] = 'Create a Skill';
	
	    if (count($_POST) === 0)
	    {
	    	$this->view_bag['skillCategories'] = $this->SkillCategories_model->get_skillCategories();
	        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/create', $this->view_bag);	
	    }
	    else
	    {
	    	$upload_data = $this->upload_image('image_filename');
			if(array_key_exists('error', $upload_data)){
				$this->view_bag['error'] = $upload_data['error'];
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/create', $this->view_bag);
			}

	        $post_data = $this->input->post();
			$post_data['imageName'] = $upload_data['file_name'];
	        $result = $this->Skills_model->save_skill($post_data);
			if($result['error']){
				$this->view_bag['error'] = $result['error'];
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/create', $this->view_bag);	
			}else{
				$this->view_bag['success'] = 'Skill was successfully created.';
				$this->index();
			}	        	
	    }
	}
	
	/**
	 * Edit action
	 * Maps to /index.php/admin/skills/:num/edit
	 */
	public function edit($id = NULL, $categoryId = NULL)
	{
	    $this->load->helper('form');
	
	    if (count($_POST) === 0)
	    {
	    	$this->view_bag['skill'] = $this->Skills_model->get_skills($id, $categoryId);
			
	        if (empty($this->view_bag['skill']))
	        {
	                show_404();
	        }
			
			$this->view_bag['skillCategories'] = $this->SkillCategories_model->get_skillCategories();
			
			$this->view_bag['title'] = 'Edit Skill - '.$this->view_bag['skill']['name'];
			
	        $this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/edit', $this->view_bag);	
	    }
	    else
	    {
	    	$upload_data = $this->upload_image('image_filename');
			if(array_key_exists('error', $upload_data)){
				$id = $this->input->post('id');
				$this->view_bag['skill'] = $this->Skills_model->get_skills($id, $categoryId);
								
	        	$this->view_bag['title'] = 'Edit Skill - '.$this->view_bag['skill']['name'];
				$this->view_bag['error'] = $upload_data['error'];
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/edit', $this->view_bag);
			}
			
	       	$post_data = $this->input->post();
			$post_data['imageName'] = $upload_data['file_name']? $upload_data['file_name'] : $post_data['imageName'];
	        $result= $this->Skills_model->save_skill($post_data);
			
	        if($result['error']){
	        	$id = $this->input->post('id');
				$this->view_bag['skill'] = $this->Skills_model->get_skills($id, $categoryId);
								
	        	$this->view_bag['title'] = 'Edit Skill - '.$this->view_bag['skill']['name'];
				$this->view_bag['error'] = $result['error'];
				
				$this->template->load(ADMIN_TEMPLATE_NAME, 'admin/skills/edit', $this->view_bag);	
			}else{
				$this->view_bag['success'] = 'Skill was successfully updated.';
				$this->index();
			}	        
	    }
	}

	public function delete($id = NULL)
	{
	    $this->view_bag['skill'] = $this->Skills_model->get_skills($id);

        if (empty($this->view_bag['skill']))
        {
                show_404();
        }
		
		$this->Skills_model->delete_skill($id);
		$this->view_bag['success'] = 'Skill was successfully deleted.';
		$this->index();
	}
	
	private function upload_image($fieldName)
    {
    	$file = $_FILES[$fieldName];
    	if (!isset($file) || empty($file['name'])){
    		return array('file_name' => NULL);
    	}
		
        $config['upload_path']          = './uploads/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 1000; //1MB

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload($fieldName))
        {
            $error = array('error' => $this->upload->display_errors());

            return $data['error'] = $error;
        }
        else
        {
            $data = $this->upload->data();

            return $data;
        }
    }
}
	