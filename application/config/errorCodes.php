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
			117 => 'Parent CategoryId cannot be the same as the CategoryId',
			118 => 'The Category name is already in use',
			119 => 'The User Attribute name is already in use',
			120 => 'Invalid User attribute',
			121 => 'resetToken is expired',
			122 => 'resetToken is invalid',
			123 => 'activationToken is invalid',
			220 => 'Object Not Found',
			222 => 'User does not have standard credential',
			223 => 'Standard credentials cannot be deleted, only social credentials',
			224 => 'Cannot delete the only remaining credential',
			225 => 'CredentialType does not exist for User',
			230 => 'User already has portfolio in this category'
		);	
}


