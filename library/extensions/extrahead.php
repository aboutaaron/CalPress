<?php
/*
    Allow post and pages to put stuff into the head of a document
*/


// add extra css to the head of the document
function calpress_extracss($postid){
    if ( get_post_custom_values('extra_css', $postid) ) {
        $css = get_post_custom_values('extra_css', $postid);
        foreach($css as $v){
            echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"$v\" />\n");
        }
    }
}
// add extra js to the head of the document
function calpress_extrajs($postid){
    if ( get_post_custom_values('extra_js', $postid) ) {
        $js = get_post_custom_values('extra_js', $postid);
        foreach($js as $v){
            echo("<script type='text/javascript' src='".$v."'></script>\n");
        }
    }
}
function calpress_extrahead(){
    global $wp_query;
    $thePostID = $wp_query->post->ID;
    calpress_extracss($thePostID);
    calpress_extrajs($thePostID);
}
add_action('wp_head', 'calpress_extrahead');



// For front page only, add extra CSS or JS if specified in CalPress Producer
function calpress_front_extra_head(){
    if (is_home()){
        $get_front_js = THEMESHORTNAME."_front_extra_js"; // get value from admin
        $front_js = trim(get_settings($get_front_js));
        if($front_js != ""){
            echo("<script type='text/javascript' src='".$front_js."'></script>\n");
        }

        $get_front_css = THEMESHORTNAME."_front_extra_css"; // get value from admin
        $front_css = trim(get_settings($get_front_css));
        if($front_css != ""){
            echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"$front_css\" />\n");
        }
    }
}
add_action('wp_head', 'calpress_front_extra_head');
?>