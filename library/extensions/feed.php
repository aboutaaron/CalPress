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


/* Lead Art Enclosures */
add_action('rss_item', 'calpress_rss_leadart_enclosure');
add_action('rss2_item', 'calpress_rss_leadart_enclosure');

/* Lead Art Embeded in content */
add_filter("the_content", "calpress_rss_leadart_embed");


/**
 * If no other enclosure in feed (like an mp3), put in lead art if available
 *
 * @return none
 */
function calpress_rss_leadart_enclosure(){
    
    // Add a lead art enclosure
    // see if art is applicable and make sure there's not already an enclosure in the post
    $previous_enclosure = get_post_custom_values('enclosure');
    if (calpress_showleadart() && count($previous_enclosure==0)) {
        
        $enclosure = "";
        
        //show an image? most basic and likely lead art
        if (calpress_showteaseart()) {
            $img_url = calpress_leadimagepath();
            $sized_image = calpress_sizedimageurl($img_url, 480);//only send a 480px sized version
            $img_header = get_headers($sized_image, 1);   
            $filesize = $img_header['Content-Length'];
            $enclosure = "<enclosure url='".$sized_image."' length ='".$filesize."'  type='image/jpg' />";
        }
        
        //show a mov instead of the image?
        if ( get_post_custom_values('lead_video') ){
            $videos = get_post_custom_values('lead_video');
            $options = calpress_parsextrafieldsoptions($videos[0]);
            $slug = $options[0];
            $path = VIDEOLOCATION.$slug.'/'.$slug.'.mov';
            
            $file_header = get_headers($path, 1);   
            $filesize = $file_header['Content-Length'];
            $enclosure = "<enclosure url='".$path."' length ='".$filesize."'  type='video/quicktime' />";
        } elseif ( get_post_custom_values('lead_youtube') ){
            $videos = get_post_custom_values('lead_youtube');
            $options = calpress_parsextrafieldsoptions($videos[0]);
            $slug = $options[0];
            $path = "http://i3.ytimg.com/vi/".$slug."/hqdefault.jpg";
            
            $file_header = get_headers($path, 1);   
            $filesize = $file_header['Content-Length'];
            $enclosure = "<enclosure url='".$path."' length ='".$filesize."'  type='image/jpg' />";
        }

        
        if ($enclosure != "") {
            echo $enclosure;
        }
        
    }
}

/**
 * Embed various lead art in rss posts
 *
 * @return string
 */
function calpress_rss_leadart_embed($content = ''){

	
	if (is_feed()) {
	    
	    //show an image? most basic and likely lead art
        if (calpress_showteaseart()) {
            $img_url = calpress_leadimagepath();
            $sized_image = calpress_sizedimageurl($img_url, 480);//only send a 480px sized version
            $art = "<img width=\"480\" src=\"$sized_image\" />";
        }
        
        //TODO - add youtube, etc
        
		$content = $art . $content;
	}
	
	return $content;
}

?>