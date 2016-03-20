<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/validations/SkillCategories_validation.php');

class SkillCategories_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_skillCategories($id = FALSE)
		{
			$result = NULL;
			
	        if ($id === FALSE)
	        {
                $query = $this->db->get('skillCategories');
                $result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where('skillCategories', array('id' => $id));			
	        	$result = $query->row_array();
	        }
	
	        if (!$result){
				$message = 'No skillCategories found';
				show_resourceNotFound_exception($message);
			}
			
			return $result;
		}
		
		public function save_skillCategory($post_data)
		{
			//validate post data
			$this->validation = new SkillCategories_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				show_validation_exception($this->form_validation->error_array());
			}
			
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where('skillCategories', array('name' => $name));
			$existingCategory = $query->row_array();
			
			if (!empty($post_data['id']))
	        {
	        	if ($existingCategory && $existingCategory['id'] !== $post_data['id']){
					$error_message = 'The name \''.$name.'\' is already in use';
					show_validation_exception($error_message);
				}
				
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update('skillCategories', $data, array('id' => $id));
			}
			
			if ($existingCategory)
			{
				$error_message = 'The name \''.$name.'\' is already in use';
				show_validation_exception($error_message);
			}
		
		    $data = array(
		        'name' => $post_data['name']
		    );
		
		    return $this->db->insert('skillCategories', $data);
		}
		
		public function delete_skillCategory($id)
		{
			//all the skills in this category will also be deleted			
			$this->db->delete('skills', array('categoryId' => $id));
			$result = $this->db->delete('skillCategories', array('id' => $id));
			
			if($result === FALSE)
	        {
	        	show_nsh_exception('failed to delete skillCategory');
	        }
		}		
}
