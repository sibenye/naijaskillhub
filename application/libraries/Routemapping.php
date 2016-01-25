<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Routemapping Class
 *
 * Responsible for mapping url names to appropriate site url.
 *
 */
class Routemapping {
    
    var $nsh;
     
    function __construct() 
    {
        $this->nsh =& get_instance();
    }

    /**
     * Site URL wrapper
     * It wraps the original CI site_url helper function
     * Create a local URL based on the basepath
     * It maps the url name to the appropriate controller url segment and appends it to the basepath
     * It also appends the action and the parameter segments passed in. 
     * And then calls the CI site_url function.
     * 
     * @param   string  $url_name   url name
     * @param   string  $action action: create or edit or delete or view
     * @param   string  $param  
     * @return  string
     */
    public function site_url($url_name, $action = NULL, $param = NULL)
    {
        $url_format = $this->nsh->config->item($url_name); //this config is located in config/routemapping.php
        switch ($action) {
            
            case "create":
                return site_url($url_format.'/create');
                break;
            case "edit":
                if ($param === NULL){
                    show_error('$param cannot be NULL for action type: '.$action);
                }
                return site_url($url_format.'/'.$param.'/edit');
                break;
            case "delete":
                if ($param === NULL){
                    show_error('$param cannot be NULL for action type: '.$action);
                }
                return site_url($url_format.'/'.$param.'/delete');
                break;
            case "view":
                if ($param === NULL){
                    show_error('$param cannot be NULL for action type: '.$action);
                }
                return site_url($url_format.'/'.$param);
                break;
            default:
                return site_url($url_format);
        }
    }

    /**
     * form_open wrapper
     * A wrapper to the original CI form_open helper function
     * It accepts a url name and maps it to the appropriate url
     * and the calls the CI form_open function
     * 
     * @param   string  $url_name    url name
     * @param   string  action name
     * @param   array   a key/value pair of attributes
     * @param   array   a key/value pair hidden data
     * @return  string
     * 
     */
    public function form_open($url_name, $action, $attributes = array(), $hidden = array())
    {
        $url_format = $this->nsh->config->item($url_name); //this config is located in config/routemapping.php
        
        switch ($action) {
            
            case "create":
                return form_open($url_format.'/create', $attributes, $hidden);
                break;
            case "edit":
                return form_open($url_format.'/edit', $attributes, $hidden);
                break;
            default:
                return form_open($url_format.'/create', $attributes, $hidden);
            
        }
    }
	
	function form_open_multipart($url_name, $action, $attributes = array(), $hidden = array())
	{
		$url_format = $this->nsh->config->item($url_name); //this config is located in config/routemapping.php
        
        switch ($action) {
            
            case "create":
                return form_open_multipart($url_format.'/create', $attributes, $hidden);
                break;
            case "edit":
                return form_open_multipart($url_format.'/edit', $attributes, $hidden);
                break;
            default:
                return form_open_multipart($url_format.'/create', $attributes, $hidden);
            
        }
	}
}