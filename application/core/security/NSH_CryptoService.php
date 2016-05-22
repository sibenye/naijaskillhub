<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class NSH_CryptoService
{
	const COST = '10';
	
	public static function secure_hash($password)
	{
		$options = ['cost' => self::COST];
			
		$hashed_pwd = password_hash($password, PASSWORD_BCRYPT, $options);
		
		if ($hashed_pwd == NULL || !$hashed_pwd)
		{
			throw new NSH_Exception(100, 'Error hashing password');
		}
		
		return $hashed_pwd;
	}
	
	public static function generate_salt()
	{
		return mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
	}
	
	public static function is_verified($password, $hash)
	{
		return password_verify($password, $hash);
	}
}