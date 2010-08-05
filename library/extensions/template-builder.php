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

//Header
add_action('calpress_hook_blogtitle', 'calpress_template_blogtitle');
add_action('calpress_hook_blogdescription', 'calpress_template_blogdescription');

//Single
add_action('calpress_hook_post_below', 'calpress_twittersearch');
add_action('calpress_hook_post_below', 'comments_template');

//Footer
add_action('calpress_hook_footer', 'calpress_template_footer_credits');

?>