<?php
/*
Use custom CalPress styles on login page

Pulled almost directly from BM Custom Login
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