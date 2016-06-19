<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
require_once(APPPATH.'/core/security/NSH_CryptoService.php');

class NSH_Controller extends REST_Controller {
	
	use NSH_CryptoService;
	
	private $nshResponse = array('status' => 0, 'message' => 'success', 'response' => '', 'errorDetail' => '');
		
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Validate and verify the user's authKey
	 * 
	 */
	protected function session_authorize()
	{
		try {
			$userId = NULL;
				
			if (!empty($this->get('id')))
			{
				$userId = $this->get('id');
			}
			elseif (!empty($this->post('id')))
			{
				$userId = $this->post('id');
			}
			elseif (!empty($this->put('id')))
			{
				$userId = $this->put('id');
			}
			elseif (!empty($this->delete('id')))
			{
				$userId = $this->delete('id');
			}
			else
			{
				$userId = $this->query('id');
			}
			
			if (empty($userId))
			{
				$errorDetail = 'The user id is missing in request';
				$nshException = new NSH_Exception(405, $errorDetail, self::HTTP_UNAUTHORIZED);
				$this->errorResponse($nshException);
			}
			
			// Get the auth key name variable from the config file
			$auth_key_variable = $this->config->item('auth_key_name');
			
			// Work out the name of the SERVER entry based on config
			$key_name = 'HTTP_' . strtoupper(str_replace('-', '_', $auth_key_variable));
			
			$authKey = $this->input->server($key_name);
			
			//validate auth key
			$sessionRow = $this->db->get_where(USERSESSIONS_TABLE, array('userId' => $userId))->row_array();
			
			if ($authKey !== $sessionRow['authorizationKey'])
			{
				$errorDetail = 'Invalid authorization key';
				$nshException = new NSH_Exception(405, $errorDetail, self::HTTP_UNAUTHORIZED);
				$this->errorResponse($nshException);
			}
			
			$authKeyDecoded = $this->decode($authKey);
			
			if (empty($authKeyDecoded))
			{
				$errorDetail = 'Invalid authorization key';
				$nshException = new NSH_Exception(405, $errorDetail, self::HTTP_UNAUTHORIZED);
				$this->errorResponse($nshException);
			}
			
			list($userEmailAddress,$authKeyDateStr) =  explode(AUTH_KEY_DELIMITER, $authKeyDecoded);
			//verify authKey has not expired
			$nowDate = new DateTime();
			$authKeyDate = new DateTime($authKeyDateStr);
			$elaspedTime = $nowDate->diff($authKeyDate)->i; //get the difference in minutes
			$authKeyLiveSpan = $this->config->item('auth_key_live_span');
			
			if ($elaspedTime > $authKeyLiveSpan)
			{
				$errorDetail = 'authorization key is expired';
				$nshException = new NSH_Exception(405, $errorDetail, self::HTTP_UNAUTHORIZED);
				$this->errorResponse($nshException);
			}
			
		} catch (Exception $exception) {
			$responseObject = $this->nshResponse;
			unset($responseObject['response']);
			$responseObject['status'] = $exception->getCode();
			$responseObject['message'] = $exception->getMessage();
			$this->response($responseObject, self::HTTP_INTERNAL_SERVER_ERROR);
		}
		
	}
	
	protected function successResponse($message = NULL)
	{
		$responseObject = $this->nshResponse;
		unset($responseObject['errorDetail']);
		$responseObject['response'] = $message;
		$this->response($responseObject, self::HTTP_OK);
	}
	
	protected function errorResponse(NSH_Exception $nshException)
	{
		$responseObject = $this->nshResponse;
		unset($responseObject['response']);
		$httpStatus = $nshException->getHttpStatusCode();
		$responseObject['status'] = $nshException->getCode();
		$responseObject['message'] = $nshException->getMessage();
		$responseObject['errorDetail'] = $nshException->getErrorDetail();
		$this->response($responseObject, $httpStatus);
	}
	
}
