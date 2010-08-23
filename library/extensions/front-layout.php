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


// Make homepage in CalPress selectable, with default layouts in CalPress
// and new layouts available in the /layouts/ folder of child themes. Child
// themes may also override parent theme layouts by using the same name.

// Get selected layout from CalPress Producer?
$layout = get_option('cp_layout');

// Define the generic blog.php layout as the default layout, if not already
if ($layout == "") {
    $layout = "blog.php";
}

$layout_template = 'layouts/'.$layout;
if ( file_exists(TEMPLATEPATH . '/' .$layout_template) ) { // current theme
    $front_template = TEMPLATEPATH . '/' .$layout_template ;
} elseif ( file_exists(CURRENTTEMPLATEPATH . '/' .$layout_template)  ) { // calpress theme
    $front_template = CURRENTTEMPLATEPATH . '/' .$layout_template ;
} else {
    $front_template = TEMPLATEPATH.'/layouts/blog.php';
}

//on home page, add a custom body class
add_action('calpress_hook_bodyclass', 'bodyclass_layout');
function bodyclass_layout(){
    global $layout;
    if (is_home()) {
        $bodyclass_layout = substr($layout, 0, -4);
        echo " front-layout-".$bodyclass_layout;
    }
}
?>