<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');

/**
 * Authentication Controller
 * api requests for handling user authentication and authorization *
 */

class Authentication extends NSH_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Authentication_model');
	}
	
	/**
	 * @api {post} /authentication/login Authenticate User
	 * @apiName LoginUser
	 * @apiGroup Authentication
	 *
	 * @apiParam {String} username
	 * @apiParam {String} password
	 * 
	 * @apiSuccess {String} authKey Authorization Key.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : {"authKey" : "...8yIepkSfHkFFSZiLujlpQL..."}
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 400 Bad Request
	 * {
	 * 	"status" : 109,
	 * 	"message" : "Invalid username or password",
	 * 	"errorDetail" : ""
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 109 Invalid username or password
	 *
	 */
	function login_post()
	{
		try {
			$post_data = $this->post();
					
			$response = $this->Authentication_model->login($post_data);
		
			$this->successResponse($response);
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {post} /authentication/logout Logout User
	 * @apiName LogoutUser
	 * @apiGroup Authentication
	 *
	 * @apiParam {String} authKey Authorization Key
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : null
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 400 Bad Request
	 * {
	 * 	"status" : 110,
	 * 	"message" : "Validation Error",
	 * 	"errorDetail" : "authKey is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 *
	 */
	function logout_post()
	{
		try {
			$post_data = $this->post();
				
			$this->Authentication_model->logout($post_data);
	
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
}