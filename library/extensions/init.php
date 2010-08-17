<?php
// This file is part of the CalPress Theme for WordPress
// http://calpresstheme.org
//
// CalPress is a project of the University of California 
// Berkeley Graduate School of Journalism
// http://journalism.berkeley.edu
//
// Copyright (c) 2010 The Regents of the University of California
//
// Released under the GPL Version 2 license
// http://www.opensource.org/licenses/gpl-2.0.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

/**
 * calpress_init() - Script to run on page initialization
 * 
 * @since 0.7
 */
function calpress_init() {
	
	//load Google's jQuery if not in admin (faster CDN). 
	//In admin, we need the default, which is in no-conflicts mode
	if( !is_admin()){
	   wp_deregister_script('jquery'); 
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"), false, '1.4.2'); 
	}
	
 	//jQuery
    wp_enqueue_script('jquery');  
    
	if( !is_admin()){
		//SWFOject 1.5. Default is 2.0.
	    wp_enqueue_script('swfobject1.5', PARENTJS . '/swfobject.js');

	    //ThickBox    
	    wp_enqueue_script('thickbox');		
	}
     
    //Non-standard, Calpress-specific plugins and scripts
    //wp_enqueue_script('calpress-min', PARENTJS . '/calpress-min.js');  
}    
add_action('init', 'calpress_init');


/**
 * register_calpress_global_menu() - Registers WP 3.0 menu
 * 
 * @since 0.7
 */
function register_calpress_global_menu() {
    if ( function_exists('wp_nav_menu') ){
        register_nav_menu( 'nav-bar', __( 'Nav Bar' ) );
    }
	
}
add_action( 'init', 'register_calpress_global_menu' );
?>