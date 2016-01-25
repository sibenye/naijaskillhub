<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skills_validation {
    
    public $validation_rules = array(
    array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
	array('field' => 'categoryId', 'label' => 'Category Id', 'rules' => 'required'));
    
    
}

