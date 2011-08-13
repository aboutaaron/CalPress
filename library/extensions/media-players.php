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

/**
 * Displays HTML for JW video player
 *
 * To get the actual code without displaying it, use calpress_get_embedvideo()
 *
 * @param string $m = video slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @param string $s = JW player theme
 * @return void
 */
function calpress_embedvideo($m,$c="",$t="",$w=250,$h=188,$s=""){
	echo calpress_get_embedvideo($m,$c,$t,$w,$h,$s);
}

/**
 * HTML for JW video player
 *
 * To simply display the player, use calpress_embedvideo()
 *
 * @param string $m = video slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @param string $s = JW player theme
 * @return string
 */
function calpress_get_embedvideo($m,$c="",$t="",$w=250,$h=188,$s=""){
	$html = ""; //html for embed
    $r = rand(); //random number appended to div id to enure it's unique
    
	$height = $h - MEDIAPLAYERCONTROLERHEIGHT + 16;
	$location = VIDEOLOCATION.$m.'/'.$m;

    //title
    if($t != ""){
        $html .= "<div class=\"video title\"><h3>$t</h3></div>";
    }

    
    //non-Flash quicktime version
	$html .= "
    <div id=\"video_$r\">
        <object width=\"$w\" height=\"$height\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\">
        	<param name=\"src\" value=\"$location-poster.jpg\" />
        	<param name=\"href\" value=\"$location.mov\" />
        	<param name=\"target\" value=\"myself\" />
        	<param name=\"controller\" value=\"true\" />
        	<param name=\"autoplay\" value=\"false\" />
        	<param name=\"scale\" value=\"tofit\" />
        	<embed width=\"$w\" height=\"$height\" type=\"video/quicktime\" pluginspage=\"http://www.apple.com/quicktime/download/\"
        		src=\"$location-poster.jpg\"
        		href=\"$location.mov\"
        		target=\"myself\"
        		controller=\"true\"
        		autoplay=\"false\"
        		scale=\"tofit\">
        	</embed>
        </object>
    </div>
    ";
   
    // check to see if a JW player path as been specified in the admin. if so, load the player. 
    // and override the quicktime reference movie
    
    // site-wide skin?
    $get_jw_player = THEMESHORTNAME."_jw_player"; // get value from admin
    $jw_player = trim(get_settings($get_jw_player));
    
    // check to see if we can monitor Google Analytics for the video player
    $get_jw_player_analytics = THEMESHORTNAME."_ga"; // get value from admin
    $jw_player_analytics = trim(get_settings($get_jw_player));
    
    if($jw_player != ""){
        
        // check to see if we need to load a skin for the JW player
        $player_skin = ""; //blank by default. if left blank, no attempt to load a skin will be made    
        if($s!=""){//custom skin on function call? 
            $player_skin = $s;
        }else{ // site-wide skin?
            $get_jw_skin = THEMESHORTNAME."_jw_theme"; // get value from admin
            $jw_skin = trim(get_settings($get_jw_skin));
            if($jw_skin != ""){
                $player_skin = $jw_skin;
            }
        }
        
        $html .= "
        <script type=\"text/javascript\">
            var so = new SWFObject('$jw_player','player','$w','$h','9');
            so.addParam('allowfullscreen','true');
            so.addParam('allowscriptaccess','always');
            so.addVariable('file', '$location-iPhone.m4v');
            so.addVariable('image','$location-poster.jpg');
			";
         
            if ($player_skin != "") {
                $html .= "so.addVariable('skin', '$player_skin');";
            }
           
            if ($jw_player_analytics != "") {
                $html .= "so.addVariable('plugins', 'googlytics-1,viral-1d');";
            } else {
                $html .= "so.addVariable('plugins', 'viral-1d');";
            }
           
            $html .= "so.write('video_$r');
        </script>
        <div class=\"entry-video-downloads\">
            <span class=\"video-download iPod\"><a href=\"$location-iPhone.m4v\">iPod</a></span> <span class=\"meta-sep\">|</span> <span class=\"video-download HQ\"><a href=\"$location-desktop.m4v\">HQ</a></span>
        </div>
        ";

		if($c!=""){
            $html .= "<p class=\"caption\">$c</p>\n";
        } 
    }  
	return $html;
  
}//end calpress_embedvideo


