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

/*
    Most article pages will load the default page template in single.php. 
    Howerver, there are times we want to load special templates. 
    loadSpecialSingle() is wrapper function to the 
    calPressSpecialSingle hook. Child templates can have their
    own loadSpecialSingle that includes custom code and then calls
    the calPressSpecialSingle()
*/

function loadSpecialSingle(){
    return calPressSpecialSingle();
}

function calPressSpecialSingle(){
    $rboolean = false;
    
    /* if Events Calendar3 Plugin is installed & we're in the events category, load events template */
    if (function_exists('ec3_action_wp_head')) {
        if (in_category('events') ) { 
            require_once(TEMPLATEPATH . '/event.php');
            $rboolean = true;
        }
    }    
    return $rboolean;
}
?>