<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');
require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

/**
 * Emails Controller
 * api requests for email resources are handled by this Controller. *
 */

class Emails extends NSH_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('EmailSends_model');
        $this->load->model('Users_model');
    }

    /**
     * @api {post} /emails/send_activate Send Activation Email
     * @apiName SendActivationEmail
     * @apiGroup Emails
     *
     * @apiDescription Used to send activation email.
     *
     * @apiParam {String} emailAddress User's emailAddress.
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
     * 	"errorDetail" : "emailAddress is required"
     * }
     *
     * @apiError 110 Validation Error.
     *
     */
    function send_activation_post() {
        try {
            $data = $this->post();
            if (!array_key_exists('emailAddress', $data)){
                $error_message = 'emailAddress is required';
                throw new NSH_ValidationException(110, $error_message);
            }
            $user = $this->Users_model->get_user($data);
            $this->EmailSends_model->send_activation_email($user);
            $this->successResponse();
        } catch (NSH_Exception $e){
            $this->errorResponse($e);
        }
    }
}