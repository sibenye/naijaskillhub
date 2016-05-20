<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_creation_validation {
    
    public $validation_rules = array(
	    array('field' => 'emailAddress', 'label' => 'EmailAddress', 'rules' => 'required'),
		array('field' => 'credentialType', 'label' => 'CredentialType', 'rules' => 'required')
	);
    
    
}

