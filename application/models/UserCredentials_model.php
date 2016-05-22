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
				$password = NSH_CryptoService::secure_hash($post_data['password']);
				$data = array(
						'userId' => $userId,
						'credentialTypeId' => $credentialTypeId,
						'password' => $password,
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
			$error_message = 'Standard credentials can not be deleted, only social credentials';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		$existingCredentialTypes = Enumerable::from($user->credentialTypes)->select('$credType ==> strtolower($credType)')->toArray();
		
		if (!in_array($credentialTypeToBeDeleted, $existingCredentialTypes))
		{
			$error_message = 'User does not have this type of credential';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		if (count($existingCredentialTypes) == 1)
		{
			$error_message = 'Can not delete the only remaining credential';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE,
				array('name' => $delete_data['credentialType']))->row_array();
					
		$credentialTypeId = $credentialTypeResult['id'];
		
		$this->db->delete(USERGENERICCREDENTIALS_TABLE, array('userId' => $userId, 'credentialTypeId' => $credentialTypeId));		
		
	}
}