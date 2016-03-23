<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class UserAttributeValues_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_userAttributeValues($userId = NULL, $attributeId = NULL)
		{
			$result = NULL;
			if (!empty($userId) && empty($attributeId)){
				$query = $this->db->get_where('userAttributeValues', array('userId' => $userId));
	        	$result = $query->result_array();
			}elseif (empty($userId) && !empty($attributeId)){
				$query = $this->db->get_where('userAttributeValues', array('attributeId' => $attributeId));
	        	$result = $query->result_array();
			}elseif (!empty($userId) && !empty($attributeId)){
				$query = $this->db->get_where('userAttributeValues', array('userId' => $userId, 'attributeId' => $attributeId));
	        	$result = $query->row_array();
			}
			
			if (!$result){
				$message = 'No userAttributeValues found';
				throw new NSH_ResourceNotFoundException($message);
			}
			return $result;
		        
		}
		
		public function insert_userAttributeValue($post_data)
		{
			$data = array(
		        'userAttributeId' => $post_data['userAttributeId'],
		        'userId' => $post_data['userId'],
		        'attributeValue' => $post_data['attributeValue'],
		    );
		
		    return $this->db->insert('userAttributeValues', $data);
		}
		
		public function update_userAttributeValue($post_data)
		{
			$data = array(
		        'attributeValue' => $post_data['attributeValue'],
		    );
		
		    return $this->db->update('userAttributeValues', $data, array('attributeId' => $post_data['userAttributeId'], 'userId' => $post_data['userId']));
		}
		
		public function delete_userAttributeValue($attributeId, $userId)
		{		
		    $result = $this->db->delete('userAttributeValues', array('attributeId' => $attributeId, 'userId' => $userId));
			
			if($result === FALSE)
	        {
	        	$message = 'failed to delete userAttributeValue';
				throw new NSH_Exception($message);
	        }
		}		
}
	