<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');
require_once(APPPATH.'/core/security/NSH_CryptoService.php');
require_once(APPPATH.'/core/utilities/NSH_Utils.php');
require_once(APPPATH.'/core/validations/UserAttributes_validation.php');
require_once(APPPATH.'/core/validations/Password_validation.php');

use \YaLinqo\Enumerable;

class UserCredentials_model extends CI_Model
{
	use NSH_Utils;
	use NSH_CryptoService;
	
	const resetKey_delimiter = '<>';
	
	public function __construct()
	{
		$this->load->database();
		$this->load->helper('date');
		$this->load->model('Users_model');
	}
	
	public function add_userCredential($post_data)
	{
		$userId = $post_data['id'];
		
		$user = $this->Users_model->get_user(array('id' => $userId));
		
		$this->validatePostData($post_data);
		
		$credentialType = $post_data['credentialType'];
		
		if (!empty($user->credentialTypes))
		{
			$matchingCredTypes = Enumerable::from($user->credentialTypes)->where('$credType ==> strtolower($credType) == strtolower("'.$credentialType.'")')->toArray();
			if (!empty($matchingCredTypes))
			{
				throw new NSH_ValidationException(114);
			}			
		}				
		
		$this->save_userCredential($post_data, $userId);
	}
	
	public function save_userCredential($post_data, $userId)
	{
		
		if (empty($post_data['credentialType']))
		{				
			return $this->getCredentialTypes($userId);
		}
		
		$this->db->select('id,name');
		
		$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE,
		 array('name' => $post_data['credentialType']))->row_array();
		 
		$credentialTypeId = $credentialTypeResult['id'];
		$credentialTypeName = $credentialTypeResult['name'];
		
		
					
		if ($this->equalIgnorecase(STANDARD_CREDENTIALTYPE, $post_data['credentialType']))
		{
			$userCredentialsQueryResult = $this->db->get_where(USERSTANDARDCREDENTIALS_TABLE,
					array('userId' => $userId, 'credentialTypeId' => $credentialTypeId))->row_array();
			
			if (empty($userCredentialsQueryResult))
			{
				$nowDate = mdate(DATE_TIME_STRING, time());
				$passwordHash = $this->secure_hash($post_data['password']);
				$data = array(
						'userId' => $userId,
						'credentialTypeId' => $credentialTypeId,
						'password' => $passwordHash,
						'createdDate' => $nowDate,
						'modifiedDate' => $nowDate
						);
				
				$this->db->insert(USERSTANDARDCREDENTIALS_TABLE, $data);
			}
		}
		else
		{
		$userCredentialsQueryResult = $this->db->get_where(USERGENERICCREDENTIALS_TABLE,
					array('userId' => $userId, 'credentialTypeId' => $credentialTypeId))->row_array();
			
			if (empty($userCredentialsQueryResult))
			{
				$nowDate = mdate(DATE_TIME_STRING, time());
				$data = array(
						'userId' => $userId,
						'credentialTypeId' => $credentialTypeId,
						'socialId' => $post_data['socialId'],
						'createdDate' => $nowDate,
						'modifiedDate' => $nowDate
				);
				
				$this->db->insert(USERGENERICCREDENTIALS_TABLE, $data);
			}
		}
		
