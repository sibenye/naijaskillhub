<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class UserAttributeValues_model extends CI_Model {
		
		public function __construct()
        {
                $this->load->database();
        }
		
		public function get_userAttributeValues($userId)
		{
			$result = $this->db->get_where(USERS_TABLE, array('id' => $userId))->row_array();
		
			if (empty($result)){
				$error_message = 'User does not exist';
				throw new NSH_ResourceNotFoundException(220, $error_message);
			}
		
			$userAttributes = array('id' => $userId, 'attributes' => $this->getAttributes($userId));
			 
			return $userAttributes;
		}
		
		public function getAttributes($userId)
		{
			$attributes = null;
		
			$this->db->select(USERATTRIBUTEVALUES_TABLE.'.attributeValue,'.USERATTRIBUTES_TABLE.'.name');
			$this->db->from(USERATTRIBUTEVALUES_TABLE);
			$this->db->join(USERATTRIBUTES_TABLE, USERATTRIBUTES_TABLE.'.id = '.USERATTRIBUTEVALUES_TABLE.'.userAttributeId');
			$this->db->where('userId', $userId);
			$userAttributes = $this->db->get()->result_array();
		
			foreach ($userAttributes as $userAttribute) {
				$attributes[$userAttribute['name']] = $userAttribute['attributeValue'];
			}
			return $attributes;
		}
		
		public function save_userAttributes($post_data)
		{
			$userId = $post_data['id'];
			$result = $this->db->get_where(USERS_TABLE, array('id' => $userId))->row_array();
				
			if (empty($result)){
				$error_message = 'User does not exist';
				throw new NSH_ResourceNotFoundException(220, $error_message);
			}
			
			if (!array_key_exists('attributes', $post_data) || empty($post_data['attributes']))
			{
				$error_message = 'attributes collection is required';
				throw new NSH_ValidationException(110, $error_message);
			}
				
			$user = new User();
			$user->id = $result['id'];
			$user->emailAddress = $result['emailAddress'];
			$user->username = $result['username'];
			$user->isActive = ($result['isActive'] == 1);
				
			$this->validateAttributes($post_data['attributes']);
				
			return $this->upsert_userAttributes($post_data['attributes'], $userId);
		}
		
		public function upsert_userAttributes($attributes, $userId)
		{
			$modifiedDate = mdate(DATE_TIME_STRING, time());
				
			$savedAttributes = array();
			if (empty($attributes))
			{
				$response = array(
						'id' => $userId,
						'attributes' => $this->getAttributes($userId)
				);
				return $response;
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
				
			$response = array(
				'id' => $userId,
				'attributes' => $this->getAttributes($userId)
			);
			return $response;
		}
		
		public function validateAttributes($attributes)
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
}
	