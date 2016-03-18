<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class user {
    
    public $userId = NULL;
	public $email = '';
	public $isActive = FALSE;
	public $firstName = '';
	public $lastName = '';
	public $credentialTypes = array();
}
