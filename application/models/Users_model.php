<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/validations/Users_creation_validation.php');
require(APPPATH.'/validations/Users_update_validation.php');
require(APPPATH.'/objects/user.php');

class Users_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
				$this->load->helper('date');
        }
		
		public function create_user($post_data)
		{
			//validate post data
			$this->validation = new Users_creation_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array())
			{
				$result['error'] = $this->form_validation->error_array();
				//TODO: throw validation exception
			}
			
			$user = $this->insert_user($post_data);
		}
		
		public function upsert_userCredential($post_data, $userId)
		{
			$this->db->select('id');
			$credentialTypeResult = $this->db->get_where('ccredentialtypes', array('name' => $post_data['credentialType']))->row_array();
			$credentialTypeId = $credentialTypeResult['id'];
			$userCredentialsQueryResult = $this->db->get_where('usercredentials', array('userId' => $userId, 'credentialTypeId' => $credentialTypeId))->row_array();
			
			if (empty($userCredentialsQueryResult))
			{
				$data = array(
				'userId' => $userId,
		        'credentialTypeId' => $credentialTypeId,
		        'createdDate' => $nowDate
		    );
				if (STANDARD_CREDENTIALTYPE == $post_data['credentialType'])
				{
					
				}
				
			
			if ($post_data['isSearchable'])
			{
				$data['isSearchable'] = $post_data['isSearchable'];
			}
		
		    $this->db->insert('users', $data);
			}
			else
			{
				
			}
		}
		
		public function insert_user($post_data)
		{
			//ensure that the email address is not in use
			$emailInUse = $this->userEmailInUse($post_data['email']);
			
			if ($emailInUse)
			{				
				//TODO: error message should say the specific credential type to log in with.
				$result['error'] = "It appears you already have an account with us. Please log with your username/password or your facebook or google account";
				//TODO: throw validation exception
			}			

			//ensure that the username is not in use
			if ($post_data['credentialType'] == STANDARD_CREDENTIALTYPE && $this->userNameInUse($post_data['username'])){
				$result['error'] = "This username is not available";
				//TODO: throw validation exception
			}
			
			//ensure the roleId is valid
			$userRoleQueryResult = $this->db->get('userroles', array('id' => $post_data['userRoleId']))->row_array();
			if (empty($userRoleQueryResult))
			{
				//TODO: throw exception
			}
			
			$userRole = $userRoleQueryResult['name'];			
			
			$datestring = '%Y/%m/%d %H:%i:%s';
			$time = time();
		
			$nowDate = mdate($datestring, $time);
			
			$data = array(
				'email' => $post_data['email'],
		        'userRoleId' => $post_data['userRoleId'],
		        'createdDate' => $nowDate
		    );
			
			if ($post_data['isSearchable'])
			{
				$data['isSearchable'] = $post_data['isSearchable'];
			}
		
		    $this->db->insert('users', $data);
			//retrieve the just created userId
			$this->db->select('id', 'email', 'userRoleId', 'active', 'isSearchable');
			$userQueryResult = $this->db->get_where('users', array('email' => $post_data['email']))->row_array();
			
			$user = new User();
			$user->userId = $userQueryResult['id'];
			$user->email = $userQueryResult['email'];
			$user->isActive = $userQueryResult['isActive'];
			$user->isSearchable = $userQueryResult['isSearchable'];
			$user->userRole = $userRole;
			
			return $user;		
		}
		
		private function userEmailInUse($email)
		{
			$query = $this->db->get_where('users', array('email' => $email));
			$row = $query->row_array();
			if ($row && count($row) > 0){
				return true;
			}
			
			return false;
		}
		
		private function userNameInUse($username)
		{
			$query = $this->db->get_where('userCredentials', array('username' => $username));
			$row = $query->row_array();
			if ($row && count($row) > 0){
				return true;
			}
			
			return false;
		}
}
	