<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Defines common exception functions that are globally available
 */
 
if ( ! function_exists('show_validation_exception'))
{
    function show_validation_exception($message)
    {
        $_error =& load_class('Exceptions', 'core');
        echo $_error->show_validation_exception($message);
        exit;
    }
}

if ( ! function_exists('show_resourceNotFound_exception'))
{
    function show_resourceNotFound_exception($message)
    {
        $_error =& load_class('Exceptions', 'core');
        echo $_error->show_resourceNotFound_exception($message);
        exit;
    }
}

if ( ! function_exists('show_nsh_exception'))
{
    function show_nsh_exception($message)
    {
        $_error =& load_class('Exceptions', 'core');
        echo $_error->show_nsh_exception($message);
        exit;
    }
}