/**
 * Displays audio version of JW video player
 *
 * To get the actual code without displaying it, use calpress_get_embedaudio
 *
 * @param string $m = audio slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @param string $s = JW player theme
 * @return void
 */
function calpress_embedaudio($m,$c="",$t="",$w=250,$h=188,$s=""){
	echo calpress_get_embedaudio($m,$c,$t,$w,$h,$s);
}

/**
 * HTML for audio version of JW video player
 *
 * To simply display the player, use calpress_embedaudio()
 *
 * @param string $m = audio slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @param string $s = JW player theme
 * @return string
 */
function calpress_get_embedaudio($m,$c="",$t="",$w=250,$h=188,$s=""){
	$html = ""; //html for embed
    $r = rand(); // random number appended to div id to enure it's unique
    $location = SOUNDSLIDESLOCATION.$m;
    
//print title
    if($t != ""){
         $html .= "<div class=\"audio title\"><h3>$t</h3></div>\n";
    }

    // print the non-Flash version
   	$html .= 
		"<div id=\"audio_$r\">\n
		    <object width=\"$w\" height=\"$h\">
		    <param name=\"src\" value=\"$location\" />
		    <param name=\"autoplay\" value=\"false\" />
		    <param name=\"controller\" value=\"true\" />
		    <embed src=\"$location\" autostart=\"false\" loop=\"false\" width=\"$w\" height=\"$h\" controller=\"true\"></embed>
		    </object>
		</div>\n";

    // site-wide skin?
    $get_jw_player = THEMESHORTNAME."_jw_player"; // get value from admin
    $jw_player = trim(get_settings($get_jw_player));
    
    // check to see if we can monitor Google Analytics for the audio player
    $get_jw_player_analytics = THEMESHORTNAME."_ga"; // get value from admin
    $jw_player_analytics = trim(get_settings($get_jw_player));
    
    if($jw_player != ""){
        
        // check to see if we need to load a skin for the JW player
        $player_skin = ""; //blank by default. if left blank, no attempt to load a skin will be made    
        if($s!=""){//custom skin on function call? 
            $player_skin = $s;
        }else{ // site-wide skin?
            $get_jw_skin = THEMESHORTNAME."_jw_theme"; // get value from admin
            $jw_skin = trim(get_settings($get_jw_skin));
            if($jw_skin != ""){
                $player_skin = $jw_skin;
            }
        }
        $html .= "
        <script type=\"text/javascript\">
            var so = new SWFObject('$jw_player','player','$w','$h','9');
            so.addParam('allowfullscreen','true');
            so.addParam('allowscriptaccess','always');
            so.addVariable('file', '$location');
			\n";
            
         if ($player_skin != "") {
              $html .= "so.addVariable('skin', '$player_skin');\n";
         }
     
         if ($jw_player_analytics != "") {
             $html .= "so.addVariable('plugins', 'googlytics-1');\n";
         }
           
             $html .= "so.write('audio_$r');
        </script>
        <div class=\"entry-audio-downloads\">
            <span class=\"audio-download iPod\"><a href=\"$location\">Download MP3</a></span>
        </div>\n";

        if($c!=""){
            $html .= "<p class=\"caption\">$c</p>\n";
        } 
    }

    return $html;
}//end calpress_embedaudio


/**
 * Displays JW video player for legacy video, mainly from the JW player plugin
 *
 * To get the actual code without displaying it, use calpress_get_legacyvideo()
 *
 * @param string $m = video slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @param string $s = JW player theme
 * @return void
 */
function calpress_legacyvideo($m,$c="",$t="",$w=250,$h=188,$s=""){
	echo calpress_get_legacyvideo($m,$c,$t,$w,$h,$s);
}

/**
 * HTML for JW video player for legacy video, mainly from the JW player plugin
 *
 * To display the legacy video, use calpress_legacyvideo()
 *
 * @param string $m = video slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @param string $s = JW player theme
 * @return string
 */
