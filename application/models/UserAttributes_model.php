<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/core/validations/UserAttributes_validation.php');
require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class UserAttributes_model extends CI_Model {
	
		private $validation;

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_userAttributes($id = FALSE)
		{
			$result = NULL;
			if ($id === FALSE)
	        {
                $query = $this->db->get('userAttributes');
                $result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where('userAttributes', array('id' => $id));				
		        $result = $query->row_array();	
	        }
			
			if (!$result){
				$message = 'No UserAttributes found';
				throw new NSH_ResourceNotFoundException($message);
			}
			
			return $result;	        	        
		}
		
		public function save_userAttribute($post_data)
		{		
			//validate post data
			$this->validation = new UserAttributes_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				throw new NSH_ValidationException($this->form_validation->error_array());
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
					$error_message = 'The name \''.$name.'\' is already in use';
					throw new NSH_ValidationException($error_message);
				}
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update('userAttributes', $data, array('id' => $id));
			}
			
			if ($existingUserAttribute)
			{
				//throw or return error
				$error_message = 'The name \''.$name.'\' is already in use';
				throw new NSH_ValidationException($error_message);
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
			$result = $this->db->delete('userAttributes', array('id' => $id));
			if($result === FALSE)
	        {
	        	$message = 'failed to delete userAttribute';
				throw new NSH_Exception($message);
	        }
		}
}