<?php
// This file is part of the CalPress Theme for WordPress
// http://calpresstheme.org
//
// CalPress is a project of the University of California 
// Graduate School of Journalism
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

// Inspired by the work of Benedict Eastaugh
// and the Tarski Theme
// http://extralogical.net/2007/06/wphooks/
// http://tarski.googlecode.com/svn/trunk/


/**
 * calpress_hook_blogtitle()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in the <div id="blog-title"> block.
 * @example add_action('calpress_hook_blogtitle', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_blogtitle
 */
function calpress_hook_blogtitle() {
	do_action('calpress_hook_blogtitle');
}

/**
 * calpress_hook_header_pretitle()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in the <div id="pre-title"> block.
 * @example add_action('calpress_hook_header_pretitle', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_header_pretitle
 */
function calpress_hook_header_pretitle() {
	do_action('calpress_hook_header_pretitle');
}

/**
 * calpress_hook_header_posttitle()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in the <div id="footer"> block.
 * @example add_action('calpress_hook_header_posttitle', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_header_posttitle
 */
function calpress_hook_header_posttitle() {
	do_action('calpress_hook_header_posttitle');
}

/**
 * calpress_hook_blogdescription()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in the <div id="blog-description"> block.
 * @example add_action('calpress_hook_header_blogdescription', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_blogdescription
 */
function calpress_hook_blogdescription() {
	do_action('calpress_hook_blogdescription');
}

/**
 * calpress_hook_footer()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in the <div id="footer"> block.
 * @example add_action('calpress_hook_footer', 'my_function');
 * @since 0.7
 * @hook action calpress_hook_footer
 */
function calpress_hook_footer() {
	do_action('calpress_hook_footer');
}

/**
 * calpress_hook_bodyclass()
 *
 * Template function appearing in header.php, allows actions
 * to be executed in loop that produces <body class="XXX XXX XXX"
 * @example add_action('calpress_hook_bodyclass', 'my_function');
 * @example see front-layout.php for working example
 * @since 0.7
 * @hook action calpress_hook_bodyclass
 */
function calpress_hook_bodyclass() {
	do_action('calpress_hook_bodyclass');
}

/**
 * calpress_hook_frontfeatures_below()
 *
 * Template function appearing in home.php, allows actions
 * to be executed below the default loop.
 * @example add_action('calpress_hook_frontfeatures_below', 'my_function');
 * @example see home.php
 * @since 0.7
 * @hook action calpress_hook_frontfeatures_below
 */
function calpress_hook_frontfeatures_below() {
	do_action('calpress_hook_frontfeatures_below');
}

/**
 * calpress_hook_frontfeatures_above()
 *
 * Template function appearing in home.php, allows actions
 * to be executed above the default loop.
 * @example add_action('calpress_hook_frontfeatures_above', 'my_function');
 * @example see home.php
 * @since 0.7
 * @hook action calpress_hook_frontfeatures_above
 */
function calpress_hook_frontfeatures_above() {
	do_action('calpress_hook_frontfeatures_above');
}

/**
 * calpress_hook_loopcontent_above()
 *
 * Template function appearing in loop-content.php, allows actions
 * to be executed above the default loop content.
 * @example add_action('calpress_hook_loopcontent_above', 'my_function');
 * @example see home.php
 * @since 0.7
 * @hook action calpress_hook_loopcontent_above
 */
function calpress_hook_loopcontent_above() {
	do_action('calpress_hook_loopcontent_above');
}

/**
 * calpress_hook_loopcontent_below()
 *
 * Template function appearing in loop-content.php, allows actions
 * to be executed below the default loop content.
 * @example add_action('calpress_hook_loopcontent_below', 'my_function');
 * @example see home.php
 * @since 0.7
 * @hook action calpress_hook_loopcontent_below
 */
function calpress_hook_loopcontent_below() {
	do_action('calpress_hook_loopcontent_below');
}
?>