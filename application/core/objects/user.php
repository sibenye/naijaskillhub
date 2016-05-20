<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class user {
    
    public $id = NULL;
	public $emailAddress = '';
	public $username = '';
	public $isActive = FALSE;
	public $credentialTypes = array();
}
