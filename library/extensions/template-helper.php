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
 ?>