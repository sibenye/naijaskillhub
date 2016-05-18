<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/core/validations/Categories_validation.php');
require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

/**
 * Model the Categories table
 * 
 * @author Silver
 *
 */
class Categories_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_categories($id = null, $parentId = null)
		{
			$result = NULL;
			
	        if (empty($id) && empty($parentId))
	        {
                $query = $this->db->get(CATEGORIES_TABLE);
                $result = $query->result_array();
	        } else if (empty($parentId)) {
	        	$query = $this->db->get_where(CATEGORIES_TABLE, array('id' => $id));			
	        	$result = $query->row_array();
	        } else if (empty($id)) {
	        	$query = $this->db->get_where(CATEGORIES_TABLE, array('parentId' => $parentId));
	        	$result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where(CATEGORIES_TABLE, array('id' => $id, 'parentId' => $parentId));
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
			$id = array_key_exists ( 'id', $post_data ) ? $post_data ['id'] : null;
			$parentId = array_key_exists ( 'parentId', $post_data ) ? $post_data ['parentId'] : null;
			$description = array_key_exists ( 'description', $post_data ) ? $post_data ['description'] : null;
			$imageUrl = array_key_exists ( 'imageUrl', $post_data ) ? $post_data ['imageUrl'] : null;
			
			//confirm that the parentId exists
			if (!empty($parentId)){
				$queryForParent = $this->db->get_where(CATEGORIES_TABLE, array('id' => $parentId));
				if (!$queryForParent->row_array()){
					$error_message = 'Parent Category does not exist';
					throw new NSH_ResourceNotFoundException($error_message);
				}
				
				//ensure that parentId is not equal to id
				if ($parentId == $id){
					$error_message = 'Parent CategoryId can not be the same as the CategoryId';
					throw new NSH_ValidationException($error_message);
				}
			}			
			
	        $query = $this->db->get_where(CATEGORIES_TABLE, array('name' => $name));
			$existingCategory = $query->row_array();
			
			if (!empty($id))
	        {
	        	if ($existingCategory && $existingCategory['id'] !== $post_data['id']){
					$error_message = 'The name \''.$name.'\' is already in use';
					throw new NSH_ValidationException($error_message);
				}
				
				$parentId = !empty($parentId) ? $parentId : $existingCategory['parentId'];
				$description = !empty($description)  ? $description : $existingCategory['description'];
				$imageUrl = !empty($imageUrl) ? $imageUrl : $existingCategory['imageUrl'];
				
	        	$data = array (
					'name' => $name,
					'parentId' => $parentId,
					'description' => $description,
					'imageUrl' => $imageUrl 
				);
				return $this->db->update(CATEGORIES_TABLE, $data, array('id' => $id));
			}
			
			if ($existingCategory)
			{
				$error_message = 'The name \''.$name.'\' is already in use';
				throw new NSH_ValidationException($error_message);
			}
		
		    $data = array(
		        'name' => $name,
				'parentId' => $parentId,
				'description' => $description,
				'imageUrl' => $imageUrl 
		    );
		
		    return $this->db->insert(CATEGORIES_TABLE, $data);
		}
		
		public function delete_category($id)
		{
			//all the portfolios in this category will also be deleted			
			$this->db->delete(PORTFOLIOS_TABLE, array('categoryId' => $id));
			
			//delete child categories
			$this->db->delete(CATEGORIES_TABLE, array('parentId' => $id));
			
			$result = $this->db->delete(CATEGORIES_TABLE, array('id' => $id));
			
			if($result === FALSE)
	        {
	        	throw new NSH_Exception('failed to delete skillCategory');
	        }
		}
		
		public function move_categories($newParentCategoryId, $oldParentCategoryId)
		{
			$data = array('parentId' => $newParentCategoryId);
			$this->db->update(CATEGORIES_TABLE, $data, array('parentId' => $oldParentCategoryId));
			
		}
}
