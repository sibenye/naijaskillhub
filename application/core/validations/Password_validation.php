<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password_validation {
	
	const MIN_LENGTH = 8;
	const MAX_LENGTH = 60;
	
	private static $password_criteria = [			
				'min length' => self::MIN_LENGTH,
				'max length' => self::MAX_LENGTH 
			];

	public static function validate($password) {
		$isValid = self::validate_length($password);
		if (!$isValid)
		{
			throw new NSH_ValidationException(113, self::$password_criteria);
		}
	}
	
	private static function validate_length($param) {
		if ((strlen($param) >= self::MIN_LENGTH) && (strlen($param) <= self::MAX_LENGTH))
		{
			return true;
		}
		
		return false;
	}


}

