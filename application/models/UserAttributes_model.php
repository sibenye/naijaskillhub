<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/validations/UserAttributes_validation.php');

class UserAttributes_model extends CI_Model {
	
		private $validation;

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_userAttributes($id = FALSE)
		{
		        if ($id === FALSE)
		        {
		                $query = $this->db->get('userAttributes');
		                return $query->result_array();
		        }
		
		        $query = $this->db->get_where('userAttributes', array('id' => $id));
		        return $query->row_array();
		}
		
		public function save_userAttribute($post_data)
		{
			//validate post data
			$this->validation = new UserAttributes_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				$result['error'] = $this->form_validation->error_array();
				return $result;
			}
		
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where('userAttributes', array('name' => $name));
			$existingUserAttribute = $query->row_array();
			
			if (!empty($post_data['id']))
	        {
	        	if ($existingUserAttribute && $existingUserAttribute['id'] !== $post_data['id'])
	        	{
					//throw or return error
					$error = 'The name \''.$name.'\' is already in use';
					$result['error'] = $error;
					return $result;
				}
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update('userAttributes', $data, array('id' => $id));
			}
			
			if ($existingUserAttribute)
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
		        'createdDate' => $nowDate
		    );
		
		    return $this->db->insert('userAttributes', $data);
		}
		
		public function delete_userAttribute($id)
		{
			return $this->db->delete('userAttributes', array('id' => $id));
		}
}