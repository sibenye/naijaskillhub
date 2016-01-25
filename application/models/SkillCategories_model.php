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
		        if ($id === FALSE)
		        {
		                $query = $this->db->get('skillCategories');
		                return $query->result_array();
		        }
		
		        $query = $this->db->get_where('skillCategories', array('id' => $id));
		        return $query->row_array();
		}
		
		public function save_skillCategory($post_data)
		{
			//validate post data
			$this->validation = new SkillCategories_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				$result['error'] = $this->form_validation->error_array();
				return $result;
			}
			
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where('skillCategories', array('name' => $name));
			$existingCategory = $query->row_array();
			
			if (!empty($post_data['id']))
	        {
	        	if ($existingCategory && $existingCategory['id'] !== $post_data['id']){
					//throw or return error
					$error = 'The name \''.$name.'\' is already in use';
					$result['error'] = $error;
					return $result;
				}
				
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update('skillCategories', $data, array('id' => $id));
			}
			
			if ($existingCategory)
			{
					//throw or return error
					$error = 'The name \''.$name.'\' is already in use';
					$result['error'] = $error;
					return $result;
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
			return $this->db->delete('skillCategories', array('id' => $id));
		}
		
		
}