<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once (APPPATH . '/core/security/NSH_CryptoService.php');
require_once (APPPATH . '/core/utilities/NSH_Utils.php');
require_once (APPPATH . '/core/validations/Users_creation_validation.php');
require_once (APPPATH . '/core/validations/Password_validation.php');
require_once (APPPATH . '/core/objects/user.php');

require_once (APPPATH . '/core/exceptions/NSH_Exception.php');
require_once (APPPATH . '/core/exceptions/NSH_ResourceNotFoundException.php');
require_once (APPPATH . '/core/exceptions/NSH_ValidationException.php');
class Users_model extends CI_Model {
    
    use NSH_Utils;
    use NSH_CryptoService;
    public function __construct() {
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('UserCredentials_model');
        $this->load->model('UserAttributeValues_model');
        $this->load->model('Portfolios_model');
        $this->load->model('EmailSends_model');
    }
    public function get_user($get_data) {
        $result = array ();
        $searchData = array ();
        if (! empty($get_data ['id'])) {
            $searchData ['id'] = $get_data ['id'];
        }
        if (! empty($get_data ['username'])) {
            $searchData ['username'] = $get_data ['username'];
        }
        if (! empty($get_data ['emailAddress'])) {
            $searchData ['emailAddress'] = $get_data ['emailAddress'];
        }
        
        if (empty($searchData)) {
            $error_message = 'Id, or username, or emailAddress is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        $query = $this->db->get_where(USERS_TABLE, $searchData);
        $result = $query->row_array();
        
        if (empty($result)) {
            $error_message = 'User does not exist';
            throw new NSH_ResourceNotFoundException(220, $error_message);
        }
        
        $user = new User();
        $user->id = $result ['id'];
        $user->emailAddress = $result ['emailAddress'];
        $user->username = $result ['username'];
        $user->isActive = ($result ['isActive'] == 1);
        
        $user->credentialTypes = $this->UserCredentials_model->getCredentialTypes($user->id);
        
        $user->attributes = $this->UserAttributeValues_model->getAttributes($user->id);
        
        return $user;
    }
    public function create_user($post_data) {
        if (array_key_exists('id', $post_data) && ! empty($post_data ['id'])) {
            $error_message = 'User Id is not needed for new user creation, for user update use the http PUT method';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        if (! array_key_exists('credentialType', $post_data)) {
            // default to STANDARD credentialType
            $post_data ['credentialType'] = STANDARD_CREDENTIALTYPE;
        }
        
        // validate post data
        $this->validateUserPostData($post_data);
        
        $user = $this->upsert_user($post_data);
        // insert credential
        // Note that credentials will only be inserted if the user does not have that credentialType
        // If the user already has that credentialType it will do nothing.
        $user->credentialTypes = $this->UserCredentials_model->save_userCredential($post_data, $user->id);
        
        if (array_key_exists('attributes', $post_data)) {
            $user->attributes = $this->UserAttributeValues_model->upsert_userAttributes($post_data ['attributes'], $user->id);
        }
        
        // send activation email
        $this->EmailSends_model->send_activation_email($user);
        
        return $user;
    }
    public function update_user($post_data) {
        if (! array_key_exists('id', $post_data) || empty($post_data ['id'])) {
            $error_message = 'The User Id is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        // validate post data
        $this->validateUserPostData($post_data);
        
        $user = $this->upsert_user($post_data);
        // insert credential
        // Note that credentials will only be inserted if the user does not have that credentialType
        // If the user already has that credentialType it will do nothing.
        $user->credentialTypes = $this->UserCredentials_model->save_userCredential($post_data, $user->id);
        
        if (array_key_exists('attributes', $post_data)) {
            $user->attributes = $this->UserAttributeValues_model->upsert_userAttributes($post_data ['attributes'], $user->id);
        }
        
        return $user;
    }
    public function update_userName($post_data) {
        if (! array_key_exists('id', $post_data) || empty($post_data ['id'])) {
            $error_message = 'user Id is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        if (! array_key_exists('username', $post_data) || empty($post_data ['username'])) {
            $error_message = 'username is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        $userId = $post_data ['id'];
        $username = $post_data ['username'];
        
        // ensure that the user exists
        if (! $this->userExists($userId)) {
            $error_message = "User does not exist";
            throw new NSH_ResourceNotFoundException(220, $error_message);
        }
        
        // ensure that the username is not in use
        if ($this->userNameInUse($username, $userId)) {
            // return error that this username is not available
            throw new NSH_ValidationException(111);
        }
        
        $modifiedDate = mdate(DATE_TIME_STRING, time());
        
        $data = array (
                'username' => $username,
                'modifiedDate' => $modifiedDate
        );
        
        $this->db->update(USERS_TABLE, $data, array (
                'id' => $userId
        ));
    }
    public function update_emailAddress($post_data) {
        if (! array_key_exists('id', $post_data) || empty($post_data ['id'])) {
            $error_message = 'user Id is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        if (! array_key_exists('emailAddress', $post_data) || empty($post_data ['emailAddress'])) {
            $error_message = 'emailAddress is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        $userId = $post_data ['id'];
        $emailAddress = $post_data ['emailAddress'];
        
        // ensure that the user exists
        if (! $this->userExists($userId)) {
            $error_message = "User does not exist";
            throw new NSH_ResourceNotFoundException(220, $error_message);
        }
        
        // ensure that the emailAddress is not in use
        if ($this->userEmailInUse($emailAddress, $userId)) {
            // return error that this emailAddress is in use
            throw new NSH_ValidationException(112);
        }
        
        $modifiedDate = mdate(DATE_TIME_STRING, time());
        
        $data = array (
                'emailAddress' => $emailAddress,
                'modifiedDate' => $modifiedDate
        );
        
        $this->db->update(USERS_TABLE, $data, array (
                'id' => $userId
        ));
    }
    public function activate_user($post_data) {
        if (! array_key_exists('activationToken', $post_data) || empty($post_data ['activationToken'])) {
            $error_message = 'activationToken is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        $activationKeyEncoded = $post_data ['activationToken'];
        // decrypt the encoded activationKey
        $activationKeyDecoded = $this->decode($activationKeyEncoded);
        // split the activationKey and get the activationToken
        list($emailAddress, $activationToken) = explode(ACTIVATION_KEY_DELIMITER, $activationKeyDecoded);
        
        // verify activationToken
        $this->db->select('id,activationToken');
        $existingUser = $this->db->get_where(USERS_TABLE, array (
                'emailAddress' => $emailAddress
        ))->row_array();
        
        if (empty($existingUser) || empty($existingUser ['activationToken'])) {
            // throw invalid activation token error
            throw new NSH_ValidationException(123);
        }
        
        $activationKeyTokenHash = $existingUser ['activationToken'];
        if (! $this->is_verified($activationToken, $activationKeyTokenHash)) {
            throw new NSH_ValidationException(123);
        }
        
        // activate user
        $userId = $existingUser ['id'];
        $modifiedDate = mdate(DATE_TIME_STRING, time());
        $this->db->update(USERS_TABLE, array (
                'isActive' => true,
                'activationToken' => NULL,
                'modifiedDate' => $modifiedDate
        ), array (
                'id' => $userId
        ));
    }
    public function get_userPortfolio($get_data) {
        if (! array_key_exists('id', $get_data) || empty($get_data ['id'])) {
            $error_message = 'The User Id is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        $userId = $get_data ['id'];
        
        $portfolios = $this->Portfolios_model->get_portfolios_by_userId($userId);
        
        $userPortfolios = array (
                'id' => $userId,
                'portfolios' => $portfolios
        );
        
        return $userPortfolios;
    }
    public function upsert_userPortfolio($post_data) {
        if (! array_key_exists('id', $post_data) || empty($post_data ['id'])) {
            $error_message = 'The User Id is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        // ensure that the user exists
        if (! $this->userExists($post_data ['id'])) {
            $error_message = "User does not exist";
            throw new NSH_ResourceNotFoundException(220, $error_message);
        }
        
        if (! array_key_exists('portfolios', $post_data) || empty($post_data ['portfolios'])) {
            $error_message = 'The Portfolios object is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        $userId = $post_data ['id'];
        $portfolio = $post_data ['portfolios'];
        
        $this->Portfolios_model->upsert_portfolio($portfolio, $userId);
    }
    public function delete_userPortfolio($delete_data) {
        if (! array_key_exists('id', $delete_data) || empty($delete_data ['id'])) {
            $error_message = 'The User Id is required';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        // ensure that the user exists
        if (! $this->userExists($delete_data ['id'])) {
            $error_message = "User does not exist";
            throw new NSH_ResourceNotFoundException(220, $error_message);
        }
        
        if (! array_key_exists('portfolios', $delete_data)) {
            $delete_data ['portfolios'] = '';
        }
        
        $userId = $delete_data ['id'];
        $portfolios = $delete_data ['portfolios'];
        
        $this->Portfolios_model->delete_portfolio($portfolios, $userId);
    }
    private function upsert_user($post_data) {
        $userId = null;
        if (array_key_exists('id', $post_data) && ! empty($post_data ['id'])) {
            $userId = $post_data ['id'];
            
            if (! $this->userExists($userId)) {
                $error_message = 'User does not exist';
                throw new NSH_ResourceNotFoundException(220, $error_message);
            }
        }
        
        $modifiedDate = mdate(DATE_TIME_STRING, time());
        $data = array ();
        // ensure that the email address is not in use
        if (array_key_exists('emailAddress', $post_data) && ! empty($post_data ['emailAddress'])) {
            $emailInUse = $this->userEmailInUse($post_data ['emailAddress'], $userId);
            
            if ($emailInUse) {
                // return error that emailAddress is already in use
                throw new NSH_ValidationException(112);
            }
            
            $data ['emailAddress'] = $post_data ['emailAddress'];
        }
        
        // ensure that the username is not in use
        if (array_key_exists('username', $post_data) && ! empty($post_data ['username'])) {
            if ($this->userNameInUse($post_data ['username'], $userId)) {
                // return error that this username is not available
                throw new NSH_ValidationException(111);
            }
            
            $data ['username'] = $post_data ['username'];
        }
        
        $userQueryResult = array ();
        if (! empty($userId)) {
            $data ['modifiedDate'] = $modifiedDate;
            $this->db->update(USERS_TABLE, $data, array (
                    'id' => $userId
            ));
            // retrieve the updated user
            $userQueryResult = $this->db->get_where(USERS_TABLE, array (
                    'id' => $userId
            ))->row_array();
        } else {
            $data ['modifiedDate'] = $modifiedDate;
            $data ['createdDate'] = $modifiedDate;
            
            $this->db->insert(USERS_TABLE, $data);
            // retrieve the just created user
            $userQueryResult = $this->db->get_where(USERS_TABLE, array (
                    'emailAddress' => $post_data ['emailAddress']
            ))->row_array();
        }
        
        $user = new User();
        $user->id = $userQueryResult ['id'];
        $user->emailAddress = $userQueryResult ['emailAddress'];
        $user->username = $userQueryResult ['username'];
        $user->isActive = ($userQueryResult ['isActive'] == 1);
        
        return $user;
    }
    private function userEmailInUse($email, $userId = NULL) {
        $query = $this->db->get_where(USERS_TABLE, array (
                'emailAddress' => $email
        ));
        $row = $query->row_array();
        if ($row && count($row) > 0) {
            if ($userId == NULL) {
                return true;
            } else {
                return $row ['id'] != $userId;
            }
        }
        
        return false;
    }
    private function userNameInUse($username, $userId = NULL) {
        $query = $this->db->get_where(USERS_TABLE, array (
                'username' => $username
        ));
        $row = $query->row_array();
        if ($row && count($row) > 0) {
            if ($userId == NULL) {
                return true;
            } else {
                return $row ['id'] != $userId;
            }
        }
        
        return false;
    }
    private function userExists($userId) {
        $existingUser = $this->db->get_where(USERS_TABLE, array (
                'id' => $userId
        ))->row_array();
        
        return ($existingUser && ! empty($existingUser));
    }
    private function validateUserPostData($post_data) {
        $isNewUserCreation = (! array_key_exists('id', $post_data) || empty($post_data ['id']));
        if ($isNewUserCreation && (! array_key_exists('emailAddress', $post_data) || empty($post_data ['emailAddress']))) {
            $error_message = 'emailAddress is required for new User creation';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        if ($isNewUserCreation && (! array_key_exists('username', $post_data) || empty($post_data ['username']))) {
            $error_message = 'username is required for new User creation';
            throw new NSH_ValidationException(110, $error_message);
        }
        
        if (array_key_exists('credentialType', $post_data)) {
            if (! $this->equalIgnorecase($post_data ['credentialType'], STANDARD_CREDENTIALTYPE) && ! $this->equalIgnorecase($post_data ['credentialType'], FACEBOOK_CREDENTIALTYPE) && ! $this->equalIgnorecase($post_data ['credentialType'], GOOGLE_CREDENTIALTYPE)) {
                $error_message = 'credentialType should be either STANDARD, FACEBOOK or GOOGLE';
                throw new NSH_ValidationException(110, $error_message);
            }
            
            if ($this->equalIgnorecase($post_data ['credentialType'], STANDARD_CREDENTIALTYPE) && (! array_key_exists('password', $post_data) || empty($post_data ['password']))) {
                $error_message = 'password is required for STANDARD crendentialType';
                throw new NSH_ValidationException(110, $error_message);
            }
            
            if (! $this->equalIgnorecase($post_data ['credentialType'], STANDARD_CREDENTIALTYPE) && (! array_key_exists('socialId', $post_data) || empty($post_data ['socialId']))) {
                $error_message = 'SocialId is required for FACEBOOK or GOOGLE crendentialType';
                throw new NSH_ValidationException(110, $error_message);
            }
            
            if (array_key_exists('password', $post_data) && ! empty($post_data ['password'])) {
                // check password meets criteria
                Password_validation::validate($post_data ['password']);
            }
        }
        
        if (array_key_exists('attributes', $post_data)) {
            $this->UserAttributeValues_model->validateAttributes($post_data ['attributes']);
        }
    }
}
