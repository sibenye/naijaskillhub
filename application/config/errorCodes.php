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
			115 => 'Invalid old password',
			116 => 'New password cannot be the same as the old password',
			120 => 'Invalid User attribute',
			121 => 'resetToken is expired',
			122 => 'resetToken is invalid',
			123 => 'activationToken is invalid',
			220 => 'Object Not Found',
			221 => 'User does not have standard credential'
		);	
}


