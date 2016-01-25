<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserRoles_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_userRoles($id = FALSE)
		{
		        if ($id === FALSE)
		        {
		                $query = $this->db->get('userRoles');
		                return $query->result_array();
		        }
		
		        $query = $this->db->get_where('userRoles', array('id' => $id));
		        return $query->row_array();
		}
		
		public function save_userRole($post_data)
		{
			if (!empty($post_data['id']))
	        {
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update('userRoles', $data, array('id' => $id));
			}
			
			$data = array(
		        'name' => $post_data['name']
		    );
		
		    return $this->db->insert('userRoles', $data);
		}
}
	