<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');
require_once(APPPATH.'/core/security/NSH_CryptoService.php');
require_once(APPPATH.'/core/utilities/NSH_Utils.php');
require_once(APPPATH.'/core/validations/UserAttributes_validation.php');
require_once(APPPATH.'/core/validations/Password_validation.php');

class Authentication_model extends CI_Model
{
	use NSH_Utils;
	use NSH_CryptoService;
	
	public function __construct()
	{
		$this->load->database();
		$this->load->helper('date');
		$this->load->model('Users_model');
	}
	
	
	public function login($post_data)
	{
		if (!array_key_exists('username', $post_data) || empty($post_data['username'])
				|| !array_key_exists('password', $post_data) || empty($post_data['password']))
		{
			$error_message = 'username and password are required';
			throw new NSH_ValidationException(110, $error_message);
		}
		
		try {
			$user = $this->Users_model->get_user(array('username' => $post_data['username']));
			
			$this->db->select('id');			
			$credentialTypeResult = $this->db->get_where(CREDENTIALTYPES_TABLE,
					array('name' => STANDARD_CREDENTIALTYPE))->row_array();
						
			$credentialTypeId = $credentialTypeResult['id'];
			
			$userCredential = $this->db->get_where(USERSTANDARDCREDENTIALS_TABLE,
					array('userId' => $user->id, 'credentialTypeId' => $credentialTypeId))->row_array();
			
			if (empty($userCredential))
			{
				throw new NSH_ValidationException(109);
			}
			
			//verify the password
			$passwordHash = $userCredential['password'];
			if (!$this->is_verified($post_data['password'], $passwordHash))
			{
				throw new NSH_ValidationException(109);
			}
			
			//generate authorization key			
			$nowDate = mdate(DATE_TIME_STRING, time());
			$email = $user->emailAddress;
			
			//concat user's email + today's datetime and encrypt it
			$authKey = $email.AUTH_KEY_DELIMITER.$nowDate;
			$authKeyEncoded = $this->encode($authKey);
			
			$usersession = $this->db->get_where(USERSESSIONS_TABLE, array('userId' => $user->id))->row_array();
			
			if (!empty($usersession))
			{
				$data = array(
						'authorizationKey' => $authKeyEncoded,
						'lastLoginDate' => $nowDate
				);
				$this->db->update(USERSESSIONS_TABLE, $data, array('userId' => $user->id));
			}
			else {
				$data = array(
						'userId' => $user->id,
						'authorizationKey' => $authKeyEncoded,
						'firstLoginDate' => $nowDate,
						'lastLoginDate' => $nowDate
				);
				$this->db->insert(USERSESSIONS_TABLE, $data);
			}
			
			$response = array('authKey' => $authKeyEncoded);
			return $response;
			
		} catch (NSH_ResourceNotFoundException $e) {
			throw new NSH_ValidationException(109);
		}
		
		
	}
	
}