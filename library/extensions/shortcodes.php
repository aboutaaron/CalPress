<?php
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