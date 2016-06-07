<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

class NSH_Controller extends REST_Controller {
	
	private $nshResponse = array('status' => 0, 'message' => 'success', 'response' => '', 'errorDetail' => '');
		
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function successResponse($message = NULL)
	{
		$responseObject = $this->nshResponse;
		unset($responseObject['errorDetail']);
		$responseObject['response'] = $message;
		$this->response($responseObject, 200);
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