<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');

class Passwords extends NSH_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('UserCredentials_model');
    }
    
    /**
     * @api {post} /passwords/reset Reset Password
     * @apiName ResetPassword
     * @apiGroup Password
     *
     * @apiParam {String} emailAddress
     *
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *	"status" : 0,
     *	"message" : "success",
     *	"response" : null
     *	}
     * }
     *
     * @apiErrorExample Validation Error:
     * HTTP/1.1 400 Bad Request
     * {
     * 	"status" : 110,
     * 	"message" : "Validation Error",
     * 	"errorDetail" : "emailAddress is required"
     * }
     *
     * @apiError 110 Validation Error.
     *
     */
    function reset_post()
    {
    	try {
    		$post_data = $this->post();
    		    	
    		$this->UserCredentials_model->reset_password($post_data);
    	
    		$this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }
    
    /**
     * @api {post} /passwords/change Change Password
     * @apiName ChangePassword
     * @apiGroup Password
     * 
     * @apiDescription OldPassword and resetToken are mutually exclusive, only one should be provided.
     *
     * @apiParam {String} newPassword  Required.
     * @apiParam {String} [oldPassword]
     * @apiParam {String} [resetToken]
     * @apiParam {Number} [userId] Required if oldPassword is provided.
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *	"status" : 0,
     *	"message" : "success",
     *	"response" : null
     *	}
     * }
     *
     * @apiErrorExample Validation Error:
     * HTTP/1.1 400 Bad Request
     * {
     * 	"status" : 110,
     * 	"message" : "Validation Error",
     * 	"errorDetail" : "newPassword is required"
     * }
     *
     * @apiError 110 Validation Error
     * @apiError 113 Password does not meet criteria
     * @apiError 115 Invalid old password
     * @apiError 116 New password cannot be the same as the old password
     * @apiError 121 resetToken is expired
     * @apiError 122 resetToken is invalid
     * @apiError 220 Object Not Found
     * @apiError 222 User does not have standard credential
     *
     */
    function change_post()
    {
    	try {
    		$post_data = $this->post();
    			
    		$this->UserCredentials_model->change_password($post_data);
    		 
    		$this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }
}