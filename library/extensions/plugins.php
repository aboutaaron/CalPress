<?php
/*
    Manage / config various plugins that *may* be installed alongside CalPress
*/


/* 
    WordPress Event Calendar
    http://wpcal.firetree.net
    We never need the CSS inserted into the head. And we only 
    need the JS on pages in the event category
*/

if (function_exists('ec3_action_wp_head')) {
    global $ec3; $ec3->nocss=true;//never load CSS
    
    if (is_category('events') ) { //don't load calendar J/S except on event category page
        //remove_filter('wp_head','ec3_action_wp_head');
    }
}
?>