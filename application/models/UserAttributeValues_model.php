<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserAttributeValues_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_userAttributeValues($userId = NULL, $attributeId = NULL)
		{
			if (!empty($userId) && empty($attributeId)){
				$query = $this->db->get_where('userAttributeValues', array('userId' => $userId));
	        	return $query->result_array();
			}
			
			if (empty($userId) && !empty($attributeId)){
				$query = $this->db->get_where('userAttributeValues', array('attributeId' => $attributeId));
	        	return $query->result_array();
			}
			
			if (!empty($userId) && !empty($attributeId)){
				$query = $this->db->get_where('userAttributeValues', array('userId' => $userId, 'attributeId' => $attributeId));
	        	return $query->row_array();
			}
		        
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
		    return $this->db->delete('userAttributeValues', array('attributeId' => $attributeId, 'userId' => $userId));
		}		
}
	