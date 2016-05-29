<?php
defined('BASEPATH') OR exit('No direct script access allowed');


trait NSH_CryptoService
{
	private  $COST = '10';
		
	public function secure_hash($password)
	{
		$options = ['cost' => $this->COST];
			
		$hashed_pwd = password_hash($password, PASSWORD_BCRYPT, $options);
		
		if ($hashed_pwd == NULL || !$hashed_pwd)
		{
			throw new NSH_Exception(100, 'Error hashing password');
		}
		
		return $hashed_pwd;
	}
	
	public function generate_salt()
	{
		return $this->secure_random();
	}
	
	public function secure_random()
	{
		return mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
	}
	
	public function is_verified($password, $hash)
	{
		return password_verify($password, $hash);
	}
	
	public function encode($str)
	{
		$this->load->library('encrypt');
		return $this->encrypt->encode($str);
	}
	
	public function decode($str)
	{
		$this->load->library('encrypt');
		return $this->encrypt->decode($str);
	}
}