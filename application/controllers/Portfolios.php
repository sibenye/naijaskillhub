<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');

/**
 * Portfolios Controller
 * api requests for skill resources are handled by this Controller. * 
 */

class Portfolios extends NSH_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Portfolios_model');
    }
	
    /**
     * @api {get} /portfolios/:id Retrieve Portfolios
     * @apiName GetPortfolios
     * @apiGroup Portfolios
     *
     * @apiParam {Number} [id] Portfolio ID.
     * @apiParam {Number} [categoryId] Portfolio category ID.
     *
     * @apiSuccess {Number} id Portfolio ID.
     * @apiSuccess {Number} categoryId Portfolio category ID.
     * @apiSuccess {Number} userId  User ID.
     * @apiSuccess {Date} createdDate Portfolio created date.
     * @apiSuccess {Date} modifiedDate Portfolio modified date.
     * @apiSuccess {Array} videos  An array of the Portfolio video urls.
     * @apiSuccess {Array} images  An array of the Portfolio image urls.
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *	"status" : 0,
     *	"message" : "success",
     *	"response" : [{
     *			"id" : "2",
     *			"categoryId" : "1",
     *			"userId" : "1",
     *			"createdDate" : "2015-12-27 04:37:58",
     *			"modifiedDate" : "2016-05-10 06:39:22",
     *			"videos" : [{
     *				"id" : "5",
     *				"portfolioId" : "2",
     *				"videoUrl" : "l:\\testdrive2.mp4"
     *				}
     *			],
     *			"images" : [{
     *				"id" : "4",
     *				"portfolioId" : "2",
     *				"imageUrl" : "c:\\secondimage.png"
     *				}
     *			]
     *		}
     *	]
     * }
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 404 Not Found
     * {
     * 	"status" : 220,
     * 	"message" : "Object Not Found",
     * 	"errorDetail" : "No portfolios found"
     * }
     *
     * @apiError 220 Object Not Found.
     *
     */
     function portfolios_get()
	 {
	 	try {
	 		$portfolios = $this->Portfolios_model->get_portfolios($this->get('id'), $this->get('categoryId'));      
        
        	$this->successResponse($portfolios); 
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
	 	
	 }
	 
}

	