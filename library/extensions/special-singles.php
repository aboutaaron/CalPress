<?php
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