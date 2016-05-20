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
        	$user->id = $result['id'];
        	$user->emailAddress = $result['emailAddress'];
        	$user->username = $result['username'];
        	$user->isActive = ($result['isActive'] == 1);
        	
        	$this->getCredentialTypes($user);
        	
        	$this->getAttributes($user);
        	
        	return $user;
        }
		
		public function save_user($post_data)
		{
			$isNewUserCreation = (!array_key_exists('id', $post_data) || empty($post_data['id']));
			if ($isNewUserCreation
					&& !array_key_exists('credentialType', $post_data))
			{
				//default to STANDARD credentialType
				$post_data['credentialType'] = STANDARD_CREDENTIALTYPE;
			}
			
			//validate post data
			$this->validateUserPostData($post_data);
			
			$user = $this->upsert_user($post_data);
			//insert credential
			//Note that credentials will only be inserted if the user does not have that credentialType
			//If the user already has that credentialType it will do nothing.
			$this->save_userCredential($post_data, $user);			
			
			if (array_key_exists('attributes', $post_data)){
				$this->save_userAttributes($post_data['attributes'], $user);
			}			
			
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
		
		private function save_userAttributes($attributes, $user)
		{
			$userId = $user->id;
			$modifiedDate = mdate(DATE_TIME_STRING, time());
			
			$savedAttributes = array();
			if (empty($attributes))
			{
				$this->getAttributes($user);
				return;
			}
			
			foreach ($attributes as $key => $value) {
				$attributeId = $this->db->get_where(USERATTRIBUTES_TABLE, array('name' => $key))->row_array()['id'];
				if (!empty($this->db->get_where(USERATTRIBUTEVALUES_TABLE, array('userAttributeId' => $attributeId, 'userId' => $userId))->row_array()))
				{
					$data = array('attributeValue' => $value, 'modifiedDate' => $modifiedDate);
					$this->db->update(USERATTRIBUTEVALUES_TABLE, $data, array('userAttributeId' => $attributeId, 'userId' => $userId));
				}
				else {
					$data = array('attributeValue' => $value, 'userAttributeId' => $attributeId, 'userId' => $userId, 'createdDate' => $modifiedDate, 'modifiedDate' => $modifiedDate);
					$this->db->insert(USERATTRIBUTEVALUES_TABLE, $data);
				}
			}
			
			$this->getAttributes($user);
		}
		
		private function save_userCredential($post_data, $user)
		{
			$userId = $user->id;
			$credentialTypes = $this->db->get(CREDENTIALTYPES_TABLE)->result_array();
			
			if (empty($post_data['credentialType']))
			{				
				$this->getCredentialTypes($user);
				return;
			}
			
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
				
				if ($this->equalIgnorecase(STANDARD_CREDENTIALTYPE, $post_data['credentialType']))
				{
					$data['password'] = $post_data['password'];
				}
				else
				{
					$data['socialId'] = $post_data['socialId'];
				}
			
			    $this->db->insert(USERCREDENTIALS_TABLE, $data);
			}
			
			$this->getCredentialTypes($user);			
		}
		
		private function upsert_user($post_data)
		{
			$userId = null;
			if (array_key_exists('id', $post_data)
					&& !empty($post_data['id']))
			{
				$userId = $post_data['id'];
				
				if (!$this->userExists($userId)){
					$error_message = 'User does not exist';
					throw new NSH_ResourceNotFoundException(220, $error_message);
				}				
			}
			
			$modifiedDate = mdate(DATE_TIME_STRING, time());			
			$data = array();
			//ensure that the email address is not in use
			if (array_key_exists('emailAddress', $post_data) 
					&& !empty($post_data['emailAddress']))
			{
				$emailInUse = $this->userEmailInUse($post_data['emailAddress'], $userId);
					
				if ($emailInUse)
				{
					//return error that emailAddress is already in use
					throw new NSH_ValidationException(112);
				}
				
				$data['emailAddress'] = $post_data['emailAddress'];
			}					

			//ensure that the username is not in use
			if (array_key_exists('username', $post_data)
					&& !empty($post_data['username']))
			{
				if (array_key_exists('credentialType', $post_data)
						&& $this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)){
					
						if ($this->userNameInUse($post_data['username'], $userId))
						{
							//return error that this username is not available
							throw new NSH_ValidationException(111);
						}
						
						$data['username'] = $post_data['username'];							
				}
			}
			
			$userQueryResult = array();
			if (!empty($userId))
			{				
				$data['modifiedDate'] = $modifiedDate;
				$this->db->update(USERS_TABLE, $data, array('id' => $userId));
				//retrieve the updated user
				$userQueryResult = $this->db->get_where(USERS_TABLE, array('id' => $userId))->row_array();
			}
			else 
			{
				$data['modifiedDate'] = $modifiedDate;
				$data['createdDate'] = $modifiedDate;
				
				$this->db->insert(USERS_TABLE, $data);
				//retrieve the just created user
				$userQueryResult = $this->db->get_where(USERS_TABLE, array('emailAddress' => $post_data['emailAddress']))->row_array();
			}
			
			$user = new User();
			$user->id = $userQueryResult['id'];
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
		
		private function getAttributes($user)
		{
			$userId = $user->id;
			
			$attributes = null;
			
			$this->db->select(USERATTRIBUTEVALUES_TABLE.'.attributeValue,'.USERATTRIBUTES_TABLE.'.name');
			$this->db->from(USERATTRIBUTEVALUES_TABLE);
			$this->db->join(USERATTRIBUTES_TABLE, USERATTRIBUTES_TABLE.'.id = '.USERATTRIBUTEVALUES_TABLE.'.userAttributeId');
			$this->db->where('userId', $userId);
			$userAttributes = $this->db->get()->result_array();
			
			foreach ($userAttributes as $userAttribute) {
				$attributes[$userAttribute['name']] = $userAttribute['attributeValue'];
			}
			$user->attributes = $attributes;			
		}
		
		private function getCredentialTypes($user)
		{
			$userId = $user->id;
			$this->db->select('credentialTypes.name');
			$this->db->from(USERCREDENTIALS_TABLE);
			$this->db->join(CREDENTIALTYPES_TABLE, CREDENTIALTYPES_TABLE.'.id = '.USERCREDENTIALS_TABLE.'.credentialTypeId');
			$this->db->where('userId', $userId);
			$userCredentialTypes = $this->db->get()->result_array();
			if (!empty($userCredentialTypes)){
					
				$userCredentialTypes = Enumerable::from($userCredentialTypes)->select('$credType ==> $credType["name"]')->toArray();
			}
			$user->credentialTypes = $userCredentialTypes;
		}
		
		private function validateAttributes($attributes)
		{
			if (empty($attributes))
			{
				return;
			}
			
			$invalidAttributes = array();
			
			$i = 0;			
			foreach ($attributes as $key => $value) {
				if (empty($this->db->get_where(USERATTRIBUTES_TABLE, array('name' => $key))->row_array())){
					$invalidAttributes[$i] = $key;
					++$i;
				}
			}
			
			if (!empty($invalidAttributes)){
				throw new NSH_ValidationException(120, $invalidAttributes);
			}
		}
		
		private function validateUserPostData($post_data)
		{
			$isNewUserCreation = (!array_key_exists('id', $post_data) || empty($post_data['id']));
			if ($isNewUserCreation 
					&& (!array_key_exists('emailAddress', $post_data) || empty($post_data['emailAddress'])))
			{
				$error_message = 'EmailAddress is required for new User creation';
				throw new NSH_ValidationException(110, $error_message);
				
			}
			
			if ($isNewUserCreation
					&& (!array_key_exists('username', $post_data) || empty($post_data['username'])))
			{
				$error_message = 'username is required for new User creation';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			if (array_key_exists('credentialType', $post_data))
			{
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
					$error_message = 'password is required for STANDARD crendentialType';
					throw new NSH_ValidationException(110, $error_message);
				}
				
				if (!$this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)
						&& (!array_key_exists('socialId', $post_data) || empty($post_data['socialId'])))
				{
					$error_message = 'SocialId is required for FACEBOOK or GOOGLE crendentialType';
					throw new NSH_ValidationException(110, $error_message);
				}
			}
			
			if (array_key_exists('attributes', $post_data))
			{
				$this->validateAttributes($post_data['attributes']);
			}
			
		}
}
	