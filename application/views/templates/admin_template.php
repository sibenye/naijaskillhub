<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 

$this->load->view('partial_views/admin_header.php');

$this->load->view('partial_views/admin_menu.php');

echo $body_content;

$this->load->view('partial_views/admin_footer.php');


