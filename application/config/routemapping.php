<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
* This config file defines the mapping of url names to route formats
*/
 
$admin_url_prefix = ADMIN_URL_PREFIX;
 
$config[ADMIN_USER_ATTRIBUTES] = $admin_url_prefix.'/userAttributes';
$config[ADMIN_SKILL_CATEGORIES] = $admin_url_prefix.'/skillCategories';
$config[ADMIN_SKILLS] = $admin_url_prefix.'/skills';