		return $this->getCredentialTypes($userId);			
	}
	
	public function getCredentialTypes($userId)
	{
		//$userId = $user->id;
		$this->db->select('credentialTypes.name');
		$this->db->from(USERSTANDARDCREDENTIALS_TABLE);
		$this->db->join(CREDENTIALTYPES_TABLE, CREDENTIALTYPES_TABLE.'.id = '.USERSTANDARDCREDENTIALS_TABLE.'.credentialTypeId');
		$this->db->where('userId', $userId);
		$query1 = $this->db->get_compiled_select();
			
		$this->db->select('credentialTypes.name');
		$this->db->from(USERGENERICCREDENTIALS_TABLE);
		$this->db->join(CREDENTIALTYPES_TABLE, CREDENTIALTYPES_TABLE.'.id = '.USERGENERICCREDENTIALS_TABLE.'.credentialTypeId');
		$this->db->where('userId', $userId);
		$query2 = $this->db->get_compiled_select();
			
		$query = $this->db->query($query1." UNION ".$query2);
			
		$userCredentialTypes = array();
		$result = $query->result_array();
		if (!empty($result)){
				
			$userCredentialTypes = Enumerable::from($result)->select('$credType ==> $credType["name"]')->toArray();
		}
		return $userCredentialTypes;
	}
	
	public function change_password($post_data)
	{
		if (!array_key_exists('newPassword', $post_data) || empty($post_data['newPassword']))
		{
			$error_message = 'newPassword is required';
			throw new NSH_ValidationException(110, $error_message);
		}
			
		if ((!array_key_exists('resetToken', $post_data) || empty($post_data['resetToken']))
				&& (!array_key_exists('oldPassword', $post_data) || empty($post_data['oldPassword'])))
		{
			$error_message = 'oldPassword or resetToken is required';
			throw new NSH_ValidationException(110, $error_message);
		}
			
		if (array_key_exists('resetToken', $post_data)
				&& array_key_exists('oldPassword', $post_data))
		{
			$error_message = 'oldPassword and resetToken are mutually exclusive. Only one should be provided';
			throw new NSH_ValidationException(110, $error_message);
		}
			
		if ((array_key_exists('oldPassword', $post_data) && !empty($post_data['oldPassword']))
				&& (!array_key_exists('userId', $post_data) || empty($post_data['userId'])))
		{
			$error_message = 'userId is required when oldPassword is provided';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		if (array_key_exists('oldPassword', $post_data) && $post_data['oldPassword'] == $post_data['newPassword'])
		{
			//new password cannot be thesame as old password
			throw new NSH_ValidationException(116);
		}
			
		$userId = NULL;
		$isNewStdCred = false;
		$credentialTypeId = NULL;
			
		//validate new password
		$newPassword = $post_data['newPassword'];
			
		Password_validation::validate($newPassword);
			
		if (array_key_exists('resetToken', $post_data)
				&& !empty($post_data['resetToken']))
		{
			$resetKeyEncoded = $post_data['resetToken'];
			//decrypt the encoded resetKey
			$resetKeyDecoded = $this->decode($resetKeyEncoded);
			//split the resetKey and get the resetToken
			list($resetDateStr, $emailAddress, $resetToken) =  explode(self::resetKey_delimiter, $resetKeyDecoded);
			//verify resetToken has not expired
			$nowDate = new DateTime();
			$resetDate = new DateTime($resetDateStr);
			$elaspedTime = $nowDate->diff($resetDate)->i; //get the difference in minutes
			$tokenLiveSpan = $this->config->item('token_live_span');
			if ($elaspedTime > $tokenLiveSpan)
			{
				throw new NSH_ValidationException(121);
			}
			//verify resetToken
			$this->db->select('id,resetToken');
			$existingUser = $this->db->get_where(USERS_TABLE, array('emailAddress' => $emailAddress))->row_array();
			
			if (empty($existingUser) || empty($existingUser['resetToken']))
			{
				throw new NSH_ValidationException(122);
			}
			
			$resetTokenHash = $existingUser['resetToken'];
			if (!$this->is_verified($resetToken, $resetTokenHash))
			{
				throw new NSH_ValidationException(122);
			}
			
			$userId = $existingUser['id'];
			
			$existingStdCred = $this->db->get_where(USERSTANDARDCREDENTIALS_TABLE, array('userId' => $userId))->row_array();
			
			if (empty($existingStdCred))
			{
				$isNewStdCred = true;
				$this->db->select('id');				
				$credentialTypeId = $this->db->get_where(CREDENTIALTYPES_TABLE,
						array('name' => $post_data['credentialType']))->row_array()['id'];
			}
	
		}
			
		if (array_key_exists('oldPassword', $post_data)
				&& !empty($post_data['oldPassword']))
		{
			//check user exists
			$userId = $post_data['userId'];
			$existingUser = $this->db->get_where(USERS_TABLE, array('id' => $userId))->row_array();
			if (empty($existingUser))
			{
				$error_message = 'User does not exist';
				throw new NSH_ResourceNotFoundException(220, $error_message);
			}
			//get existing password hash
			$this->db->select('password');
			$result = $this->db->get_where(USERSTANDARDCREDENTIALS_TABLE, array('userId' => $userId))->row_array();
	
			if (empty($result))
			{
				throw new NSH_ResourceNotFoundException(222);
			}
	
			//verify old password
			$existingPwdHash = $result['password'];
			$oldPassword = $post_data['oldPassword'];
	
			if (!$this->is_verified($oldPassword, $existingPwdHash))
			{
				throw new NSH_ValidationException(115);
			}
		}
		
		$modifiedDate = mdate(DATE_TIME_STRING, time());
		$passwordHash = $this->secure_hash($newPassword);
		
		if (!$isNewStdCred)
		{
			//update the password
			
			$data = array(
					'password' => $passwordHash,
					'modifiedDate' => $modifiedDate
			);
			$this->db->update(USERSTANDARDCREDENTIALS_TABLE, $data, array('userId' => $userId));
		}
		else {
			$data = array(
					'userId' => $userId,
					'credentialTypeId' => $credentialTypeId,
					'password' => $passwordHash,
					'createdDate' => $modifiedDate,
					'modifiedDate' => $modifiedDate
			);
			
			$this->db->insert(USERSTANDARDCREDENTIALS_TABLE, $data);
		}
		
		//clear the resetToken
		$this->db->update(USERS_TABLE, array('resetToken' => NULL, 'modifiedDate' => $modifiedDate), array('id' => $userId));
		
	}
	
	public function reset_password($post_data)
	{
		//email address is required
		if (!array_key_exists('emailAddress', $post_data) || empty($post_data['emailAddress']))
		{
			$error_message = 'emailAddress is required';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		$emailAddress = $post_data['emailAddress'];
		//verify user exists with this email address
		$existingUser = $this->db->get_where(USERS_TABLE, array('emailAddress' => $emailAddress))->row_array();
		if (empty($existingUser))
		{
			//if email doesn't exist just return without throwing.
			return;
		}
		
		$userId = $existingUser['id'];
		
		//generate random string
		$resetToken = $this->secure_random();
		//hash the random string and save in database
		$resetTokenHash = $this->secure_hash($resetToken);
		
		$this->db->update(USERS_TABLE, array('resetToken' => $resetTokenHash), array('id' => $userId));
		
		//concat user's email + today's datetime + the resetToken and encrypt it
		$nowDate = mdate(DATE_TIME_STRING, time());
		
		$resetKey = $nowDate.self::resetKey_delimiter.$emailAddress.self::resetKey_delimiter.$resetToken;
		
		$resetKeyEncoded = $this->encode($resetKey);
		
		//TODO build reset url and send a reset password email
		return;
	}
	
	public function delete_userCredential($delete_data)
	{
		$userId = empty($delete_data['id']) ? NULL : $delete_data['id'];
		
		$user = $this->Users_model->get_user(array('id' => $userId));
		
		if (!array_key_exists('credentialType', $delete_data) || empty($delete_data['credentialType']))
		{
			$error_message = 'credentialType is required';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		$credentialTypeToBeDeleted = strtolower($delete_data['credentialType']);
		
		if ($this->equalIgnorecase(STANDARD_CREDENTIALTYPE, $credentialTypeToBeDeleted))
		{
			throw new NSH_ValidationException(223);
		}
		
		$existingCredentialTypes = Enumerable::from($user->credentialTypes)->select('$credType ==> strtolower($credType)')->toArray();
		
		if (!in_array($credentialTypeToBeDeleted, $existingCredentialTypes))
		{
			throw new NSH_ValidationException(225);
		}
		
		if (count($existingCredentialTypes) == 1)
		{
			throw new NSH_ValidationException(224);
		}
		
		$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE,
				array('name' => $delete_data['credentialType']))->row_array();
					
		$credentialTypeId = $credentialTypeResult['id'];
		
		$this->db->delete(USERGENERICCREDENTIALS_TABLE, array('userId' => $userId, 'credentialTypeId' => $credentialTypeId));		
		
	}
	
	private function validatePostData($post_data)
	{
		if (!array_key_exists('credentialType', $post_data) || empty($post_data['credentialType']))
		{
			$error_message = 'credentialType is required';
			throw new NSH_ValidationException(110, $error_message);
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
			$error_message = 'password is required for STANDARD crendentialType';
			throw new NSH_ValidationException(110, $error_message);
		}
	
		if (!$this->equalIgnorecase($post_data['credentialType'], STANDARD_CREDENTIALTYPE)
				&& (!array_key_exists('socialId', $post_data) || empty($post_data['socialId'])))
		{
			$error_message = 'SocialId is required for FACEBOOK or GOOGLE crendentialType';
			throw new NSH_ValidationException(110, $error_message);
		}
	
		if (array_key_exists('password', $post_data) && !empty($post_data['password']))
		{
			//check password meets criteria
			Password_validation::validate($post_data['password']);
		}
	}
}