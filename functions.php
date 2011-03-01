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

// Getting Theme and Child Theme Data
// Based on work by: Joern Kretzschmar

$themeData = get_theme_data(TEMPLATEPATH . '/style.css');
$version = trim($themeData['Version']);
if(!$version)
    $version = "unknown";

$ct=get_theme_data(STYLESHEETPATH . '/style.css');
$templateversion = trim($ct['Version']);
if(!$templateversion)
    $templateversion = "unknown";

// set theme constants
define('THEMENAME', $themeData['Title']);
define('THEMEAUTHOR', $themeData['Author']);
define('THEMEURI', $themeData['URI']);
define('CALPRESSVERSION', $version);

define('THEMESHORTNAME', 'cp');

// set child theme constants
define('TEMPLATENAME', $ct['Title']);
define('TEMPLATEAUTHOR', $ct['Author']);
define('TEMPLATEURI', $ct['URI']);
define('TEMPLATEVERSION', $templateversion);

// set theme path of current theme, regradless if it's a parent or child theme
define('CURRENTTEMPLATEPATH', STYLESHEETPATH);

//get root url of current theme, regardelss of whether it's a parent or child theme
$surl = get_bloginfo('stylesheet_directory');
define('THEMEURL', $surl);

// set root url paths for parent theme assets, for use in parent or child themes
$base_url = get_bloginfo('wpurl');
define('BASEURL', $base_url);
define('CALPRESSURI', BASEURL.'/wp-content/themes/calpress');
define('PARENTASSETS', BASEURL.'/wp-content/themes/calpress/assets');
define('PARENTCSS', PARENTASSETS.'/css');
define('PARENTJS', PARENTASSETS.'/js');
define('PARENTSWF', PARENTASSETS.'/swf');
define('PARENTIMAGES', PARENTASSETS.'/images');


define('PARENTVIDEO', BASEURL.'/wp-content/themes/calpress/videos');

// CACHE
define('CALPRESSCACHE', WP_CONTENT_DIR.'/cache');


// Article Inline sizes, based on 960 columns
define('SIDEBARWIDTH', '290');// 210 + padding 5 = 3 (960.gs 12) columns. 290 + padding 5 = 4 (960.gs 12)
define('SIDEBARPADDING', '5');
define('MEDIAPLAYERCONTROLERHEIGHT', '60');

define('LEADARTSIZE', '620');//620 = 8 (960.gs 12) columns

// Path constants
define('THEMELIB', TEMPLATEPATH . '/library');

// Load base Sandbox functionality 
require_once(THEMELIB . '/sandbox-functions.php');

// Load CalPress theme options and set user-updated constants
//require_once(THEMELIB . '/extensions/theme-options.php');
require_once(THEMELIB . '/extensions/admin-menu.php');


$get_video_location = THEMESHORTNAME."_video_location";
$video_location = get_settings($get_video_location);
define('VIDEOLOCATION', $video_location);

$get_soundslides_location = THEMESHORTNAME."_soundslides_location";
$soundslides_location = get_settings($get_soundslides_location);
define('SOUNDSLIDESLOCATION', $soundslides_location);

// mobile or standard CSS?
require_once(THEMELIB . '/extensions/mobile.php');

if ( $calpress_mobile->showMobile() ) {
    $primarycss = PARENTCSS . '/mobile.css';
    //child theme mobile css? 
    if ( file_exists (CURRENTTEMPLATEPATH . '/mobile.css') ) {
        function childtheme_mobilecss() {
            $secondarycss = THEMEURL . '/mobile.css';
            echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $secondarycss . "\" />");
        }        
        add_action('wp_head', 'childtheme_mobilecss');
    }    
} else {
    $primarycss = get_bloginfo('stylesheet_url');
    // append last modified date to path to clear old caches
    $cssmoddate = date ("YmdHis", filemtime(CURRENTTEMPLATEPATH.'/style.css'));
    $primarycss .= '?ver='.$cssmoddate;
}
define('PRIMARYCSS', $primarycss);
 
// Load CalPress extended functionality 
require_once(THEMELIB . '/extensions/helpers.php');

// Load redirect functionality
require_once(THEMELIB . '/extensions/redirect.php');

// Load CalPress enqueue scripts, etc
require_once(THEMELIB . '/extensions/init.php');

// Load CalPress hooks and related template builders
require_once(THEMELIB . '/extensions/hooks.php');
require_once(THEMELIB . '/extensions/template-helper.php');
require_once(THEMELIB . '/extensions/template-builder.php');

// Load CalPress custom shortcodes (eg: [pullquote]This is my quote[/pullquote])
require_once(THEMELIB . '/extensions/shortcodes.php');

// Load CalPress's built-in ability to put stuff in the head of post of page
require_once(THEMELIB . '/extensions/extrahead.php');

// Load CalPress plugin management extensions. Used to manage plugins that *may* be running alongside CalPress 
require_once(THEMELIB . '/extensions/plugins.php');

// Load CalPress special article/single pages functionality 
require_once(THEMELIB . '/extensions/special-singles.php');

// Load CalPress media embed funtionality 
require_once(THEMELIB . '/extensions/media-players.php');

// Load user photo functionality
require_once(THEMELIB . '/extensions/user-photo.php');

// Load featured comment functionality
require_once(THEMELIB . '/extensions/featured-comments.php');

// Load Geotag functionality
require_once(THEMELIB . '/extensions/admin-geotag.php');

// Load default loops
require_once(TEMPLATEPATH."/loops/loop-content.php");

// Load selectable front page template
require_once(THEMELIB . '/extensions/front-layout.php');

// Load Feed functions
require_once(THEMELIB . '/extensions/feed.php');

// Load relative time functionality for timestamps (like 30 seconds ago, Yesterday, three days ago)
require_once(THEMELIB . '/extensions/relative-time.php');

// Load twitter search widget at end of stories
require_once(THEMELIB . '/extensions/twitter-search.php');

// Load weather functionality
require_once(THEMELIB . '/extensions/weather.php');

// Load custom login screen
require_once(THEMELIB . '/extensions/custom-login.php');



// Load related posts
//require_once(THEMELIB . '/extensions/related-posts.php');

?>