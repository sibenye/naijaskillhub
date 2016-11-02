<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');

/**
 * Admin Controller
 * api requests for admin resources are handled by this Controller. *
 */

class Admin extends NSH_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserAttributes_model');
		$this->load->model('CredentialTypes_model');
		$this->load->model('Categories_model');
    }

	/**
	 * @api {get} /admin/userAttributes Retrieve User Attibutes
	 * @apiName GetUserAttribute
	 * @apiGroup Admin
	 *
	 * @apiParam {Number} [id] User attribute ID.
	 *
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : [{
	 *			id: "1",
	 *			name: "firstName",
	 *			createdDate: "2015-12-07 03:38:46",
	 *			modifiedDate: "2015-12-07 03:38:46"
	 *		},
	 *		{
	 *			id: "2",
	 *			name: "lastName",
	 *			createdDate: "2015-12-07 03:43:43",
	 *			modifiedDate: "2015-12-07 03:43:43"
	 *		}
	 *	]
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 404 Bad Request
	 * {
	 * 	"status" : 220,
	 * 	"message" : "Object Not Found",
	 * 	"errorDetail" : "No UserAttributes found"
	 * }
	 *
	 * @apiError 220 Object Not Found
	 *
	 */
    function userAttributes_get()
    {
    	try {
    	    $userAttributes = $this->UserAttributes_model->get_userAttributes($this->get('id'));
			$this->successResponse($userAttributes);
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }

    /**
     * @api {post} /admin/userAttributes Create/Update User Attributes
     * @apiName upsertUserAttribute
     * @apiGroup Admin
     *
     * @apiParam {String} name  User Attribute name.
     * @apiParam {Number} [id]
     *
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *	"status" : 0,
     *	"message" : "success",
     *	"response" : null
     *
     * @apiErrorExample Validation Error:
     * HTTP/1.1 400 Bad Request
     * {
     * 	"status" : 110,
     * 	"message" : "Validation Error",
     * 	"errorDetail" : "User Attribute name is required"
     * }
     *
     * @apiError 110 Validation Error
     * @apiError 119 The Category name is already in use
     * @apiError 220 Object Not found
     *
     */
    function userAttributes_post()
    {
    	try {
    		$post_data = $this->post();

	        $this->UserAttributes_model->save_userAttribute($post_data);

	        $this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }

    /**
     * @api {delete} /admin/userAttributes Delete User Attributes
     * @apiName DeleteUserAttribute
     * @apiGroup Admin
     *
     * @apiParam {Number} id User Attribute ID
     *
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *	"status" : 0,
     *	"message" : "success",
     *	"response" : null
     *
     */
	function userAttributes_delete()
    {
    	try{
    		$id = $this->delete('id');
	    	$this->UserAttributes_model->delete_userAttribute($id);

	        $this->successResponse();
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
    }


	/**
	 * @api {get} /admin/credentialTypes Retrieve CredentialTypes
	 * @apiName GetCredentialType
	 * @apiGroup Admin
	 *
	 * @apiParam {Number} [id] User attribute ID.
	 *
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : [{
	 *			id: "1",
	 *			name: "standard"
	 *		},
	 *		{
	 *			id: "2",
	 *			name: "facebook"
	 *		},
	 *		{
	 *			id: "3",
	 *			name: "google"
	 *		}
	 *	]
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 404 Bad Request
	 * {
	 * 	"status" : 220,
	 * 	"message" : "Object Not Found",
	 * 	"errorDetail" : "No CredentialTypes found"
	 * }
	 *
	 * @apiError 220 Object Not Found
	 *
	 */
	 function credentialTypes_get()
    {
    	try {
    		$credentialTypes = $this->CredentialTypes_model->get_credentialTypes($this->get('id'));

	        $this->successResponse($credentialTypes);
    	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}

    }

	/**
	 * @api {get} /admin/categories Retrieve Categories
	 * @apiName GetCategory
	 * @apiGroup Admin
	 *
	 * @apiParam {Number} [id] Category ID.
	 * @apiParam {Number} [parentId] Category parent ID.
	 *
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : [{
	 *			id: "1",
	 *			name: "Entertainment",
	 *			parentId: null,
	 *			description: null,
	 *			imageUrl: null
	 *		},
	 *		{
	 *			id: "2",
	 *			name: "Test1",
	 *			parentId: null,
	 *			description: null,
	 *			imageUrl: null
	 *		}
	 *	]
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 404 Bad Request
	 * {
	 * 	"status" : 220,
	 * 	"message" : "Object Not Found",
	 * 	"errorDetail" : "No categories found"
	 * }
	 *
	 * @apiError 220 Object Not Found
	 *
	 */
	 function categories_get()
	 {
	 	try {

	        $categories = $this->Categories_model->get_categories($this->get('id'), $this->get('parentId'));

        	$this->successResponse($categories);
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
	 }

	 /**
	  * @api {post} /admin/categories Create/Update Category
	  * @apiName upsertCategory
	  * @apiGroup Admin
	  *
	  * @apiParam {String} name  Category name.
	  * @apiParam {Number} [id]
	  * @apiParam {Number} [parentId]
	  * @apiParam {String} [description]
	  * @apiParam {String} [imageUrl]
	  * @apiParam {String} [pluralName]
	  *
	  *
	  * @apiSuccessExample Success-Response:
	  * HTTP/1.1 200 OK
	  * {
	  *	"status" : 0,
	  *	"message" : "success",
	  *	"response" : null
	  *
	  * @apiErrorExample Validation Error:
	  * HTTP/1.1 400 Bad Request
	  * {
	  * 	"status" : 110,
	  * 	"message" : "Validation Error",
	  * 	"errorDetail" : "Category name is required"
	  * }
	  *
	  * @apiError 110 Validation Error
	  * @apiError 117 Parent CategoryId cannot be the same as the CategoryId
	  * @apiError 118 The Category name is already in use
	  * @apiError 220 Object Not found
	  *
	  */
	 function categories_post()
	 {
	 	try {
	 		$post_data = $this->post();

        	$this->Categories_model->save_category($post_data);

        	$this->successResponse();
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}

	 }

	 /**
	  * @api {delete} /admin/categories Delete Category
	  * @apiName DeleteCategory
	  * @apiGroup Admin
	  *
	  * @apiParam {Number} id Category ID
	  *
	  *
	  * @apiSuccessExample Success-Response:
	  * HTTP/1.1 200 OK
	  * {
	  *	"status" : 0,
	  *	"message" : "success",
	  *	"response" : null
	  *
	  */
	 function categories_delete()
	 {
	 	try {
	 		$id = $this->delete('id');
    		$this->Categories_model->delete_category($id);

        	$this->successResponse();
	 	} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}
	 }
}
