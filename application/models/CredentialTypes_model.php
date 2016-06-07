<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class CredentialTypes_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_credentialTypes($id)
		{
			$result = NULL;
	        if (empty($id))
	        {
                $query = $this->db->get(CREDENTIALTYPES_TABLE);
                $result = $query->result_array();
	        } else {
	        	$query = $this->db->get_where(CREDENTIALTYPES_TABLE, array('id' => $id));			
	        	$result = $query->row_array();
	        }
	
	        if (empty($result)){
				$message = 'No CredentialTypes found';
				throw new NSH_ResourceNotFoundException(220, $message);
			}
			
			return $result;
		}
		
		public function save_credentialType($post_data)
		{
			if (!empty($post_data['id']))
	        {
	        	$id = $post_data['id'];
	        	$data = array('name' => $post_data['name']);
				return $this->db->update(CREDENTIALTYPES_TABLE, $data, array('id' => $id));
			}
			
			$data = array(
		        'name' => $post_data['name']
		    );
		
		    return $this->db->insert(CREDENTIALTYPES_TABLE, $data);
		}
}
	