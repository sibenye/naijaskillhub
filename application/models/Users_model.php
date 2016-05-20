<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/core/utilities/NSH_Utils.php');
require(APPPATH.'/core/validations/Users_creation_validation.php');
require(APPPATH.'/core/objects/user.php');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

use \YaLinqo\Enumerable;

class Users_model extends CI_Model {
	
		use NSH_Utils;
		
		public function __construct()
        {
                $this->load->database();
				$this->load->helper('date');
        }
        
        public function get_user($get_data)
        {
        	$result = array();
        	$searchData = array();
        	if (!empty($get_data['id']))
        	{
        		$searchData['id'] = $get_data['id'];
        	}
        	if (!empty($get_data['username']))
        	{
        		$searchData['username'] = $get_data['username'];
        	}
        	if (!empty($get_data['emailAddress']))
        	{
        		$searchData['emailAddress'] = $get_data['emailAddress'];
        	} 
        	
        	if (empty($searchData)) {
        		$error_message = 'Id, or username, or emailAddress is required';
        		throw new NSH_ValidationException(110, $error_message);        		
        	}
        	
        	$query = $this->db->get_where(USERS_TABLE, $searchData);
        	$result = $query->row_array();
        	
        	if (empty($result))
        	{
        		$error_message = 'User does not exist';
        		throw new NSH_ResourceNotFoundException(220, $error_message);
        	}
        	
        	$user = new User();
        	$user->userId = $result['id'];
        	$user->emailAddress = $result['emailAddress'];
        	$user->username = $result['username'];
        	$user->isActive = ($result['isActive'] == 1);
        	
        	$this->getCredentialTypes($user);
        	
        	$this->getAttributes($user);
        	
        	return $user;
        }
		
		public function create_user($post_data)
		{
			//validate post data
			$this->validation = new Users_creation_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array() || empty($post_data))
			{				
				throw new NSH_ValidationException(110, $this->form_validation->error_array());
			}
			
