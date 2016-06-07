<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/controllers/NSH_Controller.php');

/**
 * Users Controller
 * api requests for user resources are handled by this Controller. * 
 */

class Users extends NSH_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
		$this->load->model('UserAttributeValues_model');
		$this->load->model('UserCredentials_model');
    }
    
    /**
     * @api {get} /users/:id Retrieve User Information
     * @apiName GetUser
     * @apiGroup Users
     * 
     * @apiDescription At least one of these id, or username, or emailAddress is required.
     * 
     * @apiParam {Number} [id] User ID.
     * @apiParam {String} [username] User's username.
     * @apiParam {String} [emailAddress] User's emailAddress.
     * 
     * @apiSuccess {Number} id ID of the User.
     * @apiSuccess {String} emailAddress  Email address of the User.
     * @apiSuccess {String} username  Username of the User.
     * @apiSuccess {Boolean} isActive  Indicates whether the User is active.
     * @apiSuccess {Array} credentialTypes  An array of the User's credentialTypes.
     * @apiSuccess {Dictionary} attributes  A dictionary list of the User's attributes.
     * 
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : {
	 *		"id" : "21",
	 *		"emailAddress" : "testGSR31@mailinator.com",
	 *		"username" : "testGSR30",
	 *		"isActive" : false,
	 *		"credentialTypes" : ["standard"],
	 *		"attributes" : {
	 *			"firstName" : "test",
	 *			"lastName" : "GSR31"
	 *		}
	 *	}
     * }
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 404 Not Found
     * {
     * 	"status" : 220,
     * 	"message" : "Object Not Found",
     * 	"errorDetail" : "User does not exist"
     * }
     * 
     * @apiError 220 Object Not Found
     * 
     */
    function users_get()
    {
    	try {
    		$get_data = $this->get();
    		$user = $this->Users_model->get_user($get_data);
    		
    		$this->successResponse($user);
    	} catch (NSH_Exception $e) {
    		$this->errorResponse($e);
    	}
    }
    
    /**
	 * @api {get} /users/:id/attributes Retrieve User Attibute Values
	 * @apiName GetUserAttributeValue
	 * @apiGroup Users
	 *
	 * @apiParam {Number} id User ID.
	 *
	 * @apiSuccess {Number} id ID of the User.
	 * @apiSuccess {Dictionary} attributes  A dictionary list of the User's attributes.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : {
	 *		"id" : "21",
	 *		"attributes" : {
	 *			"firstName" : "test",
	 *			"lastName" : "GSR31"
	 *		}
	 *	}
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 404 Bad Request
	 * {
	 * 	"status" : 220,
	 * 	"message" : "Object Not Found",
	 * 	"errorDetail" : "User does not exist"
	 * }
	 *
	 * @apiError 220 Object Not Found
	 *
	 */    
    function attributes_get()
    {
    	try {
    		$userAttributes = $this->UserAttributeValues_model->get_userAttributeValues($this->get('id'));
    	
    		$this->successResponse($userAttributes);
    	} catch (NSH_Exception $e) {
    		$this->errorResponse($e);
    	}
    }
    
    /**
     * @api {get} /users/:id/portfolios Retrieve User portfolios
     * @apiName GetUserPortfolio
     * @apiGroup Users
     *
     * @apiParam {Number} id User ID.
     * @apiParam {Number} [portfolioId] Portfolio ID
     * @apiParam {Number} [categoryId] Portfolio category ID
     *
     * @apiSuccess {Number} id User ID.
     * @apiSuccess {Number} portfolios/id Portfolio ID.
     * @apiSuccess {Number} portfolios/categoryId Portfolio category ID.
     * @apiSuccess {Number} portfolios/userId  User ID.
     * @apiSuccess {Date} portfolios/createdDate Portfolio created date.
     * @apiSuccess {Date} portfolios/modifiedDate Portfolio modified date.
     * @apiSuccess {Array} portfolios/videos  An array of the Portfolio video urls.
     * @apiSuccess {Array} portfolios/images  An array of the Portfolio image urls.
     * 
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : [{
	 *			"id" : "1",
	 *			"portfolios" : [{
	 *					"id" : "2",
	 *					"categoryId" : "1",
	 *					"userId" : "1",
	 *					"createdDate" : "2015-12-27 04:37:58",
	 *					"modifiedDate" : "2016-05-10 06:39:22",
	 *					"videos" : [{
	 *						"id" : "5",
	 *						"portfolioId" : "2",
	 *						"videoUrl" : "l:\\testdrive2.mp4"
	 *						}
	 *					],
	 *					"images" : [{
	 *						"id" : "4",
	 *						"portfolioId" : "2",
	 *						"imageUrl" : "c:\\secondimage.png"
	 *						}
	 *					]
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
     * @apiError 220 Object Not Found
     *
     */
    function portfolios_get()
    {
    	try {
    		$userPortfolios = $this->Users_model->get_userPortfolio($this->get());
    		 
    		$this->successResponse($userPortfolios);
    	} catch (NSH_Exception $e) {
    		$this->errorResponse($e);
    	}
    }
    
    /**
     * @api {post} /users Create User
     * @apiName CreateUser
     * @apiGroup Users
     *
     * @apiParam {String} emailAddress  Required.
     * @apiParam {String} username  Required.
     * @apiParam {String} [credentialType]  User's credentialType [Standard, Google, Facebook]. Default is Standard.
     * @apiParam {String} [password]  Required if credentialType is Standard.
     * @apiParam {String} [socialId]  Required if credentialType is Google or Facebook.
     * @apiParam {Dictionary} [attributes]  A dictionary list of the User's attributes.
     *
     * @apiSuccess {Number} id ID of the User.
     * @apiSuccess {String} emailAddress  Email address of the User.
     * @apiSuccess {String} username  Username of the User.
     * @apiSuccess {Boolean} isActive  Indicates whether the User is active.
     * @apiSuccess {Array} credentialTypes  An array of the User's credentialTypes.
     * @apiSuccess {Dictionary} attributes  A dictionary list of the User's attributes.
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *	"status" : 0,
     *	"message" : "success",
     *	"response" : {
     *		"id" : "21",
     *		"emailAddress" : "testGSR31@mailinator.com",
     *		"username" : "testGSR30",
     *		"isActive" : false,
     *		"credentialTypes" : ["standard"],
     *		"attributes" : {
     *			"firstName" : "test",
     *			"lastName" : "GSR31"
     *		}
     *	}
     * }
     *
     * @apiErrorExample Validation Error:
     * HTTP/1.1 400 Bad Request
     * {
     * 	"status" : 110,
     * 	"message" : "Validation Error",
     * 	"errorDetail" : "username is required for new User creation"
     * }
     *
     * @apiError 110 Validation Error
     * @apiError 111 This username is not available
     * @apiError 112 This emailAddress is already in use
     * @apiError 113 Password does not meet criteria
     *
     */
    function users_post()
	{	
		try {
			$post_data = $this->post();
			
    		$userObject = $this->Users_model->create_user($post_data);
     
    		$this->successResponse($userObject);
		} catch (NSH_Exception $e){
    		$this->errorResponse($e);
    	}		
	}
	
	/**
	 * @api {put} /users/:id Update User
	 * @apiName UpdateUser
	 * @apiGroup Users
	 *
	 * @apiParam {Number} id User's unique ID.
	 * @apiParam {String} [emailAddress]
	 * @apiParam {String} [username]
	 * @apiParam {String} [credentialType]  User's credentialType [Standard, Google, Facebook].
	 * @apiParam {String} [password]  Required if credentialType is Standard.
	 * @apiParam {String} [socialId]  Required if credentialType is Google or Facebook.
	 * @apiParam {Dictionary} [attributes]  A dictionary list of the User's attributes and their corresponding values.
	 *
	 * @apiSuccess {Number} id ID of the User.
	 * @apiSuccess {String} emailAddress  Email address of the User.
	 * @apiSuccess {String} username  Username of the User.
	 * @apiSuccess {Boolean} isActive  Indicates whether the User is active.
	 * @apiSuccess {Array} credentialTypes  An array of the User's credentialTypes.
	 * @apiSuccess {Dictionary} attributes  A dictionary list of the User's attributes.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : {
	 *		"id" : "21",
	 *		"emailAddress" : "testGSR31@mailinator.com",
	 *		"username" : "testGSR30",
	 *		"isActive" : false,
	 *		"credentialTypes" : ["standard"],
	 *		"attributes" : {
	 *			"firstName" : "test",
	 *			"lastName" : "GSR31"
	 *		}
	 *	}
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 400 Bad Request
	 * {
	 * 	"status" : 110,
	 * 	"message" : "Validation Error",
	 * 	"errorDetail" : "username is required for new User creation"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 111 This username is not available
	 * @apiError 112 This emailAddress is already in use
	 * @apiError 113 Password does not meet criteria
	 * @apiError 220 Object Not Found
	 *
	 */
	function users_put()
	{
		try {
			$post_data = $this->put();
			
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
				
			$userObject = $this->Users_model->update_user($post_data);
			 
			$this->successResponse($userObject);
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {post} /users/:id/attributes Create/Update User Attibute Values
	 * @apiName UpsertUserAttributeValue
	 * @apiGroup Users
	 *
	 * @apiParam {Number} id User ID.
	 * @apiParam {Dictionary} attributes  A dictionary list of the User's attributes and their corresponding values.
	 *
	 * @apiSuccess {Number} id ID of the User.
	 * @apiSuccess {Dictionary} attributes  A dictionary list of the User's attributes.
	 *
	 * @apiSuccessExample Success-Response:
	 * HTTP/1.1 200 OK
	 * {
	 *	"status" : 0,
	 *	"message" : "success",
	 *	"response" : {
	 *		"id" : "21",
	 *		"attributes" : {
	 *			"firstName" : "test",
	 *			"lastName" : "GSR31"
	 *		}
	 *	}
	 * }
	 *
	 * @apiErrorExample Validation Error:
	 * HTTP/1.1 400 Bad Request
	 * {
	 * 	"status" : 110,
	 * 	"message" : "Validation Error",
	 * 	"errorDetail" : "attributes collection is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 120 Invalid User attribute
	 * @apiError 220 Object Not Found
	 *
	 */
	function attributes_post()
	{
		try {
			$post_data = $this->post();
			
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
				
			$userAttributes = $this->UserAttributeValues_model->save_userAttributes($post_data);
			 
			$this->successResponse($userAttributes);
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {post} /users/:id/credentials Add User's credential
	 * @apiName AddUserCredential
	 * @apiGroup Users
	 * 
	 * @apiDescription This endpoint only adds a new credential to the user's credential collection. It cannot be used to update a user's password or socialId.
	 *
	 * @apiParam {Number} id User's unique ID.
	 * @apiParam {String} credentialType  User's credentialType [Standard, Google, Facebook].
	 * @apiParam {String} [password]  Required if credentialType is Standard.
	 * @apiParam {String} [socialId]  Required if credentialType is Google or Facebook.
	 *
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
	 * 	"errorDetail" : "credentialType is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 113 Password does not meet criteria
	 * @apiError 114 User credential already exists
	 * @apiError 220 Object Not Found
	 *
	 */
	function credentials_post()
	{
		try {
			$post_data = $this->post();
				
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
		
			$this->UserCredentials_model->add_userCredential($post_data);
		
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {put} /users/:id/emailAddress Update User's emailAddress
	 * @apiName UpdateUserEmailAddress
	 * @apiGroup Users
	 *
	 * @apiDescription This endpoint is only used to change a user's emailAddress. 
	 *
	 * @apiParam {Number} id User's unique ID.
	 * @apiParam {String} emailAddress  User's emailAddress.
	 *
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
	 * @apiError 110 Validation Error
	 * @apiError 112 This emailAddress is already in use
	 * @apiError 220 Object Not Found
	 *
	 */
	function emailAddress_put()
	{
		try {
			$post_data = $this->put();
		if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
				
			$this->Users_model->update_emailAddress($post_data);
			 
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {put} /users/:id/username Update User's username
	 * @apiName UpdateUserUsername
	 * @apiGroup Users
	 *
	 * @apiDescription This endpoint is only used to change a user's username.
	 *
	 * @apiParam {Number} id User's unique ID.
	 * @apiParam {String} username  User's username.
	 *
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
	 * 	"errorDetail" : "username is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 111 This username is not available
	 * @apiError 220 Object Not Found
	 *
	 */
	function username_put()
	{
		try {
			$post_data = $this->put();
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
	
			$this->Users_model->update_userName($post_data);
	
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {post} /users/activate Activate User
	 * @apiName ActivateUser
	 * @apiGroup Users
	 *
	 * @apiParam {String} activationToken
	 *
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
	 * 	"errorDetail" : "activationToken is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 123 activationToken is invalid
	 *
	 */
	function activate_post()
	{
		try {
			$post_data = $this->post();
					
			$this->Users_model->activate_user($post_data);
		
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {post} /users/:id/portfolios Create User portfolio
	 * @apiName CreateUserPortfolio
	 * @apiGroup Users
	 *
	 * @apiParam {Number} id User's unique ID.
	 * @apiParam {Number} portfolio/categoryId  Portfolio category ID.
	 * @apiParam {String} [portfolio/videos/videoUrl]  Video URL.
	 * @apiParam {String} [portfolio/images/imageUrl]  Image URL.
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
	 * 	"errorDetail" : "categoryId is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 220 Object Not Found
	 * @apiError 230 User already has portfolio in this category
	 *
	 */
	function portfolios_post()
	{
		try {
			$post_data = $this->post();
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
		
			$this->Users_model->create_userPortfolio($post_data);
		
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {put} /users/:id/portfolios Update User portfolio
	 * @apiName UpdateUserPortfolio
	 * @apiGroup Users
	 *
	 * @apiParam {Number} id User's unique ID.
	 * @apiParam {Number} portfolio/id  Portfolio ID.
	 * @apiParam {Number} portfolio/categoryId  Portfolio category ID.
	 * @apiParam {String} [portfolio/videos/videoUrl]  Video URL.
	 * @apiParam {String} [portfolio/images/imageUrl]  Image URL.
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
	 * 	"errorDetail" : "categoryId is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 220 Object Not Found
	 * @apiError 230 User already has portfolio in this category
	 *
	 */
	function portfolios_put()
	{
		try {
			$post_data = $this->put();
			if (!array_key_exists('id', $post_data)){
				//if the user id is not in the post body get it from the request url
				$post_data['id'] = $this->get('id');
			}
		
			$this->Users_model->update_userPortfolio($post_data);
		
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {delete} /users/:id/credentials Delete User credential
	 * @apiName DeleteUserCredential
	 * @apiGroup Users
	 * 
	 * @apiDescription Deletes a User's credential. Standard credentials cannot be deleted, only social credentials.
	 *
	 * @apiParam {Number} id User's ID
	 * @apiParam {String} credentialType [Google, Facebook].
	 *
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
	 * 	"errorDetail" : "credentialType is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 220 Object Not Found
	 * @apiError 223 Standard credentials cannot be deleted, only social credentials
	 * @apiError 224 Cannot delete the only remaining credential
	 * @apiError 225 CredentialType does not exist for User
	 *
	 */
	function credentials_delete()
	{
		try {
			$delete_data = $this->query();
			if (!array_key_exists('id', $delete_data)){
				$delete_data['id'] = $this->get('id');
			}
	
			$this->UserCredentials_model->delete_userCredential($delete_data);
	
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
	
	/**
	 * @api {delete} /users/:id/portfolios Delete User portfolio
	 * @apiName DeleteUserPortfolio
	 * @apiGroup Users
	 *
	 * @apiParam {Number} id User ID
	 * @apiParam {String} portfolioId Portfolio ID.
	 *
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
	 * 	"errorDetail" : "The portfolio Id is required"
	 * }
	 *
	 * @apiError 110 Validation Error
	 * @apiError 220 Object Not Found
	 *
	 */
	function portfolios_delete()
	{
		try {
			$delete_data = $this->query();
			if (!array_key_exists('id', $delete_data)){
				$delete_data['id'] = $this->get('id');
			}
	
			$this->Users_model->delete_userPortfolio($delete_data);
	
			$this->successResponse();
		} catch (NSH_Exception $e){
			$this->errorResponse($e);
		}
	}
}
	