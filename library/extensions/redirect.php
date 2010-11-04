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

// Based on Ralf Hortt's Redirector plugin for WP
// http://www.horttcore.de/wordpress/redirector


/**
 * Redirect page or post with a "redirect" extra field to the value of that field
 *
 * @param none
 * @return void
 */
function calpress_redirect(){
    global $wp_query, $post;
  
    if (is_single() || is_page()) {
        $redirect = get_post_meta($wp_query->post->ID, 'redirect', true);
        if ($redirect != '')
		{   
			wp_redirect($redirect);
			header("Status: 301");
			exit;
		}
    }
}
add_action('template_redirect', 'calpress_redirect');

/**
 * Return redirect link
 *
 * @param none
 * @return void
 */
function calpress_redirect_get_link(){
    global $wp_query, $post;
    setup_postdata($post);
    $thePostID = $post->ID;
    
    $redirect = get_post_meta($thePostID, 'redirect', true);
    
    if ($redirect != '')
	{   
	    return $redirect;
	}
	else{
	    return false; 
	}
} 

/**
 * Return redirect link domain
 *
 * @param none
 * @return void
 */
function calpress_redirect_get_domain(){
    if (calpress_redirect_get_link() !== false) {
        $url = calpress_redirect_get_link();
        $url = parse_url(trim($url));
        return trim($url[host] ? $url[host] : array_shift(explode('/', $url[path], 2)));
    } else{
        return false;
    }
} 


 
?>