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
    if ( get_post_custom_values('byline') ) {
        echo("<span class=\"entry-byline\">By: ");
        $byline_values = get_post_custom_values('byline');
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
        $gacode = '<script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
        </script>
        <script type="text/javascript">
        try {
        var pageTracker = _gat._getTracker("'. $gaID . '");
        pageTracker._trackPageview();
        } catch(err) {}
        </script>';

        echo $gacode;   
    }
}
add_action('wp_footer', 'calpress_googleanalytics');

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
    	if ( $menu = str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages('title_li=&sort_column=menu_order&echo=0') ) )
    		$menu = '<ul>' . $menu . '</ul>';
    	$menu = '<div id="menu" class="grid_' . $grid . '">' . $menu . '</div><div class="clear"></div>';
    	echo apply_filters( 'globalnav_menu', $menu ); // Filter to override default globalnav: globalnav_menu   
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
 * @param string $ration = desired ratio. 43 for 4x3. 169 for 16x9 (default).
 * @return void
 */
function calpress_teaseart($w = LEADARTSIZE, $ratio = "169"){
	global $posts;
    if(calpress_postimage()!=false) { //show the lead art photo
        $srcimg = calpress_leadimagepath();//uploaded photo
        $imgsrc = CALPRESSURI.'/library/extensions/timthumb.php?src='.$srcimg.'&amp;w='.$w;//resizer script
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
    $h = calpress_mediaheight($w, $ratio);
    
    if ( get_post_custom_values('lead_video') ) {//show the lead art video
        $h = calpress_mediaheight($w, "169", "video");
        $videos = get_post_custom_values('lead_video');
        
        $options = calpress_parsextrafieldsoptions($videos[0]);
        $slug = $options[0];
        $title = $options[1];
        $cutline = $options[2];
        
        echo("<div class=\"lead-art entry-leadvideo\">");   
            calpress_embedvideo($slug,$cutline,"",$w,$h);
        echo("</div>");
        
    } elseif ( get_post_custom_values('lead_youtube') ) {//show the lead art slideshow
        $h = calpress_mediaheight($w, "169", "video");
        $videos = get_post_custom_values('lead_youtube');
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
    } elseif ( get_post_custom_values('lead_vimeo') ) {//show the lead art slideshow
        $h = calpress_mediaheight($w, "169", "video");
        $videos = get_post_custom_values('lead_vimeo');   
        $options = calpress_parsextrafieldsoptions($videos[0]);
        $vimeoid = $options[0];
        $title = "";
        $cutline = $options[2];
        echo("<div class=\"lead-art entry-leadvimeo\">");
            calpress_embedvimeo($vimeoid,$cutline,$title,$w,$h);
        echo("</div>");
    } elseif ( get_post_custom_values('lead_soundslides') ) {//show the lead art slideshow
        echo("<div class=\"lead-art entry-leadsoundslides\">");
            $h = calpress_mediaheight($w, "43");//always used 4x3
            $h = $h + 34;//add control bar height
            $soundslides = get_post_custom_values('lead_soundslides');
            $options = calpress_parsextrafieldsoptions($soundslides[0]);
            $slug = $options[0];
            
            $path = mysql2date('Y/m/', $post->post_date);
            $path = $path.$slug;
            $title = "";
            $cutline = $options[2];
            calpress_embedsoundslides($path,$cutline,$title,$w,$h);
        echo("</div>");
    } elseif ( get_post_custom_values('lead_embed') ) {//show the lead art embed
       echo("<div class=\"lead-art entry-leadembed\">");
            $embeds = get_post_custom_values('lead_embed');
            $options = calpress_parsextrafieldsoptions($embeds[0]);
            $embedcode = $options[0];
            $title = $options[1];
            $caption = $options[2];
            echo("<div class=\"lead-art entry-leadembed\">"); 
	        echo($embedcode);
	        echo("<div class=\"embed caption\"><p class=\"caption\">$caption</p></div>");
	        echo("</div>");   
       echo("</div>");
   } elseif ( get_post_custom_values('Video') ) {//legacy FLV video on Oakland North
            $h = calpress_mediaheight($w, "169", "video");
            $videos = get_post_custom_values('Video');
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
 * Returns an image url for a resized version of any image on the server
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
    $imgsrc = CALPRESSURI.'/library/extensions/timthumb.php?src='.$srcimg.'&amp;w='.$w;//resizer script
    $formattedURL = "<img alt=\"$alt\" src =\"$imgsrc\" />";
    return $formattedURL;
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
	if ( $images = get_children(array(
		'post_parent' => get_the_ID(),
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
		    return $attachmentimage.'<p class="photo caption">'.$img_desc.'</p>';
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
    if ( $images = get_children(array(
	    'post_parent' => get_the_ID(),
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
    if ( $images = get_children(array(
		'post_parent' => get_the_ID(),
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
	$inlines = calpress_get_inlines();
	$inlines = str_replace("\r\n","",$inlines);
	if (is_single()) {
		$content = $inlines . $content;
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
        $html .= "<div id=\"entry-sidebar\">";    
        
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
        $html .= "</div><!-- //end #entry-sidebar-->";  
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
function calpress_spanishurl(){
     if ( get_post_custom_values('in_spanish') ){
         $url = get_post_custom_values('in_spanish');
        echo("<div class=\"alternate-url spanish\">");
            echo ("<a href=\"$url[0]\">En Espanol</a>");
        echo("</div>");
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
?>