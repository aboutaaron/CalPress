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

// Custom CalPress shortcodes

function pullquote_shortcode( $atts, $content = null ) {
    if($atts['align']){
        if($atts['align'] == "right"){
            $float = "right";
        }else{
            $float = "left";
        }
        return '<blockquote class="pullquote '. $float .'"><p>' . trim($content) . '</p></blockquote>';
    } else {
        return '<blockquote class="pullquote"><p>' . trim($content) . '</p></blockquote>';
    }
}
add_shortcode('pullquote', 'pullquote_shortcode');
?>