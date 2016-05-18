<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/core/validations/Categories_validation.php');
require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class Categories_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_categories($id = FALSE)
		{
			$result = NULL;
			
	        if ($id === FALSE)
	        {
                $query = $this->db->get(CATEGORIES_TABLE);
                $result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where(CATEGORIES_TABLE, array('id' => $id));			
	        	$result = $query->row_array();
	        }
	
	        if (!$result){
				$message = 'No categories found';
				throw new NSH_ResourceNotFoundException($message);
			}
			
			return $result;
		}
		
		public function save_category($post_data)
		{
			//validate post data
			$this->validation = new Categories_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				throw new NSH_ValidationException($this->form_validation->error_array());
			}
			
			//ensure that the name does not belong to another
			$name = $post_data['name'];
	        $query = $this->db->get_where(CATEGORIES_TABLE, array('name' => $name));
			$existingCategory = $query->row_array();
			
			if (!empty($post_data['id']))
	        {
	        	if ($existingCategory && $existingCategory['id'] !== $post_data['id']){
					$error_message = 'The name \''.$name.'\' is already in use';
					show_validation_exception($error_message);
				}
				
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update(CATEGORIES_TABLE, $data, array('id' => $id));
			}
			
			if ($existingCategory)
			{
				$error_message = 'The name \''.$name.'\' is already in use';
				show_validation_exception($error_message);
			}
		
		    $data = array(
		        'name' => $post_data['name']
		    );
		
		    return $this->db->insert(CATEGORIES_TABLE, $data);
		}
		
		public function delete_category($id)
		{
			//all the portfolios in this category will also be deleted			
			$this->db->delete('portfolios', array('categoryId' => $id));
			$result = $this->db->delete(CATEGORIES_TABLE, array('id' => $id));
			
			if($result === FALSE)
	        {
	        	throw new NSH_Exception('failed to delete skillCategory');
	        }
		}		
}
