<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait NSH_Utils 
{
	
	function equalIgnorecase($str1, $str2) 
	{
		if ($str1 == NULL && $str2 == NULL)
		{
			return true;
		}
		
		if (empty($str1) || empty($str2))
		{
			return false;
		}
		
		return (strtolower($str1) == strtolower($str2));
	}
}