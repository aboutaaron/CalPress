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


// Basic CalPress extensions

/**
 * Generate custom byline text
 *
 * WP only allows 1 byline. We need multiples on some stories. Invoke with 
 * the use of a single "byline" extra field per name
 *
 * @param obj $authordata
 * @return void
 */
function calpress_bylines($authordata = null){
    global $post;
    $thePostID = $post->ID;
    
    if ( get_post_custom_values('byline', $thePostID ) ) {
        echo("<span class=\"entry-byline\">By: ");
        $byline_values = get_post_custom_values('byline', $thePostID);
        $byline = '';
        foreach ( $byline_values as $key => $value ) {
           $byline = $byline . $value . ", ";
        }
        $byline = substr($byline, 0, -2);//delete last comma and space
        echo($byline);
        echo("</span>");
    } else { ?>
        <span class="entry-byline">By: <?php printf( __('<a class="url fn n" href="' . get_author_posts_url( $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'sandbox' ), $authordata->display_name ) . '">' . get_the_author() . '</a>')) ?></span>
        <?php }
}


/**
 * Display Google Analytics code in footer if GA ID is set in the admin
 *
 * @return void
 */
function calpress_googleanalytics() {
    
    // get value from admin
    $get_ga_id = THEMESHORTNAME."_ga";
    $gaID = trim(get_settings($get_ga_id));
    
    // see if its value is not blank
    if ($gaID != ""){
	$gacode = '<!-- GOOGLE ANALYTICS TRACKING CODE -->
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push([\'_setAccount\', \''. $gaID . '\']);
		_gaq.push([\'_trackPageview\']);

		(function() {
		var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
		ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
		var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>'."\n";
        echo $gacode;
    }
}
add_action('wp_head', 'calpress_googleanalytics');

/**
 * Display ShareThis Code to the top of posts if id set in admin
 *
 * @return void
 */
function calpress_sharethis(){
    
    // get value from admin
    $get_sharethis_id = THEMESHORTNAME."_sharethis";
    $sharethisID = trim(get_settings($get_sharethis_id));
    
    // see if its value is not blank
    if ($sharethisID != ""){
        $sharethiscode = '<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher='. $sharethisID .'&amp;type=website&amp;buttonText=Share%20this%20Article!&amp;style=rotate&amp;post_services=email%2Cfacebook%2Ctwitter%2Cdigg%2Cmyspace%2Csms%2Creddit%2Cdelicious%2Cstumbleupon%2Cgoogle_bmarks%2Clinkedin%2Cnewsvine%2Cybuzz%2Cblogger%2Cyahoo_bmarks%2Cmixx%2Ctechnorati%2Cfriendfeed%2Cpropeller%2Cwordpress%2Cxanga"></script>';
        
        echo("<div class=\"entry-sharethis\">". $sharethiscode ."</div>");
    }
}

/**
 * List pages in global nav unless told not to in the admin
 *
 * Lifted almost directly from Sandbox, produces a list of pages in the 
 * header without whitespace
 *
 * @param string $g -  grid size. default is 12. 16 is also an option
 * @return void
 */
function calpress_globalnav( $g = "12" ) {
    
    // get whether this should be shown from the admin
    $get_hide_nav_menu = THEMESHORTNAME."_hide_nav_menu";
    $shownav = trim(get_settings($get_hide_nav_menu));
    if (!$shownav) {
        if ( $g == "16" ) {
            $grid = "16";
        } else {
            $grid = "12";
        }
    	
    	if (function_exists('wp_nav_menu') && has_nav_menu( 'nav-bar' ) ) {//wordpress 3.0 menu builder
    	    wp_nav_menu( array( 'container' => 'div', 'container_class' => 'grid_12', 'container_id' => 'menu', 'theme_location' => 'nav-bar' ) ); 
    	    echo '<div class="clear"></div>';
    	}else{// <= 2.9
    	    if ( $menu = str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages('title_li=&sort_column=menu_order&echo=0') ) )
        		$menu = '<ul>' . $menu . '</ul>';
        	$menu = '<div id="menu" class="grid_' . $grid . '">' . $menu . '</div><div class="clear"></div>';
        	echo apply_filters( 'globalnav_menu', $menu ); // Filter to override default globalnav: globalnav_menu  
    	}
    }	
}

/**
 * Extend Sandbox's body_class function, adding more classes
 *
 * Additions:
 *	1. User-specified body class via custom field "body_class". One class per custom field
 *	2. Add a body.grid class if query string ?grid=true exists
 *
 * @param boolean $print - print out the new body classes
 * @return string
 */
// 
function calpress_body_class( $print = true ) {
    global $post;
    $c = sandbox_body_class(false);
    
    $bc = array();
    
    // Users can specify a body classes in custom fields
    if ( get_post_custom_values('body_class') ) {
        $body_class_values = get_post_custom_values('body_class');
        foreach ( $body_class_values as $key => $value ) {
           $bc[] = trim(strip_tags($value));
        } 
    }
    
    // If grid=on in query string, set in body class
    if ( $_GET['grid'] === "true" ) {
        $bc[] = "grid";
    }
    
    if(count($bc) > 0){
        $bc = join( ' ', apply_filters( 'post_class', $bc ) ); // Available filter: post_class
        $c = trim($c." ".$bc);
    }
    
    return $print ? print($c) : $c;
}

/**
 * Wrapper around Sandbox's post_class function
 *
 * @param boolean $print - print out the new body classes
 * @return string
 */
function calpress_post_class( $print = true ) {
    global $post;
    $c = sandbox_post_class(false);
    
    return $print ? print($c) : $c;
}

/**
 * Wrapper around Sandbox's sandbox_comment_class function
 *
 * @param boolean $print - print out the comment classes
 * @return string
 */
function calpress_comment_class( $print = true ) {
    global $post;
    $c = sandbox_comment_class(false);
    
    return $print ? print($c) : $c;
}

/**
 * Explode and trim whitespace
 *
 * Returns an array of strings, each of which is a substring of $e split by $s with whitespace removed.
 *
 * @param string $s = search string
 * @param string $e = string to explode
 * @return array
 */
//
function calpress_explodeandtrim($e,$s){
    $totrim = explode($e,$s);
    $a = array();
    foreach($totrim as $t) {
        $a[]=trim($t);
    }
    return $a;
}

/**
 * Parse latitude and longitude
 *
 * Parse a string of format "lat,lng" from custom field and return it as array of 
 * lat/long values. See calpress_mappoints().
 *
 * @param string $s = single lat/lng string of format: "lat,lng"
 * @return array
 */
//
function calpress_parselatlong($s){
    return calpress_explodeandtrim(",",$s);
}

/**
 * From the maps options passed via custom fields in calpress_inlines, find the 
 * points and info titles. Accepts a string of options of this format:
 *  
 * center lat/center long,zoom,map type,title,cutline,map point,point info,map point,point info
 *
 * Returns an array of this format:
 * 
 * (
 *     [0] => Array
 *         (
 *             [0] => Array
 *                (
 *                     [0] => 34.875356
 *                     [1] => -124.260817
 *                 )
 *            [1] => Array
 *                 (
 *                     [0] => 38.938832
 *                     [1] => -77.047119
 *                 )
 *         )
 *     [1] => Array
 *         (
 *             [0] => Bob's House
 *             [1] => John's House
 *         )
 * )
 * 
 * @param string $o = map options array. 
 * @return array
 */
function calpress_mappoints($o){
    $a = array_slice($o,5);//start from 5 option in map options
    $points = array();
    $info = array();
    $i = 0;
    foreach( $a as $k => $v){
        $valid_latlng_regex = "[-]?[0-9][.]{0,1}[0-9]{4}";
        if ($k % 2 === 0) {
             $ll = calpress_parselatlong($v);
            //verify the values are lat/long
            //TODO: this isn't returning false... check regex pattern
            if ( ereg($valid_latlng_regex, $ll[0]) && ereg($valid_latlng_regex, $ll[1]) ) {
                $points[] = $ll;
                $info[] = $a[$i+1];
            }
        }
        $i++; 
    }
    return array($points,$info);
}

/**
 * Check if an extra field matches a given pattern
 *
 * @param string $s = string to check
 * @param string / array $p = either a string regex pattern to compare against or an array of regex values
 * @return boolean
 */
function calpress_extrafieldtest($pattern, $string){
    if( is_array($pattern) ) {
        $b = false;
        foreach( $pattern as $p ) {
            if( ereg($p, $string) ) {
                $b = true;
                break;
            }
        }
        return $b;
    } else {
        if ( ereg($pattern, $string) ) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Parse the pipe delimited extra field info into array
 *
 * @param string $s = delimited options of the format: "option1|options2|"
 * @return array
 */
function calpress_parsextrafieldsoptions($s){
    return calpress_explodeandtrim("|", $s);
}

/**
 * Determine height of video / photo elements based on their width, for either 16x9 or 4x3 media
 *
 * @param int $w = known width of the element
 * @param string $r = desired ratio. 43 for 4x3. 169 for 16x9 (default).
 * @param string $f = media format. If video, add height for play bar
 * @return int
 */
function calpress_mediaheight($w,$r="169",$f="photo"){
    if ($r == "43") {
        $h = round($w * .75);
    } else {
        $h = round($w * .5626);
    }
    //add video control bar height
    if ($f == "video") {
        $h = $h + MEDIAPLAYERCONTROLERHEIGHT;
    }
    return $h;
}

// Determine the width of media in the sidebar. It is the sidebar width minus and padding
function calpress_sidebarmediawidth(){
    return SIDEBARWIDTH;
}

/**
 * Display the lead image of an asset (billboard image, etc). no cutline
 *
 * @param int $w = known width of the element
 * @param string $ration = desired ratio. 43 for 4x3. 169 for 16x9 (default). currently unused. will probably go away. 
 * @return void
 */
function calpress_teaseart($w = LEADARTSIZE, $ratio = "169"){
	global $posts;
    if(calpress_postimage()!=false) { //show the lead art photo
        $srcimg = calpress_leadimagepath();//uploaded photo
        $imgsrc = calpress_sizedimageurl($srcimg, $w);
        
        echo("<div class=\"tease-art entry-leadphoto\">");
            if (is_single(get_the_ID())) {
                echo ('<img alt="" src ="'.$imgsrc.'" />');//on article. no need to make image a link
            }else{
                $link = get_permalink();
                echo ('<a href="'.$link.'"><img alt="" src ="'.$imgsrc.'" /></a>');
            }
            
        echo("</div>");  
    }
}

/**
 * Display a cropped version of the lead image of an asset (billboard image, etc). no cutline
 *
 * @param int $w = known width of the element
 * @param int $h = height of the element
 * @return void
 */
function calpress_teaseart_cropped($w = LEADARTSIZE, $h){
	global $posts;
    if(calpress_postimage()!=false) { //show the lead art photo
        $srcimg = calpress_leadimagepath();//uploaded photo
        $imgsrc = calpress_croppedimageurl($srcimg, $w, $h); // resized image url
        
        echo("<div class=\"tease-art entry-leadphoto\">");
            if (is_single(get_the_ID())) {
                echo ('<img alt="" src ="'.$imgsrc.'" />');//on article. no need to make image a link
            }else{
                $link = get_permalink();
                echo ('<a href="'.$link.'"><img alt="" src ="'.$imgsrc.'" /></a>');
            }
            
        echo("</div>");  
    }
}

/**
 * Determine if lead art needs to be shown for current post
 *
 * Determination is based on presence of:
 * 1. lead_video
 * 2. lead_youtube
 * 3. lead_vimeo
 * 4. lead_embed
 * 5. lead_soundslides
 * 6. photo in post photo gallery
 *
 * @return boolean
 */
function calpress_showleadart(){    
    if ( 
        get_post_custom_values('lead_video') ||
        get_post_custom_values('lead_youtube') ||
        get_post_custom_values('lead_vimeo') ||
        get_post_custom_values('lead_embed') ||
        get_post_custom_values('lead_soundslides')
        ) 
    {
        return true;
 	} elseif (calpress_postimage()!=false ){
 	    return true;
 	} else{
 	    return false;
 	}
}

/**
 * Determine if lead art is an embed (lead_embed)
 *
 * @return boolean
 */
function calpress_leadartembed(){
    if ( get_post_custom_values('lead_embed')) {
        return true;
    } else {
        return false;
    }
}

/**
 * Determine if tease art can be shown for current post
 *
 * Determination is based on whether calpress_postimage()  
 * finds a suitable image.
 *
 * @return boolean
 */
function calpress_showteaseart(){
    if (calpress_postimage()!=false ){
 	    return true;
 	} else {
 	    return false;
 	}
}

/**
 * Display the lead art block (video, slideshow, etc), regardless of media type.
 *
 * Includes cutline paragraph needed
 *
 * @param int $w = width of spot
 * @param string $ratio = horizontal size ratio "169" or "43"
 * @return boolean
 */
function calpress_leadart($w = LEADARTSIZE, $ratio = "169"){
    global $post;
    $thePostID = $post->ID; // get_post_custom_values needs to use this id, b/c sometimes
                            // the value of $post and $wp_query->post->ID are not the same b/c 
                            // a page will temporarily reset $post, but get_post_custom_values
                            // seems to use $wp_query->post->ID and not the current $post global.
    
    $h = calpress_mediaheight($w, $ratio);
    
    if ( get_post_custom_values('lead_video', $thePostID) ) {//show the lead art video
        $h = calpress_mediaheight($w, "169", "video");
        $videos = get_post_custom_values('lead_video', $thePostID);
        
        $options = calpress_parsextrafieldsoptions($videos[0]);
        $slug = $options[0];
        $title = $options[1];
        $cutline = $options[2];
        
        echo("<div class=\"lead-art entry-leadvideo\">");   
            calpress_embedvideo($slug,$cutline,"",$w,$h);
        echo("</div>");
        
    } elseif ( get_post_custom_values('lead_youtube', $thePostID) ) {//show the lead art slideshow
        $h = calpress_mediaheight($w, "169", "video");
        $videos = get_post_custom_values('lead_youtube', $thePostID);
        // get custom field options
        // options are in this format:
        //  youtube ID    title   cutline     
        $options = calpress_parsextrafieldsoptions($videos[0]);
        $youtubeid = $options[0];
        $title = "";
        $cutline = $options[2];
        echo("<div class=\"lead-art entry-leadyoutube\">");
            calpress_embedyoutube($youtubeid,$cutline,$title,$w,$h);
        echo("</div>");
    } elseif ( get_post_custom_values('lead_vimeo', $thePostID) ) {//show the lead art slideshow
        $h = calpress_mediaheight($w, "169", "video");
        $videos = get_post_custom_values('lead_vimeo', $thePostID);   
        $options = calpress_parsextrafieldsoptions($videos[0]);
        $vimeoid = $options[0];
        $title = "";
        $cutline = $options[2];
        echo("<div class=\"lead-art entry-leadvimeo\">");
            calpress_embedvimeo($vimeoid,$cutline,$title,$w,$h);
        echo("</div>");
    } elseif ( get_post_custom_values('lead_soundslides', $thePostID) ) {//show the lead art slideshow
        echo("<div class=\"lead-art entry-leadsoundslides\">");
            $h = calpress_mediaheight($w, "43");//always used 4x3
            $h = $h + 34;//add control bar height
            $soundslides = get_post_custom_values('lead_soundslides', $thePostID);
            $options = calpress_parsextrafieldsoptions($soundslides[0]);
            $slug = $options[0];
            
            $path = mysql2date('Y/m/', $post->post_date);
            $path = $path.$slug;
            $title = "";
            $cutline = $options[2];
            calpress_embedsoundslides($path,$cutline,$title,$w,$h);
        echo("</div>");
    } elseif ( get_post_custom_values('lead_embed', $thePostID) ) {//show the lead art embed
       echo("<div class=\"lead-art entry-leadembed\">");
            $embeds = get_post_custom_values('lead_embed', $thePostID);
            $options = calpress_parsextrafieldsoptions($embeds[0]);
            $embedcode = $options[0];
            $title = $options[1];
            $caption = $options[2];
            echo("<div class=\"lead-art entry-leadembed\">"); 
	        echo($embedcode);
	        echo("<div class=\"embed caption\"><p class=\"caption\">$caption</p></div>");
	        echo("</div>");   
       echo("</div>");
   } elseif ( get_post_custom_values('Video', $thePostID) ) {//legacy FLV video on Oakland North
            $h = calpress_mediaheight($w, "169", "video");
            $videos = get_post_custom_values('Video', $thePostID);
            //FLV path
            $options = calpress_parsextrafieldsoptions($videos[0]);
            $flvpath = $options[0];
            $cutline = "";
            $title = "";
            echo("<div class=\"lead-art entry-legacyvideo\">");
            calpress_legacyvideo($flvpath,$cutline,$title,$w,$h);
            echo("</div>");
    } elseif(calpress_postimage()!=false) { //show the lead art photo
        $srcimg = calpress_leadimagepath();//uploaded photo
        
        $imgsrc = calpress_sizedimage($srcimg, $w);
        echo("<div class=\"lead-art entry-leadphoto\">");
            if (is_single()) {
                echo ($imgsrc);//on article. no need to make image a link
            }else{
                $link = get_permalink();
                echo ("<a href=\"$link\">".$imgsrc."</a>");
            }
        echo("</div>");  
        if (calpress_leadimagecaption()!=false) {
            $caption = calpress_leadimagecaption();
            echo('<p class="photo caption">'.$caption.'</p>');
        }
    } else {
        return false;
    }
}

/**
 * Returns an img element with image url for a resized version of any image on the server
 *
 * Resize will only go from larger image to smaller image. The suggested upload 
 * size of all images is around 1600px on the longest side for maximum flexibility.
 *
 * @param string $srcimg = the location of the original, unsized image on the server
 * @param int $w = the output size of the image
 * @param string $alt = image alt text
 * @return string
 */
function calpress_sizedimage($srcimg, $w, $alt=""){
    $imgsrc = calpress_sizedimageurl($srcimg, $w);
    $formattedURL = "<img alt=\"$alt\" src =\"$imgsrc\" />";
    return $formattedURL;
}

/**
 * Returns an img element with image url for a resized and cropped version of any image on the server
 *
 * Resize will only go from larger image to smaller image. The suggested upload 
 * size of all images is around 1600px on the longest side for maximum flexibility.
 *
 * @param string $srcimg = the location of the original, unsized image on the server
 * @param int $w = the output size of the image
 * @param int $h = the output height image
 * @param int $z = zoom crop 
 * @param string $alt = image alt text
 * @return string
 */
function calpress_croppedimage($srcimg, $w, $h, $z=1,$alt=""){
    $imgsrc = calpress_croppedimageurl($srcimg, $w, $h, $z);
    $formattedURL = "<img alt=\"$alt\" src =\"$imgsrc\" />";
    return $formattedURL;
}

/**
 * Returns an image path to dynamically resized image
 *
 * @param string $srcimg = the location of the original, unsized image on the server
 * @param int $w = the output size of the image
 * @return string
 */
function calpress_sizedimageurl($srcimg, $w){
    $imgsrc = CALPRESSURI.'/library/extensions/timthumb.php?src='.$srcimg.'&amp;w='.$w;//resizer script
    return $imgsrc;
}

/**
 * Returns an image path to dynamically resized image
 *
 * @param string $srcimg = the location of the original, unsized image on the server
 * @param int $w = the output width of the image
 * @param int $h = the output height of the image
 * @param int $z = crop zoom
 * @return string
 */
function calpress_croppedimageurl($srcimg, $w, $h, $z=1){
    $imgsrc = CALPRESSURI.'/library/extensions/timthumb.php?src='.$srcimg.'&amp;w='.$w.'&amp;h='.$h.'&amp;z='.$z;//resizer script
    return $imgsrc;
}

/**
 * Return a list of images from a post wrapped in an HTML Anchor tag
 *
 * Image size is either a string keyword (thumbnail, medium, large or full) or
 * a 2-item array representing width and height in pixels, e.g. array(32,32).
 * Function based on this: http://wordpress.org/support/topic/197746
 *
 * @param string/array $size
 * @param int $num = depreciated
 * @param boolean $caption = whether or not to show the caption
 * @return string
 */
function calpress_postimage($size='large',$num=1, $caption=true) {
    global $post;
    $thePostID = $post->ID;
    
	if ( $images = get_children(array(
		'post_parent' => $thePostID,
		'post_type' => 'attachment',
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'post_mime_type' => 'image',)))
	{
	    $image_key = array_keys($images);
		$image = $images[$image_key[0]];
		$attachmenturl=wp_get_attachment_url($image->ID);
		$attachmentimage=wp_get_attachment_image($image->ID, $size );
		$img_title = $image->post_title;
		$img_desc = $image->post_excerpt;
		if($caption){
		    return $attachmentimage.'<p class="photo caption entry-caption">'.$img_desc.'</p>';
		}else{
		    return $attachmentimage;
		}

	} else {
		return false;
	}
}

/**
 * Return the path to the large-sized lead image of a post.
 *
 * May be combined with library/extensions/timthumb.php for dynamic resizing.
 * See also calpress_postimage()
 *
 * @param string/array $size
 * @param int $num = depreciated
 * @param boolean $caption = whether or not to show the caption
 * @return string
 */
function calpress_leadimagepath(){
    global $post;
    $thePostID = $post->ID;
    
    if ( $images = get_children(array(
	    'post_parent' => $thePostID,
		'post_type' => 'attachment',
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'post_mime_type' => 'image',)))
	{
		$image_key = array_keys($images);
		$image = $images[$image_key[0]];
		$attachmenturl=wp_get_attachment_url($image->ID);
		return $attachmenturl;
		
	} else {
		return false;
	}
}

/**
 * Return the caption of lead image
 *
 * @return string
 */
function calpress_leadimagecaption(){
    global $post;
    $thePostID = $post->ID;
    
    if ( $images = get_children(array(
		'post_parent' => $thePostID,
		'post_type' => 'attachment',
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'post_mime_type' => 'image',)))
	{
		$image_key = array_keys($images);
		$image = $images[$image_key[0]];
		$img_desc = $image->post_excerpt;
		return $img_desc;
	} else {
		return false;
	}
}

/**
 * Return a series of LIs of posts ranked by the most commented with X number of days
 *
 * Based on: http://bavotasan.com/tutorials/how-to-list-your-most-popular-posts-in-wordpress/
 *
 * @param int $num = number of results. default = 10
 * @param int $days = number of days to check. default = 20
 * @return string
 */
function calpress_popularposts($num=10, $days=20) {  
    global $wpdb;  
      
    $posts = $wpdb->get_results("SELECT comment_count, ID, post_title FROM $wpdb->posts WHERE post_type='post' AND post_status = 'publish' AND DATE_SUB(CURDATE(), INTERVAL $days DAY) <= post_date_gmt ORDER BY comment_count DESC LIMIT 0 , $num");    
    foreach ($posts as $post) {  
        setup_postdata($post);  
        $id = $post->ID;  
        $title = $post->post_title;  
        $count = $post->comment_count;  
        
        if ($count != 0) {  
            $popular .= '<li>';  
            $popular .= '<a href="' . get_permalink($id) . '" title="' . $title . '">' . $title . '</a> ';  
            $popular .= '</li>';  
        }  
    }  
    return $popular;  
}

/**
 * Return an array of stories weighted by timeliness and number of comments
 *
 * @param int $number_of_posts = number of results. default = 10
 * @param boolean $images_required = only return posts with images. default = true
 * @param int/array int or an array of ints with IDs that must be excluded from search 
 * @return string
 */
function calpress_get_weighted_story_list($number_of_posts = 10, $images_required = true, $exludeposts = array()) {
    global $wpdb;  
    
    // build query
    
    // find only posts with images?
   if ($images_required) {
       $query = "SELECT wp_posts.ID, wp_posts.post_title AS post_title, wp_posts.post_date, ( To_days(now()) - To_days(wp_posts.post_date) ) AS post_age, ( 8 - ( To_days(now()) - To_days(wp_posts.post_date) ) ) AS post_weight, wp_posts.comment_count, (1 + wp_posts.comment_count ) * ( 8 - ( To_days(now()) - To_days(wp_posts.post_date) ) ) AS post_relevance, attachments.post_title AS attachment_title, attachments.guid as attachment_file from $wpdb->posts LEFT JOIN $wpdb->posts AS attachments on wp_posts.id = attachments.post_parent WHERE wp_posts.post_status LIKE 'publish' AND wp_posts.post_type LIKE 'post' AND attachments.post_type LIKE 'attachment'";  
   } else {
        $query = "SELECT wp_posts.ID, wp_posts.post_title AS post_title, wp_posts.post_date, ( To_days(now()) - To_days(wp_posts.post_date) ) AS post_age, ( 8 - ( To_days(now()) - To_days(wp_posts.post_date) ) ) AS post_weight, wp_posts.comment_count, (1 + wp_posts.comment_count ) * ( 8 - ( To_days(now()) - To_days(wp_posts.post_date) ) ) AS post_relevance from $wpdb->posts WHERE wp_posts.post_status LIKE 'publish' AND wp_posts.post_type LIKE 'post'";  
   }
    
    
    // exclude certain posts
    if (!empty($exludeposts)) {
        if (is_array($exludeposts)) {
            $excludes = " AND wp_posts.ID != ";
            $excludes .= implode(" AND wp_posts.ID != ", $exludeposts);
        }
        elseif (is_int($exludeposts)) {
            $excludes = " AND wp_posts.ID != " . $exludeposts;
        }
    }

    $query .= $excludes;
    
    // group and sort
    $query .= " GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
    
    // limit number of results
    $query .= " LIMIT $number_of_posts;";
    
    //exectute query
    return $wpdb->get_results($query);
}


/**
 * Return a string from a post or page formatted to be a list categories for a WP Query object.
 *
 * see: http://codex.wordpress.org/Template_Tags/query_posts
 * eg: cat=4 , cat=4,3 cat=-3
 *
 * @return string
 */
function calpress_customcategories(){
    $categories = "";
    if ( get_post_custom_values('custom_category') ) {
        $cats = get_post_custom_values('custom_category');
        $categories = $cats[0];
    }
    return $categories;
}

/**
 * Determine if there are sidebar/inline elements to show in a story post
 *
 * If any inline_* exists in extra fields for a story, return true
 *
 * @return boolean
 */
function calpress_showinlines(){
    if ( 
        get_post_custom_values('inline_video') || 
        get_post_custom_values('inline_embed') ||
        get_post_custom_values('inline_soundslides') || 
        get_post_custom_values('inline_youtube') || 
        get_post_custom_values('inline_vimeo') || 
        get_post_custom_values('inline_embed') || 
        get_post_custom_values('related_links') || 
        get_post_custom_values('inline_story') ||
        get_post_custom_values('inline_audio') ||
        ( get_post_custom_values('inline_poll') && function_exists('vote_poll') ) ||
        get_post_custom_values('inline_map')) {
	    return true;
	} else {
	    return false;
	}
}

/**
 * Display the inline story elements in the correct order
 *
 * For HTML for the inline, see calpress_get_inlines()
 *
 * @return void
 */
function calpress_inlines($content = ''){
	if (is_single() && calpress_showinlines()) {
	    //build inlines from extra fields in post
	    $inlines = calpress_get_inlines();
    	$inlines = str_replace("\r\n","",$inlines);
	    
	    //container div
		$inlinehtml = "<div id=\"entry-sidebar\">\n";
		
		    //above inline hook
    		ob_start();
    		calpress_template_inline_above();
    		$above_inline = ob_get_contents();
            ob_end_clean();
        
            if ($above_inline != "") {
                $inlinehtml .= "<div class=\"sidebar-element inline-above\">\n";
                $inlinehtml .= $above_inline . "\n";
                $inlinehtml .= "\n</div><!-- //end .inline-above-->\n";
            }
        
    		//main inline content
    		$inlinehtml .= $inlines;
		
    		//below inline hook
    		ob_start();
    		calpress_template_inline_below();
    		$below_inline = ob_get_contents();
            ob_end_clean();
		    
		    if ($below_inline != "") {
                $inlinehtml .= "<div class=\"sidebar-element inline-below\">\n";
                $inlinehtml .= $below_inline . "\n";
                $inlinehtml .= "\n</div><!-- //end .inline-below-->\n";
            }
		
		$inlinehtml .= "\n</div><!-- //end #entry-sidebar-->\n";
		
		//combine content and inlines
		$content = $inlinehtml . $content; 
	}
	return $content;
}
add_filter("the_content", "calpress_inlines", 10);

/**
 * HTML for the inline story elements in the correct order
 *
 * Inlines are automatically displayed via an add_filter on 
 * the content in calpress_inlines().
 *
 * @return string
 */
function calpress_get_inlines(){
    global $post;
	$html = "";
    
    // make sure we having something to print
    if ( calpress_showinlines() ) {
        
        // determine if there is a correct order. If not print in default order
        if ( get_post_custom_values('inline_order') ) {
            
        } else {
            // map
             if ( get_post_custom_values('inline_map') ) {
                $maps = get_post_custom_values('inline_map');
	            $w = calpress_sidebarmediawidth();
	            $h = calpress_mediaheight($w, "43", "map");
	            for ($i = 0; $i < sizeof($maps); $i++) {
	                
	                // get custom field options
	                // options are in this format:
	                //  lat, long                zoom    map type    title   cutline      map point              point info       map point              point info            
	                //  38.938832, -77.047119   |8      |ROADMAP    |tile   |cutline    |34.875356,-124.260817  |Bob's House    |34.875356,-124.260817  |Bob's House
	                $options = calpress_parsextrafieldsoptions($maps[$i]);
	                
	                // center point
	                $latlng = calpress_parselatlong($options[0]);
	                $centerpoint_lat = $latlng[0];
	                $centerpoint_lng = $latlng[1];
	                
	                // zoom - check for valid or set default of 15
	                $zoom = ( calpress_extrafieldtest("^[1-9]\${1,2}", $options[1]) ) ? $zoom = $options[1] : $zoom = 15;
	                
                    // map type - Valid are ROADMAP, SATELLITE, HYBRID, TERRAIN - default is roadmap
                    $type = ( calpress_extrafieldtest("^(ROADMAP|SATELLITE|HYBRID|TERRAIN)\$", $options[2]) ) ? $type = $options[2] : $type = "ROADMAP";
                    
                    // map title.
                    $title = $options[3];
                    
                    // cutline
                    $cutline = $options[4];
                    
                    // get list of map points
                    $points = calpress_mappoints($options);
                    
	                $html .= "<div class=\"sidebar-element entry-sidebar-map\">"; 
	                $html .=  calpress_get_embedmap($centerpoint_lat, $centerpoint_lng, $zoom, $type, $w, $h, $title, $cutline, $points);
	                $html .= "</div><!--//end .sidebar-element -->"; 
	            }
        	}
            
            // video
            if ( get_post_custom_values('inline_video') ) {
                $videos = get_post_custom_values('inline_video');
	            $w = calpress_sidebarmediawidth();
                $h = calpress_mediaheight($w, "169", "video");
	            for ($i = 0; $i < sizeof($videos); $i++) {
	                
	                // get custom field options
	                // options are in this format:
	                //  video slug    title   cutline     
	                $options = calpress_parsextrafieldsoptions($videos[$i]);
	                $slug = $options[0];
	                $title = $options[1];
	                $cutline = $options[2];
	                
	                $html .= "<div class=\"sidebar-element entry-sidebar-video\">";   
	                $html .= calpress_get_embedvideo($slug,$cutline,$title,$w,$h);
	                $html .= "</div><!--//end .sidebar-element -->"; 
	            }
            }
            
            // soundsslides
            if ( get_post_custom_values('inline_soundslides') ) {
                $soundslides = get_post_custom_values('inline_soundslides');
	            $w = calpress_sidebarmediawidth();
                $h = calpress_mediaheight($w, "43");//always used 4x3
                $h = $h + 34;//add control bar height
	            for ($i = 0; $i < sizeof($soundslides); $i++) {
	                   
	                $options = calpress_parsextrafieldsoptions($soundslides[$i]);
                    $slug = $options[0];
                    $path = mysql2date('Y/m/', $post->post_date);
                    $path = $path.$slug;
                    $title = $options[1];
                    $cutline = $options[2];
	                $html .= "<div class=\"sidebar-element entry-sidebar-vimeo\">"; 
	                $html .= calpress_get_embedsoundslides($path,$cutline,$title,$w,$h);
	                $html .= "</div><!--//end .sidebar-element -->"; 
	            }
    	    }
            
            // inline embed code
            if ( get_post_custom_values('inline_embed') ) {
                $embeds = get_post_custom_values('inline_embed');
                for ($i = 0; $i < sizeof($embeds); $i++) {
                    $options = calpress_parsextrafieldsoptions($embeds[$i]);
                    $embedcode = $options[0];
                    $title = $options[1];
                    $html .= "<div class=\"sidebar-element entry-sidebar-embed\">"; 
                    $html .= "<div class=\"embed title\"><h3>$title</h3></div>";
	                $html .= $embedcode;
	                $html .= "</div><!--//end .sidebar-element -->";
                }
            }
            
            // audio
            if ( get_post_custom_values('inline_audio') ) {
                $audio = get_post_custom_values('inline_audio');
	            $w = calpress_sidebarmediawidth();
                $h = 50;
	            for ($i = 0; $i < sizeof($audio); $i++) {
	                
	                // get custom field options
	                // options are in this format:
	                //  audio slug    title   cutline     
	                $options = calpress_parsextrafieldsoptions($audio[$i]);
	                $slug = $options[0];
                    $path = mysql2date('Y/m/', $post->post_date);
                    $path = $path.$slug;
	                $title = $options[1];
	                $cutline = $options[2];
	                
	                $html .= "<div class=\"sidebar-element entry-sidebar-audio\">";   
	                $html .= calpress_get_embedaudio($path,$cutline,$title,$w,$h);
	                $html .= "</div><!--//end .sidebar-element -->"; 
	            }
            }
            
            // inline poll 
            // --   only if WP-Polls Plugin is installed
            //      http://lesterchan.net/wordpress/readme/wp-polls.html
            if ( get_post_custom_values('inline_poll') && function_exists('vote_poll') ) {
                $polls = get_post_custom_values('inline_poll');
                for ($i = 0; $i < sizeof($polls); $i++) {
                    $options = calpress_parsextrafieldsoptions($polls[$i]);
                    $pollid = $options[0];
                    $title = $options[1];
                    $cutline = $options[2];
                    $html .= "<div class=\"sidebar-element entry-sidebar-poll\">"; 
                    $html .= "<div class=\"poll title\"><h3>$title</h3></div>";
	                $html .= get_poll($pollid, false);
	                if($cutline!=""){
                        $html .= "<p class=\"caption\">$cutline</p>";
                    }
	                $html .= "</div><!--//end .sidebar-element -->";
                }
            }
            
            // inline story
            if ( get_post_custom_values('inline_story') ) {
                $stories = get_post_custom_values('inline_story');
                for ($i = 0; $i < sizeof($stories); $i++) {
                    $options = calpress_parsextrafieldsoptions($stories[$i]);
                    $storytxt = $options[0];
                    $title = $options[1];
                    $html .= "<div class=\"sidebar-element entry-sidebar-story\">"; 
                    $html .= "<div class=\"story title\"><h3>$title</h3></div>";
	                $html .= $storytxt;
	                $html .= "</div><!--//end .sidebar-element -->";
                }
            }
            
            // youtube
            if ( get_post_custom_values('inline_youtube') ) {
                $videos = get_post_custom_values('inline_youtube');
	            $w = calpress_sidebarmediawidth();
                $h = calpress_mediaheight($w, "169", "video");
	            for ($i = 0; $i < sizeof($videos); $i++) {
	                
	                // get custom field options
	                // options are in this format:
	                //  youtube ID    title   cutline     
	                $options = calpress_parsextrafieldsoptions($videos[$i]);
	                $youtubeid = $options[0];
	                $title = $options[1];
	                $cutline = $options[2];
	                
	                $html .= "<div class=\"sidebar-element entry-sidebar-youtube\">"; 
	                $html .= calpress_get_embedyoutube($youtubeid,$cutline,$title,$w,$h);
	                $html .= "</div><!--//end .sidebar-element -->"; 
	            }
    	    }
    	    
    	    // vimeo
            if ( get_post_custom_values('inline_vimeo') ) {
                $videos = get_post_custom_values('inline_vimeo');
	            $w = calpress_sidebarmediawidth();
                $h = calpress_mediaheight($w, "169", "video");
	            for ($i = 0; $i < sizeof($videos); $i++) {
	                
	                // get custom field options
	                // options are in this format:
	                //  youtube ID    title   cutline     
	                $options = calpress_parsextrafieldsoptions($videos[$i]);
	                $vimeoid = $options[0];
	                $title = $options[1];
	                $cutline = $options[2];
	                
	                $html .= "<div class=\"sidebar-element entry-sidebar-vimeo\">"; 
	                $html .= calpress_get_embedvimeo($vimeoid,$cutline,$title,$w,$h);
	                $html .= "</div><!--//end .sidebar-element -->"; 
	            }
    	    }
    	    
    	    // related links
            if ( get_post_custom_values('related_links') ) {
                $links = get_post_custom_values('related_links');
	            for ($i = 0; $i < sizeof($links); $i++) {
	                $options = calpress_parsextrafieldsoptions($links[$i]);
	                $catid = $options[0];
	                $title = $options[1];
	                if($title==""){
	                    $title="Related";
                    }
                    $linkquery = "echo=0&title_li=&categorize=0&category=".$catid."&orderby=rating";
	                $html .="<div class=\"sidebar-element entry-sidebar-links\">";
	                $html .= "<div class=\"links title\"><h3>$title</h3></div>";
	                $html .="<ul>";
	                $html .= wp_list_bookmarks($linkquery);
	                $html .= "</ul>";
	                $html .="</div><!--//end .sidebar-element $linkquery -->"; 
	            }
    	    }
    	    
        }
    }
	return $html;
}

/**
 * Return URL of first mp3 in a post
 *
 * @return string
 */
function calpress_getmp3(){
	if (get_post_custom_values('enclosure')) {
		$enclosure = get_post_custom_values('enclosure');
		// Relevant portion of the array is the URL, or 0 indecies of array
		// [0] => http://media.journalism.berkeley.edu/oaknorth/2010/03/20100317_podcast/on_podcast1.mp3 [1] => 4226015 [2] => audio/mpeg [3] => a:1:{s:8:"duration";s:7:"0:05:01";} ) 
		$mp3_meta = explode("\n", $enclosure[0]);
		$mp3 = trim($mp3_meta[0]);
		return $mp3;
	}
}

/**
 * Change the default contact methods for site authors
 *
 * Add twitter, phone and title. Drop aim, yim and jabber.
 * Based on: http://sillybean.net/wordpress/users-and-roles/creating-a-user-directory-part-1-changing-user-contact-fields/ 
 *
 * @return string
 */
function change_contactmethod( $contactmethods ) {
  // Add some fields
  $contactmethods['twitter'] = 'Twitter Name (no @)';
  $contactmethods['phone'] = 'Phone Number';
  $contactmethods['title'] = 'Title';
  $contactmethods['org'] = 'Organization';
  $contactmethods['address_1'] = 'Address Line 1';
  $contactmethods['address_2'] = 'Address Line 2';
  $contactmethods['address_city'] = 'City';
  $contactmethods['address_state'] = 'State';
  $contactmethods['address_zip'] = 'Zip Code';
  $contactmethods['short_bio'] = 'Short biography';
  
  // Remove AIM, Yahoo IM, Google Talk/Jabber
  unset($contactmethods['aim']);
  unset($contactmethods['yim']);
  unset($contactmethods['jabber']);
  // make it go!
  return $contactmethods;
}
add_filter('user_contactmethods','change_contactmethod',10,1);

/**
 * Display link for spanish version of page
 *
 * @return void
 */
function calpress_spanishlink(){
     if ( get_post_custom_values('in_spanish') ){
         $url = get_post_custom_values('in_spanish');
        echo("<div class=\"alternate-url spanish\">");
            echo ("<a href=\"$url[0]\">En Espa&#241;ol</a>");
        echo("</div>");
    }
}

//put a small bug next to headline if there is a spanish version
function calpress_spanishbug($image_link = null){
	if ( get_post_custom_values('in_spanish') && $image_link){
		$url = get_post_custom_values('in_spanish');
		echo("&nbsp;<span class=\"spanish-bug\"><a href=\"$url[0]\"><img src=\"$image_link\" alt=\"Spanish icon\" /></a></span>");
	}
}

// spanish version of an asset?
function calpress_inspanish(){
    if ( get_post_custom_values('in_spanish') ){
        return true;
    }else{
        return false;
    }
}

/**
 * Much like calpress_trim_except, except can trim any text to a length
 *
 * @param $length - int - number of words to trim to
 * @return string
 */ 
function calpress_trim_text($text, $length = 55) {
    
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
	$words = preg_split("/[\n\r\t ]+/", $text, $length + 1, PREG_SPLIT_NO_EMPTY);
	if ( count($words) > $length ) {
		array_pop($words);
		$text = implode(' ', $words);
		$text = $text . $excerpt_more;
	} else {
		$text = implode(' ', $words);
	}
	
	return $text;
}

/**
 * A custom version of WP's the_excerpt. This one
 * allows for a truncation length to be set. 
 *
 * @param $length - int - number of words to use in the excerpt
 * @return string
 */ 
function calpress_trim_excerpt($length = 55) {
    global $post;
    setup_postdata($post);
    
	//$text = $post->post_content;
	
	$text = get_the_excerpt();
	$text = strip_shortcodes( $text );
	$text = apply_filters('the_content', $text);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	$excerpt_length = apply_filters('excerpt_length', $length);
	$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
	$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	if ( count($words) > $excerpt_length ) {
		array_pop($words);
		$text = implode(' ', $words);
		$text = $text . $excerpt_more;
	} else {
		$text = implode(' ', $words);
	}
	echo '<p>'.$text.'</p>';
}

function new_excerpt_more($more) {
       //global $post;
	   //return '<a href="'. get_permalink($post->ID) . '">' . '...' . '</a>';
	   return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');



// Check for static widgets in widget-ready areas
// http://themeshaper.com/wordpress-theme-sidebar-template/
function calpress_is_sidebar_active( $index ){
    global $wp_registered_sidebars;
    $widgetcolums = wp_get_sidebars_widgets();
    if ($widgetcolums[$index]) return true;
    
    return false;
}

// drop in relpacement for sandbox_commenter_link;
function calpress_commenter_link(){
    global $post;
    $commenter = get_comment_author_link();
	if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
		$commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
	} else {
		$commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
	}
	$avatar_email = get_comment_author_email();
	$avatar_size = apply_filters( 'avatar_size', '65' ); // Available filter: avatar_size
	$avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, $avatar_size ) );
	echo $avatar . ' <span class="fn n">' . $commenter . '</span>';
}

// drop in replacement for sandbox_gallery
// using custom calpress image sizes, and striping HTML from captions before inclusion into gallery. 
function calpress_gallery($attr) {
	global $post;
	if ( isset($attr['orderby']) ) {
		$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		if ( !$attr['orderby'] )
			unset($attr['orderby']);
	}

	extract(shortcode_atts( array(
		'orderby'    => 'menu_order ASC, ID ASC',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'large',
	), $attr ));

	$id           =  intval($id);
	$orderby      =  addslashes($orderby);
	$attachments  =  get_children("post_parent=$id&post_type=attachment&post_mime_type=image&orderby={$orderby}");

	if ( empty($attachments) )
		return null;

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, true ) . "\n";
		return $output;
	}

	$listtag     =  tag_escape($listtag);
	$itemtag     =  tag_escape($itemtag);
	$captiontag  =  tag_escape($captiontag);
	$columns     =  intval($columns);
	$itemwidth   =  $columns > 0 ? floor(100/$columns) : 100;

	$output = apply_filters( 'gallery_style', "\n" . '<div class="gallery">', 9 ); // Available filter: gallery_style

	foreach ( $attachments as $id => $attachment ) {
		$img_lnk = get_attachment_link($id);
		$img_src = wp_get_attachment_image_src( $id, $size );
		$img_src = $img_src[0];
		$img_src = calpress_sizedimageurl($img_src, 200);
		$img_alt = strip_tags($attachment->post_excerpt);
		if ( $img_alt == null )
			$img_alt = $attachment->post_title;
		$img_rel = apply_filters( 'gallery_img_rel', 'attachment' ); // Available filter: gallery_img_rel
		$img_class = apply_filters( 'gallery_img_class', 'gallery-image' ); // Available filter: gallery_img_class

		$output  .=  "\n\t" . '<' . $itemtag . ' class="gallery-item gallery-columns-' . $columns .'">';
		$output  .=  "\n\t\t" . '<' . $icontag . ' class="gallery-icon"><a href="' . $img_lnk . '" title="' . $img_alt . '" rel="' . $img_rel . '"><img src="' . $img_src . '" alt="' . $img_alt . '" class="' . $img_class . ' attachment-' . $size . '" /></a></' . $icontag . '>';

		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "\n\t\t" . '<' . $captiontag . ' class="gallery-caption">' . $attachment->post_excerpt . '</' . $captiontag . '>';
		}

		$output .= "\n\t" . '</' . $itemtag . '>';
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= "\n</div>\n" . '<div class="gallery">';
	}
	$output .= "\n</div>\n";

	return $output;
}
// remove old
remove_filter( 'post_gallery', 'sandbox_gallery', $attr );
// add new
add_filter( 'post_gallery', 'calpress_gallery', $attr );

/**
 * A custom comment counter for Posts. Excludes track/ping backs
 *
 * Based on work by Ian Stewart:
 * http://themeshaper.com/wordpress-theme-comments-template-tutorial/
 * @return string
 */
function calpress_get_comment_count(){
    global $wp_query;
    
    $comments = $wp_query->comments;
    $comment_count = 0;
    foreach ( $comments as $comment ) {
        if($comment->comment_type != "pingback" && $comment->comment_type != "trackback"){
            $comment_count++;
        }
    }
    
    return $comment_count;
}

/**
 * A function to change the behavior of WP's get_comment_count to exclude trackback and pingbacks
 *
 * @uses get_comments_number()
 * Based on :
 * http://sivel.net/2008/10/wp-27-comment-separation/
 * @return string
 */
function calpress_update_comment_count(){
    add_filter('get_comments_number', 'calpress_get_comment_count', 0);
}

/**
 * A custom callback for wp_list_comments, so we can 
 * customize commenter links/stats. see comments.php
 *
 * @return string
 */
function calpress_custom_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; 
   // get correct avatar type from Settings->Discussion
   $avatar_default = get_option('avatar_default');
   if ( empty($avatar_default) ) {
       $default = 'mystery';
   } else {
       $default = $avatar_default;
   }
   
   // get user-defined depth of contents from Settings->Discussion
   $comment_depth = get_option('thread_comments_depth');
   $threaded_comments_enabled = get_option('thread_comments');
   
   // We want commentor URLs to point to their WP profile, if possible
   // - Is the comment author registered on the site? the author of the post?
    $registered_user = false;
    $post_author = false;
    $staff_memeber = false;
   if ( $comment->user_id > 0 && $user = get_userdata($comment->user_id) ) {
       // get user info
       $registered_user = true;
       $user_info = get_userdata($comment->user_id);
       $post_author_link = get_author_posts_url( $comment->user_id ); 
       $post_author_name = $user_info->display_name;
       $post_author_registered_date = calpress_relativetime(strtotime($user_info->user_registered));
       $commentauthorlink = "<a href=\"$post_author_link\">$post_author_name</a>";
       
       // is this registered user also the post author?
       if ( $post = get_post($post_id) ) {
			if ( $comment->user_id === $post->post_author )
				$post_author = true;
		}
		
		// is the registered user a staff member (editor level or above)?
		if ($user_info->user_level >= 7) {
		  $staff_memeber = true;
		}
       
   } else {
       $commentauthorlink = get_comment_author_link();
   }
   
   ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
      <div class="comment-author vcard">
         <?php echo get_avatar($comment,$size='32',$default); ?>
         <?php 
         if ($post_author){
             printf(__('<cite class="fn">%s</cite> <span class="member-status">Post author</span>'), $commentauthorlink);
         } elseif ($staff_memeber) {
             printf(__('<cite class="fn">%s</cite> <span class="member-status">Staff</span>'), $commentauthorlink);
         } elseif ($registered_user) {
             printf(__('<cite class="fn">%s</cite> <span class="member-status">Joined %s</span>'), $commentauthorlink, $post_author_registered_date);
         } else {
             printf(__('<cite class="fn">%s</cite>'), $commentauthorlink);  
         }
         ?>
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>

      <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>

      <?php comment_text() ?>

      <?php if ($comment_depth > $depth && $threaded_comments_enabled && comments_open()): ?>
          <div class="reply">
               <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            </div>
      <?php endif ?>
     </div>
<?php
}

/**
 * Returns object of approved comments posted by a user
 *
 * @param int $authorID
 * 
 * @return obj
 */
function calpress_author_comments_by_id($authorID){
    global $wpdb;
    $sql  = 'SELECT wp_comments.*, wp_posts.post_title FROM ' . $wpdb->comments . ',' . $wpdb->posts . ' WHERE user_id = '. $authorID . ' AND comment_approved = \'1\' AND wp_comments.comment_post_ID = wp_posts.ID';
    return $wpdb->get_results($sql);
}

/**
 * Displays comments posted by a user by name and email.
 *
 * Wrapper around ppm_author_comments. Flexible with lots of options. See function doc.
 * library/extensions/get-author-comments
 *
 * @uses apply_filters() Calls 'ppm_get_author_comments' on author's comments before displaying
 * 
 * @param string $author_name The author's name
 * @param string|array $author_email The author's e-mail
 * @param int    $postID An optional post ID
 * @param array  $args Formatting options ({@link ppm_get_author_comments()} and {@link wp_list_comments()})
 * 
 * @return void
 */
function calpress_author_comments($author_name, $author_email, $postID = null, $args = array()){
    require_once('get-author-comments.php');
    ppm_author_comments($author_name, $author_email, $postID, $args);
}

// http://www.nerdydork.com/simple-php-pluralize.html
function pluralize($num, $plural = 's', $single = '') {
    if ($num == 1) return $single; else return $plural;
}

/**
 * Returns object of featured comments, sorted by most recently commented.
 *
 * This function assumes use of the featured-comments.php file, which updates wp_commentsmeta table
 *
 * @param int $limit - number of comments to return
 * @param int $category - for any number > 0, return only featured comments from posts in that category
 * 
 * @return obj
 */
function calpress_featured_comments($limit = 1, $category = 0){
    global $wpdb;
    if ($category > 0) {
        /*
            based on:
            http://wordpress.org/support/topic/get-last-comments-per-category
            
            TODO: convert all into $wpdb
        */
        $sql = "SELECT DISTINCT ID, post_title, post_password, c.comment_ID, 
        comment_post_ID, comment_author, comment_date, cm.meta_key, comment_approved, 
        comment_type,comment_author_url, comment_content FROM wp_term_taxonomy as t1, 
            wp_posts, wp_term_relationships as r1, wp_comments c, wp_commentmeta as cm
        WHERE comment_approved = '1'
           AND comment_type = ''
           AND ID = comment_post_ID
           AND post_password = ''
           AND ID = r1.object_id
           AND r1.term_taxonomy_id = t1.term_taxonomy_id
           AND t1.taxonomy = 'category'
           AND cm.meta_key LIKE 'featured'
           AND cm.meta_value = 1
           AND cm.comment_id = c.comment_ID
           AND t1.term_id IN ('$category')
        ORDER BY comment_date DESC LIMIT $limit;";
        
    } else {
        $sql = "SELECT wp_commentmeta.*, wp_comments.comment_ID, wp_comments.comment_content, wp_comments.comment_post_ID, wp_comments.comment_author, wp_comments.comment_date, wp_posts.post_title, wp_posts.post_date FROM $wpdb->commentmeta, $wpdb->comments, $wpdb->posts WHERE wp_commentmeta.meta_key LIKE 'featured' AND wp_commentmeta.meta_value = 1 AND wp_commentmeta.comment_id = wp_comments.comment_ID AND wp_posts.id = wp_comments.comment_post_ID ORDER BY wp_commentmeta.meta_id DESC LIMIT $limit;";
    }

    return $wpdb->get_results($sql);
}

?>