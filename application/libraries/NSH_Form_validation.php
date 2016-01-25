<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class NSH_Form_validation extends CI_Form_validation {
    
    public $validation_rules = array();
		
    /**
     * Class constructor
     *
     * @return  void
     */
	public function __construct($rules = array()) {
		$this->validation_rules = $rules;
		parent::__construct($this->validation_rules);
	}
    
    public function get_validation_rules()
    {
        return $this->validation_rules;
    }
	
	public function validate($data = array())
	{
		if (empty($data)){
			return;
		}
		$this->set_data($data);
		$this->run();
	}
}
