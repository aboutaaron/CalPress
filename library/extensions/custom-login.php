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

/*
Use custom CalPress styles on login page

Based BM Custom Login plugin, GPL V.2
URI: http://www.binarymoon.co.uk/projects/bm-custom-login/
Author: Ben Gillbanks
Author URI: http://www.binarymoon.co.uk/
*/ 

// display custom login styles
function calpress_custom_login() {
	$logincss = PRIMARYCSS;
	echo '<link rel="stylesheet" type="text/css" href="' . $logincss . '" />';
}

function calpress_change_wp_login_url() {
    echo bloginfo('url');
}

function calpress_change_wp_login_title() {
    echo get_option('blogname');
}

add_action('login_head', 'calpress_custom_login');
add_filter('login_headerurl', 'calpress_change_wp_login_url');
add_filter('login_headertitle', 'calpress_change_wp_login_title');
?>