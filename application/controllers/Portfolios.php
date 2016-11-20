<?php
defined('BASEPATH') or exit('No direct script access allowed');

require (APPPATH . '/controllers/NSH_Controller.php');

/**
 * Portfolios Controller
 * api requests for skill resources are handled by this Controller.
 * *
 */
class Portfolios extends NSH_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Portfolios_model');
    }

    /**
     * @api {get} /portfolios/:id Retrieve Portfolios
     * @apiName GetPortfolios
     * @apiGroup Portfolios
     *
     * @apiParam {Number} [page] Page Number.
     * @apiParam {Number} [perPage] Number of results per page.
     *
     * @apiSuccess {Number} id User ID.
     * @apiSuccess {Dictionary} userAttributes  A dictionary list of the User's attributes.
     * @apiSuccess {Array} portfolios/categories An array of the Portfolio categories.
     * @apiSuccess {Array} portfolios/voiceClips  An array of the Portfolio voiceClips.
     * @apiSuccess {Array} portfolios/videos  An array of the Portfolio videos.
     * @apiSuccess {Array} portfolios/images  An array of the Portfolio images.
     * @apiSuccess {Array} portfolios/credits  An array of the Portfolio credits.
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *"status": 0,
     *"message": "success",
     *"response": [
     *      {
     *        "id": "27",
     *        "emailAddress": "testsly4change@mailinator.com",
     *        "userAttributes": null,
     *        "portfolios": {
     *          "videos": [
     *            {
     *              "videoPortfolioId": "1",
     *              "videoUrl": "/testVideo.mp4",
     *              "caption": "video caption"
     *            }
     *          ],
     *          "images": [
     *            {
     *              "imagePortfolioId": "1",
     *              "imageUrl": "/testImage.png",
     *              "caption": "image caption"
     *            }
     *          ],
     *          "voiceClips": [
     *            {
     *              "voiceClipPortfolioId": "1",
     *              "clipUrl": "/testClip.mp3",
     *              "caption": "my first voice clip"
     *            }
     *          ],
     *          "credits": [
     *            {
     *              "creditPortfolioId": "1",
     *              "year": "1980",
     *              "caption": "High School Graduation",
     *              "creditTypeId": "1"
     *            }
     *          ],
     *          "categories": [
     *            {
     *              "categoryId": "6",
     *              "categoryName": "Actor/Actress"
     *            },
     *            {
     *              "categoryId": "7",
     *              "categoryName": "Dancer"
     *            }
     *          ]
     *        }
     *      },
     *      {
     *        "id": "28",
     *        "emailAddress": "testGSR3766@mailinator.com",
     *        "userAttributes": {
     *          "firstName": "test",
     *          "lastName": "GSR37"
     *        },
     *        "portfolios": {
     *          "videos": [],
     *          "images": [],
     *          "voiceClips": [],
     *          "credits": [],
     *          "categories": []
     *        }
     *      }
     *  ]
     *}
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 404 Not Found
     * {
     * "status" : 220,
     * "message" : "Object Not Found",
     * "errorDetail" : "No portfolios found"
     * }
     *
     * @apiError 220 Object Not Found.
     */
    function portfolios_get() {
        try {
            $portfolios = $this->Portfolios_model->get_portfolios(
                    $this->query('page'), $this->query('perPage'));

            $this->successResponse($portfolios);
        } catch ( NSH_Exception $e ) {
            $this->errorResponse($e);
        }
    }

}

