<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_creation_validation {
    
    public $validation_rules = array(
    array('field' => 'email', 'label' => 'Email', 'rules' => 'required'),
	array('field' => 'credentialType', 'label' => 'CredentialType', 'rules' => 'required')
	//TODO: username and password is required if credentialType == 'STANDARD'
	//TODO: socialId is required if credentialType == 'GOOGLE' || 'FACEBOOK'
	);
    
    
}

