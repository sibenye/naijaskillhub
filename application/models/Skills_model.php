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
			$result = NULL;
	        if (!$id && !$categoryId)
	        {
                $query = $this->db->get('skills');
				$result = $query->result_array();
	        }elseif ($id && $categoryId){
	        	$query = $this->db->get_where('skills', array('id' => $id, 'categoryId' => $categoryId));
				$result = $query->row_array();
	        }elseif ($categoryId){
	        	$query = $this->db->get_where('skills', array('categoryId' => $categoryId));
				$result = $query->result_array();
	        }else{
	        	$query = $this->db->get_where('skills', array('id' => $id));					
				$result = $query->row_array();
	        }
			
			if (!$result){
				$message = 'No skills found';
				show_resourceNotFound_exception($message);
			}	
			return $result;        
		}
		
		public function save_skill($post_data)
		{
			//validate post data
			$this->validation = new Skills_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				show_validation_exception($this->form_validation->error_array());
			}

			//ensure that the category Id exists
			$existingCategory = $this->db->get_where('skillCategories', array('id' => $post_data['categoryId']))->row_array();
			if(!$existingCategory || empty($existingCategory)){
				$error_message = "Category Id does not exist";
				show_validation_exception($error_message);
			}
			
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where('skills', array('name' => $name));
			$existingSkill = $query->row_array();
			
			if (!empty($post_data['id']))
	        {
	        	if ($existingSkill && $existingSkill['id'] !== $post_data['id']){
					//throw or return error
					$error_message = 'The name \''.$name.'\' is already in use';
					show_validation_exception($error_message);
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
				$error_message = 'The name \''.$name.'\' is already in use';
				show_validation_exception($error_message);
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
			$result = $this->db->delete('skills', array('id' => $id));
			
			if($result === FALSE)
	        {
	            show_nsh_exception('failed to delete skill');
	        }
		}
		
		
}