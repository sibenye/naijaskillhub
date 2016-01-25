<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
 
 /**
 * Template Class
 *
 * Responsible for loading templates.
 *
 */
class Template 
{
    var $nsh;
     
    function __construct() 
    {
        $this->nsh =& get_instance();
    }
    
    /**
     * load
     *
     * It takes the name of the template being requested 
     * and the view and loads thems.
     *
     * @param   string  $tpl_name Template file name
     * @param   string  $body_view View file name
     * @param   string  $data The view data bag
     * @return  void
     */
    
    function load($tpl_name, $body_view = null, $data = null) 
    {
        if ( ! is_null( $body_view ) ) 
        {
            if ( file_exists( APPPATH.'views/'.$body_view ) ) 
            {
                $body_view_path = $body_view;
            }
            else if ( file_exists( APPPATH.'views/'.$body_view.'.php' ) ) 
            {
                $body_view_path = $body_view.'.php';
            }
            else
            {
                show_error('Unable to load the requested file: ' . $body_view.'.php');
            }
             
            $body = $this->nsh->load->view($body_view_path, $data, TRUE);
             
            if ( is_null($data) ) 
            {
                $data = array('body_content' => $body);
            }
            else if ( is_array($data) )
            {
                $data['body_content'] = $body;
            }
            else if ( is_object($data) )
            {
                $data->body_content = $body;
            }
        }
    
        if ( file_exists( APPPATH.'views/templates/'.$tpl_name ) ) 
        {
            $tpl_view_path = 'templates/'.$tpl_name;
        }
        else if ( file_exists( APPPATH.'views/templates/'.$tpl_name.'.php' ) ) 
        {
            $tpl_view_path = 'templates/'.$tpl_name.'.php';
        }
        else
        {
            show_error('Unable to load the requested file: ' . $tpl_name.'.php');
        }
        
        $this->nsh->load->view($tpl_view_path, $data);
    }
}    
    