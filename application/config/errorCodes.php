<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Error codes definitions
 */
class errorCodes {
	
	public static $codes = array(
			100 => 'An Error Occured',
			110 => 'Validation Error',
			111 => 'This username is not available',
			112 => 'This emailAddress is already in use',
			113 => 'Password does not meet criteria',
			114 => 'User credential already exists',
			120 => 'Invalid User attribute',
			220 => 'Object Not Found'
		);	
}


