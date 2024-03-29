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

// Utilizes CalPress template hooks to build out various pieces of the template

// Inspired by the work of Benedict Eastaugh
// and the Tarski Theme
// http://extralogical.net/2007/06/wphooks/
// http://tarski.googlecode.com/svn/trunk/


/**
 * calpress_template_blogtitle() - Outputs blog title in header.
 * 
 * @since 0.7
 * @return string
 */
 function calpress_template_blogtitle() {
     $home = get_bloginfo('home');
     $title = wp_specialchars( get_bloginfo('name'), 1 );
     $name = get_bloginfo('name');
     printf("<h1><span><a href=\"%s/\" title=\"%s\" rel=\"home\">%s</a></span></h1>", $home, $title, $name);
 }

/**
 * calpress_template_blogdescription() - Outputs blog description.
 * 
 * @since 0.7
 * @return string
 */
 function calpress_template_blogdescription() {
     echo "<span>". get_bloginfo('description') ."</span>";
 }
 

/**
* calpress_template_footer_credits() - Outputs footer credits.
* 
* @since 0.7
* @return string
*/
function calpress_template_footer_credits() {
    $date = date('Y');
    $name = get_bloginfo('name');
    printf("
    <div id=\"footer-credits\">
    <span class=\"copyright\">Copyright %s, %s</span>
    <span class=\"meta-sep\">|</span>
    <span class=\"poweredby\">Powered by <span id=\"generator-link\"><a href=\"http://wordpress.org/\" title=\"WordPress\" rel=\"generator\">WordPress</a></span> and the <span id=\"theme-link\"><a href=\"http://calpresstheme.org\" title=\"CalPress theme\" rel=\"designer\">CalPress</a> theme.</span></span>
    </div>", $date, $name);
}

/**
* calpress_template_comment_message() - Outputs comments messge
* 
* @since 0.7
* @return string
*/
function calpress_template_comment_message() {
    echo '<p id="comment-notes">Your email is <em>never</em> shared. Required fields are marked <span class="required">*</span></p>';
}


/**
* calpress_template_inline_above()
*
* works with helper.php calpress_inlines() 
* to hook content above story inlines.
* 
* @since 0.7
*/
function calpress_template_inline_above(){
    calpress_hook_postinline_above();
}

/**
* calpress_template_inline_below()
*
* works with helper.php calpress_inlines() 
* to hook content below story inlines.
* 
* @since 0.7
*/
function calpress_template_inline_below(){
    calpress_hook_postinline_below();
}
?>