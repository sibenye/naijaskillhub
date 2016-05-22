<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');
require_once(APPPATH.'/core/security/NSH_CryptoService.php');
require_once(APPPATH.'/core/utilities/NSH_Utils.php');
require_once(APPPATH.'/core/validations/UserAttributes_validation.php');

use \YaLinqo\Enumerable;

class UserCredentials_model extends CI_Model
{
	use NSH_Utils;
	
	public function save_userCredential($post_data, $userId)
	{
		//$userId = $user->id;
		$credentialTypes = $this->db->get(CREDENTIALTYPES_TABLE)->result_array();
		
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
}