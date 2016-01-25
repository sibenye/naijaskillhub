<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CredentialTypes_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_credentialTypes($id = FALSE)
		{
		        if ($id === FALSE)
		        {
		                $query = $this->db->get('credentialTypes');
		                return $query->result_array();
		        }
		
		        $query = $this->db->get_where('credentialTypes', array('id' => $id));
		        return $query->row_array();
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
	