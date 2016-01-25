<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/validations/Skills_validation.php');

class Skills_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_skills($id = NULL, $categoryId = NULL)
		{
		        if (!$id && !$categoryId)
		        {
	                $query = $this->db->get('skills');
					return $query->result_array();
		        }elseif ($id && $categoryId){
		        	$query = $this->db->get_where('skills', array('id' => $id, 'categoryId' => $categoryId));
					return $query->row_array();
		        }elseif ($categoryId){
		        	$query = $this->db->get_where('skills', array('categoryId' => $categoryId));
					return $query->result_array();
		        }else{
		        	$query = $this->db->get_where('skills', array('id' => $id));
					return $query->row_array();
		        }		        
		}
		
		public function save_skill($post_data)
		{
			//validate post data
			$this->validation = new Skills_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				$result['error'] = $this->form_validation->error_array();
				return $result;
			}

			//ensure that the category Id exists
			$existingCategory = $this->db->get_where('skillCategories', array('id' => $post_data['categoryId']))->row_array();
			if(!$existingCategory || empty($existingCategory)){
				$result['error'] = "Category Id does not exist";
				return $result;
			}
			
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where('skills', array('name' => $name));
			$existingSkill = $query->row_array();
			
			if (!empty($post_data['id']))
	        {
	        	if ($existingSkill && $existingSkill['id'] !== $post_data['id']){
					//throw or return error
					$error = 'The name \''.$name.'\' is already in use';
					$result['error'] = $error;
					return $result;
				}
				
	        	$id = $post_data['id'];
	        	$data = array(
	        	'name' => $post_data['name'], 
	        	'categoryId' => $post_data['categoryId'],
		        'imageName' => empty($post_data['imageName'])? $existingSkill['imageName'] : $post_data['imageName']);
				
				return $this->db->update('skills', $data, array('id' => $id));
			}
			
			if ($existingSkill)
			{
					//throw or return error
					$error = 'The name \''.$name.'\' is already in use';
					$result['error'] = $error;
					return $result;
			}
			
			$this->load->helper('date');
			
			$datestring = '%Y/%m/%d %H:%i:%s';
			$time = time();
		
			$nowDate = mdate($datestring, $time);
		
		    $data = array(
		        'name' => $post_data['name'],
		        'categoryId' => $post_data['categoryId'],
		        'imageName' => empty($post_data['imageName'])? DEFAULT_IMAGE_NAME : $post_data['imageName'],
		        'createdDate' => $nowDate
		    );
		
		    return $this->db->insert('skills', $data);
		}
		
		public function delete_skill($id)
		{
			return $this->db->delete('skills', array('id' => $id));
		}
		
		
}