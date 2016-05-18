<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/core/validations/Users_creation_validation.php');
require(APPPATH.'/core/validations/Users_update_validation.php');
require(APPPATH.'/core/objects/user.php');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

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
				throw new NSH_ValidationException($this->form_validation->error_array());
			}
			
			$user = $this->insert_user($post_data);
			//insert credential
			$this->upsert_userCredential($post_data, $user->userId);
		}
		
		public function upsert_userCredential($post_data, $userId)
		{
			$this->db->select('id');
			
			$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE,
			 array('name' => $post_data['credentialType']))->row_array();
			 
			$credentialTypeId = $credentialTypeResult['id'];
			
			$userCredentialsQueryResult = $this->db->get_where(USERCREDENTIALS_TABLE,
			 array('userId' => $userId, 'credentialTypeId' => $credentialTypeId))->row_array();
						
			if (empty($userCredentialsQueryResult))
			{
				$nowDate = mdate(DATE_TIME_STRING, time());
				$data = array(
				'userId' => $userId,
		        'credentialTypeId' => $credentialTypeId,
		        'createdDate' => $nowDate
		    	);
				
				if (STANDARD_CREDENTIALTYPE == $post_data['credentialType'])
				{
					$data['password'] = $post_data['password'];
				}
				else
				{
					$data['socialId'] = $post_data['socialId'];
				}
			
			    $this->db->insert(USERCREDENTIALS_TABLE, $data);
			}
		}
		
		private function insert_user($post_data)
		{
			//ensure that the email address is not in use
			$emailInUse = $this->userEmailInUse($post_data['email']);
			
			if ($emailInUse)
			{
				$error_message = "It appears you already have an account with us. Please log with your username/password or your facebook or google account";
				throw new NSH_ValidationException($error_message);
			}			

			//ensure that the username/socialId is not in use
			if ($post_data['credentialType'] == STANDARD_CREDENTIALTYPE && $this->userNameInUse($post_data['username'])){
				$error_message = "This username is not available";
				throw new NSH_ValidationException($error_message);
			}
			
			$nowDate = mdate(DATE_TIME_STRING, time());
			
			$data = array(
				'email' => $post_data['email'],
		        'username' => $post_data['username'],
		        'createdDate' => $nowDate
		    );
			
			if (array_key_exists('firstName', $post_data))
			{
				$data['firstName'] = $post_data['firstName'];
			}
			
			if (array_key_exists('lastName', $post_data))
			{
				$data['lastName'] = $post_data['lastName'];
			}
		
		    $this->db->insert(USERS_TABLE, $data);
			//retrieve the just created user
			$userQueryResult = $this->db->get_where(USERS_TABLE, array('email' => $post_data['email']))->row_array();
			
			$user = new User();
			$user->userId = $userQueryResult['id'];
			$user->email = $userQueryResult['email'];
			$user->isActive = $userQueryResult['isActive'];
			$user->firstName = $userQueryResult['firstName'];
			$user->lastName = $userQueryResult['lastName'];;
			
			return $user;		
		}
		
		private function userEmailInUse($email)
		{
			$query = $this->db->get_where(USERS_TABLE, array('email' => $email));
			$row = $query->row_array();
			if ($row && count($row) > 0){
				return true;
			}
			
			return false;
		}
		
		private function userNameInUse($username)
		{
			$query = $this->db->get_where(USERS_TABLE, array('username' => $username));
			$row = $query->row_array();
			if ($row && count($row) > 0){
				return true;
			}
			
			return false;
		}
}
	