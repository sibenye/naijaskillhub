<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CredentialTypes_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_credentialTypes($id = FALSE)
		{
			$result = NULL;
	        if ($id === FALSE)
	        {
                $query = $this->db->get('credentialTypes');
                $result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where('credentialTypes', array('id' => $id));			
	        	$result = $query->row_array();
	        }
	
	        if (!$result){
				$message = 'No CredentialTypes found';
				show_resourceNotFound_exception($message);
			}
			
			return $result;
		}
		
		public function save_credentialType($post_data)
		{
			if (!empty($post_data['id']))
	        {
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update('credentialTypes', $data, array('id' => $id));
			}
			
			$data = array(
		        'name' => $post_data['name']
		    );
		
		    return $this->db->insert('credentialTypes', $data);
		}
}
	