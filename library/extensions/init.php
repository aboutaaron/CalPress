<?php
function calpress_init() {
	
	//load Google's jQuery if not in admin (faster CDN). 
	//In admin, we need the default, which is in no-conflicts mode
	if( !is_admin()){
	   wp_deregister_script('jquery'); 
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"), false, '1.3.2'); 
	}
	
 	//jQuery
    wp_enqueue_script('jquery');  
    
	if( !is_admin()){
		//SWFOject 1.5. Default is 2.0.
	    wp_enqueue_script('swfobject1.5', PARENTJS . '/swfobject.js');

	    //ThickBox    
	    wp_enqueue_script('thickbox');		
	}
     
    //Non-standard, Calpress-specific plugins and scripts
    //wp_enqueue_script('calpress-min', PARENTJS . '/calpress-min.js');  
}    
add_action('init', 'calpress_init');
?>