			if (!$this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)
					&& !$this->equalIgnorecase($post_data['credentialType'], FACEBOOK_CREDENTIALTYPE)
					&& !$this->equalIgnorecase($post_data['credentialType'], GOOGLE_CREDENTIALTYPE))
			{
				$error_message = 'CredentialType should be either STANDARD, FACEBOOK or GOOGLE';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			if ($this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)
					&& (!array_key_exists('password', $post_data) || empty($post_data['password'])))
			{
				$error_message = 'Password is required for STANDARD crendentialType';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			if (!$this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)
					&& (!array_key_exists('socialId', $post_data) || empty($post_data['socialId'])))
			{
				$error_message = 'SocialId is required for FACEBOOK or GOOGLE crendentialType';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			$user = $this->insert_user($post_data);
			//insert credential
			$this->save_userCredential($post_data, $user);
			
			return $user;
		}
		
		public function update_userName($post_data)
		{
			if (!array_key_exists('id', $post_data) || empty($post_data['id']))
			{
				$error_message = 'user Id is required';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			if (!array_key_exists('username', $post_data) || empty($post_data['username']))
			{
				$error_message = 'username is required';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			$userId = $post_data['id'];
			$username = $post_data['username'];
			
			//ensure that the user exists
			if (!$this->userExists($userId))
			{
				$error_message = "User does not exist";
				throw new NSH_ResourceNotFoundException(220, $error_message);
			}
			
			//ensure that the username is not in use
			if ($this->userNameInUse($username, $userId))
			{
				//return error that this username is not available
				throw new NSH_ValidationException(111);
			}
			
			$modifiedDate = mdate(DATE_TIME_STRING, time());
			
			$data = array('username' => $username, 'modifiedDate' => $modifiedDate);
			
			$this->db->update(USERS_TABLE, $data, array('id' => $userId));
			
		}
		
		public function update_emailAddress($post_data)
		{
			if (!array_key_exists('id', $post_data) || empty($post_data['id']))
			{
				$error_message = 'user Id is required';
				throw new NSH_ValidationException(110, $error_message);
			}
				
			if (!array_key_exists('emailAddress', $post_data) || empty($post_data['emailAddress']))
			{
				$error_message = 'emailAddress is required';
				throw new NSH_ValidationException(110, $error_message);
			}
				
			$userId = $post_data['id'];
			$emailAddress = $post_data['emailAddress'];
				
			//ensure that the user exists
			if (!$this->userExists($userId))
			{
				$error_message = "User does not exist";
				throw new NSH_ResourceNotFoundException(220, $error_message);
			}
				
			//ensure that the username is not in use
			if ($this->userEmailInUse($emailAddress, $userId))
			{
				//return error that this emailAddress is in use
				throw new NSH_ValidationException(112);
			}
				
			$modifiedDate = mdate(DATE_TIME_STRING, time());
				
			$data = array('emailAddress' => $emailAddress, 'modifiedDate' => $modifiedDate);
				
			$this->db->update(USERS_TABLE, $data, array('id' => $userId));
				
		}
		
		private function save_userCredential($post_data, $user)
		{
			$userId = $user->userId;
			$this->db->select('id,name');
			
			$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE,
			 array('name' => $post_data['credentialType']))->row_array();
			 
			$credentialTypeId = $credentialTypeResult['id'];
			$credentialTypeName = $credentialTypeResult['name'];
			
			$userCredentialsQueryResult = $this->db->get_where(USERCREDENTIALS_TABLE,
			 array('userId' => $userId, 'credentialTypeId' => $credentialTypeId))->row_array();
						
			if (empty($userCredentialsQueryResult))
			{
				$nowDate = mdate(DATE_TIME_STRING, time());
				$data = array(
				'userId' => $userId,
		        'credentialTypeId' => $credentialTypeId,
		        'createdDate' => $nowDate,
				'modifiedDate' => $nowDate
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
			
			$user->credentialTypes = array($credentialTypeName);			
		}
		
		private function insert_user($post_data)
		{
			//ensure that the email address is not in use
			$emailInUse = $this->userEmailInUse($post_data['emailAddress']);
			
			if ($emailInUse)
			{
				//return error that emailAddress is already in use
				throw new NSH_ValidationException(112);
			}			

			//ensure that the username/socialId is not in use
			if ($this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)
					&& $this->userNameInUse($post_data['username'])){
				//return error that this username is not available
				throw new NSH_ValidationException(111);
			}
			
			$nowDate = mdate(DATE_TIME_STRING, time());
			
			$data = array(
				'emailAddress' => $post_data['emailAddress'],
		        'username' => $post_data['username'],
		        'createdDate' => $nowDate,
				'modifiedDate' => $nowDate
		    );
			
			$this->db->insert(USERS_TABLE, $data);
			//retrieve the just created user
			$userQueryResult = $this->db->get_where(USERS_TABLE, array('emailAddress' => $post_data['emailAddress']))->row_array();
			
			$user = new User();
			$user->userId = $userQueryResult['id'];
			$user->emailAddress = $userQueryResult['emailAddress'];
			$user->username = $userQueryResult['username'];
			$user->isActive = ($userQueryResult['isActive'] == 1);
			
			return $user;		
		}
		
		private function userEmailInUse($email, $userId = NULL)
		{
			$query = $this->db->get_where(USERS_TABLE, array('emailAddress' => $email));
			$row = $query->row_array();
			if ($row && count($row) > 0){
				if ($userId == NULL)
				{
					return true;
				}
				else 
				{
					return $row['id'] != $userId;
				}	
			}
			
			return false;
		}
		
		private function userNameInUse($username, $userId = NULL)
		{
			$query = $this->db->get_where(USERS_TABLE, array('username' => $username));
			$row = $query->row_array();
			if ($row &&  count($row) > 0){
				if ($userId == NULL)
				{
					return true;
				}
				else 
				{
					return $row['id'] != $userId;
				}				
			}
			
			return false;
		}
		
		private function userExists($userId)
		{
			$existingUser = $this->db->get_where(USERS_TABLE, array('id' => $userId))->row_array();
			
			return ($existingUser && !empty($existingUser));
		}
		
		private function getCredentialTypes($user)
		{
			$credentialTypes = array();
			$userCredentialsQueryResults = $this->db->get_where(USERCREDENTIALS_TABLE, array('userId' => $user->userId))->result_array();
			
			foreach ($userCredentialsQueryResults as $key => $value) {
				$credentialTypeId = $userCredentialsQueryResults[$key]['credentialTypeId'];
				
				$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE, array('id' => $credentialTypeId))->row_array();
				
				$credentialTypes[$key] = $credentialTypeResult['name'];
			}
			
			$user->credentialTypes = $credentialTypes;
		}
		
		private function getAttributes($user)
		{
			$userId = $user->userId;
			$userAttributes = $this->db->get(USERATTRIBUTES_TABLE)->result_array();
			$userAttributeValues = $this->db->get_where(USERATTRIBUTEVALUES_TABLE, array('userId' => $userId))->result_array();
			
			$attributes = null;
			
			foreach ($userAttributeValues as $userAttributeValue) {
				$name = Enumerable::from($userAttributes)->where('$userAttr ==> $userAttr["id"] == $userAttributeValue["userAttributeId"]')['name'];
				$attributeValue = $userAttributeValue['attributeValue'];
				$attributes[$name] = $attributeValue;
			}
			
			$user->attributes = $attributes;
			
		}
}
	