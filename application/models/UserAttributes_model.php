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
		
		public function get_userAttributes($id)
		{
			$result = NULL;
			if (empty($id))
	        {
                $query = $this->db->get(USERATTRIBUTES_TABLE);
                $result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where(USERATTRIBUTES_TABLE, array('id' => $id));				
		        $result = $query->row_array();	
	        }
			
			if (empty($result)){
				$message = 'No UserAttributes found';
				throw new NSH_ResourceNotFoundException(220, $message);
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
			if ($this->form_validation->error_array() || empty($post_data)){
				throw new NSH_ValidationException(110, $this->form_validation->error_array());
			}
		
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where(USERATTRIBUTES_TABLE, array('name' => $name));
			$existingUserAttribute = $query->row_array();
			
			$this->load->helper('date');
			$nowDate = mdate(DATE_TIME_STRING, time());
			
			$id = array_key_exists ( 'id', $post_data ) ? $post_data ['id'] : null;
			
			if (!empty($id))
	        {
	        	//ensure that the id is valid.
	        	if (empty($this->db->get_where(USERATTRIBUTES_TABLE, array('id' => $id))->row_array()))
	        	{
	        		$error_message = 'User Attribute Id does not exist';
	        		throw new NSH_ResourceNotFoundException(220, $error_message);
	        	}
	        	
	        	if ($existingUserAttribute && $existingUserAttribute['id'] !== $id)
	        	{
	        		//this means the name is already in use
					throw new NSH_ValidationException(119);
				}				
	        	
	        	$data = array(
	        			'name' => $post_data['name'], 
	        			'modifiedDate' => $nowDate	        			
	        	);
				return $this->db->update(USERATTRIBUTES_TABLE, $data, array('id' => $id));
			}
			
			if ($existingUserAttribute)
			{
				throw new NSH_ValidationException(119, $error_message);
			}
			
			$data = array(
		        'name' => $post_data['name'],
		        'createdDate' => $nowDate,
				'modifiedDate' => $nowDate
		    );
		
		    return $this->db->insert(USERATTRIBUTES_TABLE, $data);			
		}
		
		public function delete_userAttribute($id)
		{
			$result = $this->db->delete(USERATTRIBUTES_TABLE, array('id' => $id));
			if($result === FALSE)
	        {
	        	$message = 'failed to delete userAttribute';
				throw new NSH_Exception(100, $message);
	        }
		}
}