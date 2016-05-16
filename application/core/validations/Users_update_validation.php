<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_update_validation {
    
    public $validation_rules = array(
    array('field' => 'userId', 'label' => 'UserId', 'rules' => 'required')
	);
    
    
}