function calpress_get_legacyvideo($m,$c="",$t="",$w=250,$h=188,$s=""){
	$html = ""; //html for embed
    $r = rand(); // random number appended to div id to enure it's unique
    
    //title
    if($t != ""){
        $html .= "<div class=\"video title\"><h3>$t</h3></div>";
    }
    
    // site-wide skin?
    $get_jw_player = THEMESHORTNAME."_jw_player"; // get value from admin
    $jw_player = trim(get_settings($get_jw_player));

	// check to see if we can monitor Google Analytics for the audio player
    $get_jw_player_analytics = THEMESHORTNAME."_ga"; // get value from admin
    $jw_player_analytics = trim(get_settings($get_jw_player));

    if($jw_player != ""){
        
        // check to see if we need to load a skin for the JW player
        $player_skin = ""; //blank by default. if left blank, no attempt to load a skin will be made    
        if($s!=""){//custom skin on function call? 
            $player_skin = $s;
        }else{ // site-wide skin?
            $get_jw_skin = THEMESHORTNAME."_jw_theme"; // get value from admin
            $jw_skin = trim(get_settings($get_jw_skin));
            if($jw_skin != ""){
                $player_skin = $jw_skin;
            }
        }
        
        //non-Flash quicktime version
		$html .= "
	    <div id=\"video_$r\"></div>
        <script type=\"text/javascript\">
            var so = new SWFObject('$jw_player','player','$w','$h','9');
            so.addParam('allowfullscreen','true');
            so.addParam('allowscriptaccess','always');
            so.addVariable('file', '$m');
		\n";

		 if ($player_skin != "") {
              $html .= "so.addVariable('skin', '$player_skin');\n";
         }

         if ($jw_player_analytics != "") {
             $html .= "so.addVariable('plugins', 'googlytics-1');\n";
         }
          
		 $html .= "so.write('video_$r');
        </script>\n";

		if($c!=""){
            $html .= "<p class=\"caption\">$c</p>\n";
        } 
    }
  	return $html;
}//end calpress_legacyvideo

/**
 * Display YouTube videos by passing unique YouTube video ID
 *
 * To get the actual code without displaying it, use calpress_get_embedyoutube()
 *
 * @param string $m = YouTube Video ID
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @return void
 */
function calpress_embedyoutube($m,$c="",$t="",$w=250,$h=188){
	echo calpress_get_embedyoutube($m,$c,$t,$w,$h);
}

/**
 * HTML for YouTube videos by passing unique YouTube video ID
 *
 * To display a YouTube video, use calpress_embedyoutube()
 *
 * @param string $m = YouTube Video ID
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @return string
 */
