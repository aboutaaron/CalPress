<?php
function calpress_init() {
    
    //use google CDN jQuery
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
    wp_enqueue_script('jquery');  
    
    //SWFOject 1.5
    wp_enqueue_script('swfobject1.5', PARENTJS . '/swfobject.js');
    
    //ThickBox    
    wp_enqueue_script('thickbox');
     
    //Non-standard, Calpress-specific plugins and script
    wp_enqueue_script('calpress-min', PARENTJS . '/calpress-min.js');  
}    
add_action('init', 'calpress_init');
?>