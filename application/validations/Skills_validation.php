<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skills_validation {
    
    public $validation_rules = array(
	array('field' => 'categoryId', 'label' => 'Category Id', 'rules' => 'required'),
	array('field' => 'userId', 'label' => 'User Id', 'rules' => 'required'),
	array('field' => 'images', 'label' => 'images'),
	array('field' => 'videos', 'label' => 'videos'));
    
    
}