function calpress_get_embedyoutube($m,$c="",$t="",$w=250,$h=188){
	$html = "";
    $r = rand();

	//title
    if($t != ""){
        $html .= "<div class=\"youtube title\"><h3>$t</h3></div>\n";
    }
  
    $html .= "
    <div id=\"video_$r\">Flash Player 8 required to see YouTube video</div>
    <script language=\"JavaScript\" type=\"text/javascript\">
        var so = new SWFObject('http://www.youtube.com/v/$m', 'youtube', '$w', '$h', '8');
        so.addParam(\"wmode\", \"transparent\"); 
        so.write('video_$r');
    </script>\n";
	if($c!=""){
        $html .= "<p class=\"youtube caption\">$c</p>\n";
    }
	return $html;
}//end calpress_embedyoutube

/**
 * Display Vimeo videos by passing unique Vimeo ID
 *
 * To get the actual code without displaying it, use calpress_get_embedvimeo()
 *
 * @param string $m = YouTube Video ID
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @return void
 */
function calpress_embedvimeo($m,$c="",$t="",$w=250,$h=188){
	echo calpress_get_embedvimeo($m,$c,$t,$w,$h);
}

/**
 * HTML for Vimeo videos by passing unique YouTube video ID
 *
 * To display a Vimeo video, use calpress_embedvimeo()
 *
 * @param string $m = Vimeo ID
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @return string
 */
function calpress_get_embedvimeo($m,$c="",$t="",$w=250,$h=188){
	$html = "";
	$r = rand();
	
	//title
    if($t != ""){
        $html .= "<div class=\"vimeo title\"><h3>$t</h3></div>\n";
    }
    
	$html .= "
    <div id=\"video_$r\">Flash Player 8 required to see Vimeo video</div>
    <script language=\"JavaScript\" type=\"text/javascript\">
        var so = new SWFObject('http://vimeo.com/moogaloop.swf?clip_id=$m&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=59a5d1&amp;fullscreen=1', 'vimeo', '$w', '$h', '8');
        so.addParam(\"wmode\", \"transparent\");
        so.addParam('allowfullscreen','true');
        so.write('video_$r');
    </script>\n";

	if($c!=""){
        $html .= "<p class=\"vimeo caption\">$c</p>\n";
    }
	return $html;
}//end calpress_embedvimeo


/**
 * Display a Soundslides presentation
 *
 * To get the actual code without displaying it, use calpress_get_embedsoundslides()
 *
 * @param string $m = soundslides path&slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @return void
 */
function calpress_embedsoundslides($m,$c="",$t="",$w=620,$h=498){
	echo calpress_get_embedsoundslides($m,$c,$t,$w,$h);
}

/**
 * HTML for a Soundslides presentation
 *
 * To display a Soundslides, use calpress_embedsoundslides()
 *
 * @param string $m = soundslides path&slug
 * @param string $c = cutline
 * @param string $t = title
 * @param int $w = width
 * @param int $h = height
 * @return string
 */
function calpress_get_embedsoundslides($m,$c="",$t="",$w=620,$h=498){
    $html = "";
	$r = rand();
	$location = SOUNDSLIDESLOCATION.$m;
	//title
	if($t != ""){
	    $html .= "<div class=\"soundslides title\"><h3>$t</h3></div>";
	}
	
	$html .= "
    <div id=\"soundslides_$r\">Flash Player 8 required to see this photo gallery</div>
    <script language=\"JavaScript\" type=\"text/javascript\">
        var so = new SWFObject('$location/soundslider.swf?size=2&format=xml&embed_width=$w&embed_height=$h&autoload=false', 'soundsslide', '$w', '$h', '8');
        so.addParam('allowfullscreen','true');
        so.addParam(\"wmode\", \"transparent\"); 
        so.write('soundslides_$r');
    </script>\n";

	if($c!=""){
        $html .= "<p class=\"soundslides caption\">$c</p>\n";
    }
	return $html;
}

/**
 * Display a Google Map
 *
 * To get the actual code without displaying it, use calpress_get_embedmap()
 *
 * @param int $lat = latitude
 * @param int $lng = longitude
 * @param int $z = zoomlevel
 * @param int $t = map type (ROADMAP (default), SATELLITE, HYBRID, TERRAIN)
 * @param int $w = width
 * @param int $h = height
 * @param string $c = cutline
 * @param string $ti = title
 * @return void
 */
function calpress_embedmap($lat=37.875356, $lng=-122.260817,$z=15,$t="ROADMAP",$w=250,$h=188,$ti="",$c="",$points=array()){
	echo calpress_get_embedmap($lat, $lng,$z,$t,$w,$h,$ti,$c,$points);
}

/**
 * HTML for a Google Map
 *
 * To display a map, use calpress_embedmap()
 *
 * @param int $lat = latitude
 * @param int $lng = longitude
 * @param int $z = zoomlevel
 * @param int $t = map type (ROADMAP (default), SATELLITE, HYBRID, TERRAIN)
 * @param int $w = width
 * @param int $h = height
 * @param string $c = cutline
 * @param string $ti = title
 * @return string
 */
function calpress_get_embedmap($lat=37.875356, $lng=-122.260817,$z=15,$t="ROADMAP",$w=250,$h=188,$ti="",$c="",$points=array()){
    $html = "";
	$r = rand();

    $html .= "<div id=\"map_$r\">";
     //title
	if($ti != ""){
	    $html .= "<div class=\"map title\"><h3>$ti</h3></div>";
	}
	
	$html .= "
        <div id=\"map_canvas\" style=\"width:".$w."px; height:".$h."px\"></div>
        <script type=\"text/javascript\">  
            function draw_map_$r() {
              var myLatlng = new google.maps.LatLng($lat, $lng);
              var myOptions = {
                zoom: $z,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.$t
              	}
              var map = new google.maps.Map(document.getElementById(\"map_canvas\"), myOptions);\n";
             
              if (count($points) > 0) {
                    foreach ($points[0] as $k => $v) {
                      	$r2 = rand();
                   		$html .= "
                        var currentLatLng = new google.maps.LatLng($v[0], $v[1]);
                    
                        var marker_$r2 = new google.maps.Marker({
                            position: currentLatLng, 
                            map: map,
                            title:\"$points[1][$k]\"
                        });\n";
                    }
                }
			$html .="
            }
            gmapV3(\"draw_map_$r\");
        </script>\n";
    	
		if($c!=""){
	        $html .= "<p class=\"map caption\">$c</p>\n";
	    }
		
	$html .= "</div>";
	return $html;
}

/**
 * Display a Dotspotting Map
 *
 * To get the actual code without displaying it, use calpress_get_embed_dotspotting()
 *
 * @param int $lat = latitude
 * @param int $lng = longitude
 * @param int $z = zoomlevel
 * @param int $uid = dotspotting user id
 * @param int $st = dotspotting sheet number
 * @param int $w = width
 * @param int $h = height
 * @param string $ti = title
 * @param boolean $sti = show title?
 * @param boolean $slnk = show dotspotting link?
 * @return void
 */
function calpress_embed_dotspotting($lat=37.7621, $lng=-122.4174,$z=14,$uid=233,$st=1083,$w=620,$h=400,$ti="",$sti=true,$slnk=true, $showui=1, $base="toner", $iconbase=""){
	echo calpress_get_embed_dotspotting($lat, $lng, $z, $uid, $st, $w, $h, $ti, $sti, $slnk, $showui, $base, $iconbase);
}

if (!function_exists('calpress_get_embed_dotspotting')):
/**
 * HTML for a Dotspotting Map
 *
 * To display a map, use calpress_embed_dotspotting()
 *
 * @param int $lat = starting latitude
 * @param int $lng = starting longitude
 * @param int $z = zoomlevel
 * @param int $uid = dotspotting user id
 * @param int $st = dotspotting sheet number
 * @param int $w = width
 * @param int $h = height
 * @param string $ti = title
 * @param boolean $sti = show title?
 * @param boolean $slnk = show dotspotting link?
 * @param int $showui = 0 or 1 to show legend in map
 * @param string $base = map theme, i.e. crime, toner
 * @return string
 */
function calpress_get_embed_dotspotting($lat=37.7621, $lng=-122.4174, $z=14, $uid=233, $st=1083, $w=620, $h=400, $ti="", $sti=true, $slnk=true, $showui=1, $base="toner", $iconbase=""){
    $html = "";
	if($ti != ""){
		$pti = split(" ", $ti);
		$pti = join("+", $pti);
	}
    $html .= "<iframe type=\"text/html\" width=\"" . $w . "\" height=\"". $h ."\" "; 
	$html .= "src=\"http://dotspotting.org/embed/crime/map?user=" . $uid . "&amp;sheet=". $st ."&amp;";
	if($ti != ""){
		$html .= "title=". $pti . "&amp;";
	}
	if($iconbase != ""){
		$html .= "iconbase=" . $iconbase . "&amp;";
	}
	$html .= "ui=". $showui ."&amp;base=". $base ."&amp;\" frameborder=\"0\"></iframe>\n";
	if($slnk){	
		$html .= "<p class=\"dotspotting-linkback\"><a href='http://dotspotting.org/u/". $uid ."/sheets/". $st ."'>Map by Dotspotting.org</a>\n";
	}
	return $html;
}

endif;

?>