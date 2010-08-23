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

// Theme options/settings adapted from Thematics theme-options.php, which is derived from 
// "A Theme Tip For WordPress Theme Authors" 
// http://literalbarrage.org/blog/archives/2007/05/03/a-theme-tip-for-wordpress-theme-authors/

$themename = "CalPress";
$shortname = THEMESHORTNAME;

// get dyanmic front page templats from /layouts/ folder in either parent or child theme
// based on a snippet from Premium News from Woo Themes, downloaded 8.19.2009.
$layout_paths = array();
$layout_paths[] = CURRENTTEMPLATEPATH . '/layouts/'; // child themes
$layout_paths[] = TEMPLATEPATH . '/layouts/'; // parent theme
$layouts = array();
foreach ($layout_paths as $key => $layout_path) {
    if ( is_dir($layout_path) ) {
    	if ($layout_dir = opendir($layout_path) ) { 
    		while ( ($layout_file = readdir($layout_dir)) !== false ) {
    			if(stristr($layout_file, ".php") !== false) {
    				$layouts[] = $layout_file;
    			}
    		}	
    	}
    }
}
$layouts = array_unique($layouts);

// get all categories for a selectable front page list. 
$front_cats = &get_categories('hide_empty=0');
$front_categories = array();
$front_categories[0] = "---";
foreach ($front_cats as $category) {
    $front_categories[$category->cat_ID] = $category->cat_name;
}


// WP-Poll
// Some CalPress themes support WP-Poll in various templates.
// Grab a list for a featured poll
// http://lesterchan.net/wordpress/readme/wp-polls.html
if (function_exists('vote_poll')) {
    $polls = $wpdb->get_results("SELECT * FROM $wpdb->pollsq  ORDER BY pollq_timestamp DESC");
    $poll_list = array();
    $poll_list[0] = "---";
    foreach($polls as $poll) {
		$poll_id = intval($poll->pollq_id);
		$poll_question = stripslashes($poll->pollq_question);
		$poll_list[$poll_id] = $poll_question;
	}
}

 
// CalPress configuration options
$calpress_theme_options = array (
	array(	"name" => __('Front Page Category','calpress'),
			"desc" => __('Use selected category for front page. If not set, pulls from all Posts by date.','calpress'),
			"id" => $shortname."_front_category",
			"std" => "",
			"type" => "arraylist",
			"options" => $front_categories),
	
	array(	"name" => __('Hide Page-based Nav Menu','calpress'),
			"desc" => __("By default, CalPress lists pages (or Menu Editor selections in WP3) as navigation elements under the header. This behavior can be turned off.",'calpress'),
			"id" => $shortname."_hide_nav_menu",
			"std" => "false",
			"type" => "checkbox"),
			
	array(	"name" => __('Enable Mobile Site','calpress'),
			"desc" => __("By default, CalPress does not show a mobile version of the site. When enabled, mobile stylesheets are used and lead art sizes are reworked.",'calpress'),
			"id" => $shortname."_show_mobile",
			"std" => "false",
			"type" => "checkbox"),
			
	array(	"name" => __('Video Location','calpress'),
			"desc" => __('Path to video files, with the trailing slash. eg: /videos/ or http://www.berkeley.edu/videos/','calpress'),
			"id" => $shortname."_video_location",
			"std" => PARENTVIDEO."/",
			"type" => "text"),
			
    array(	"name" => __('Soundslides Location','calpress'),
			"desc" => __('Path to root soundslides directory, with the trailing slash. eg: /soundslides/ or http://www.berkeley.edu/soundslides/. CalPress will look for Soundsslides in this folder + plus /YYYY/MM/ of the post added.','calpress'),
			"id" => $shortname."_soundslides_location",
			"std" => PARENTVIDEO."/",
			"type" => "text"),

    array(	"name" => __('JW Player','calpress'),
			"desc" => __('By default, CalPress expects QuickTime video files. But we love the very cool Flash-based JW Player. Because of license restrictions, we can\'t bundle the JW player with CalPress. Please <a href="http://www.longtailvideo.com/">download it</a> and upload it to your web server. Put the path to it here. eg: http://yourhost.com/media-player.swf or /wp-content/themes/calpress/media-player.swf','calpress'),
			"id" => $shortname."_jw_player",
			"std" => "",
			"type" => "text"),
    
    array(	"name" => __('JW Player Theme','calpress'),
			"desc" => __('Path to JW player theme. eg: /player/snel.swf or http://www.berkeley.edu/snel.swf','calpress'),
			"id" => $shortname."_jw_theme",
			"std" => "",
			"type" => "text"),

	array(	"name" => __('Info on Author Page','calpress'),
			"desc" => __("Display a <a href=\"http://microformats.org/wiki/hcard\" target=\"_blank\">microformatted vCard</a>—with the author's avatar, bio and email—on the author page.",'calpress'),
			"id" => $shortname."_authorinfo",
			"std" => "false",
			"type" => "checkbox"),


	array(	"name" => __('Google Analytics Code','calpress'),
			"desc" => __("If you want to use Google Analytics for this site, enter your Google Analytics ID (eg: UA-5988962-11).",'calpress'),
			"id" => $shortname."_ga",
			"std" => __(" ", 'calpress'),
			"type" => "text",),
			
    array(	"name" => __('ShareThis','calpress'),
			"desc" => __("If you want to use ShareThis at the top of your posts, put your publisher code here (eg: a56a1066-bab8-4bb1-bb9f-81120f8b8a79). If you need an account, go to: http://sharethis.com/",'calpress'),
			"id" => $shortname."_sharethis",
			"std" => __(" ", 'calpress'),
			"type" => "text",),
);


// CalPress Producer options
$calpress_nonadmin_options = array (
    array(	"name" => "Front Page Layout",
			"desc" => "Choose the layout of to be used for the other entries on your homepage.",
    		"id" => $shortname."_layout",
    		"std" => "default.php",
    		"type" => "layout",
    		"options" => $layouts),
    		
    array(	"name" => __('Lead Story Override','calpress'),
			"desc" => __('On supported themes, HTML code here replaces the lead story on the page. To restore the main feature, this code box *must* be empty. <a href="#">Documentation</a><br />','calpress'),
			"id" => $shortname."_front_feature_override",
			"std" => "",
			"type" => "textarea",
			"options" => array(	"rows" => "5",
								"cols" => "94") ),
    		
    array(	"name" => __('Front Features','calpress'),
			"desc" => __('On supported themes, HTML code here shows up in the feature block of your front page. It is usually below the main feature of a site. <a href="#">Documentation</a><br />','calpress'),
			"id" => $shortname."_front_features",
			"std" => "",
			"type" => "textarea",
			"options" => array(	"rows" => "5",
								"cols" => "94") ),
								
	array(	"name" => __('Front Page Poll','calpress'),
			"desc" => __('Requires WP-Poll and CalPress Poll-aware theme (like Mission and Oakland). Leave blank for no poll. <a href="#">Documentation</a>','calpress'),
			"id" => $shortname."_front_poll",
			"std" => "",
			"type" => "arraylist",
			"options" => $poll_list),
			
    array(	"name" => __('Front Page Extra CSS','calpress'),
			"desc" => __('Path to CSS only applied to front page. <a href="#">Documentation</a>','calpress'),
			"id" => $shortname."_front_extra_css",
			"std" => "",
			"type" => "text"),
			
    array(	"name" => __('Front Page Extra Javascript','calpress'),
			"desc" => __('Path to J/S only applied to front page. <a href="#">Documentation</a>','calpress'),
			"id" => $shortname."_front_extra_js",
			"std" => "",
			"type" => "text"),
);



// Build menus, setup pages and process any options or changes
add_action('admin_menu', 'calpress_admin_menus');
function calpress_admin_menus()
{
    global $themename, $shortname, $calpress_nonadmin_options, $calpress_theme_options;

    // Update settings for saves on the Producer page (admin-menu.php) or he config page (admin-configuration.php)
    if ( $_GET['page'] == basename(__FILE__) || $_GET['page'] == "admin-configuration.php") {

        if ( $_GET['page'] == basename(__FILE__) ) { // Produer options
            $current_options = $calpress_nonadmin_options; 
            $header_redirect_page = "admin-menu";
        } else { // Theme settings
            $current_options = $calpress_theme_options; 
            $header_redirect_page = "admin-configuration";
        }
        
        
        if ( 'save' == $_REQUEST['action'] ) {

            foreach ($current_options as $value) {
               update_option( $shortname."_layout", $_REQUEST[ $value['id'] ] ); 
            }

            foreach ($current_options as $value) {
               if( isset( $_REQUEST[ $value['id'] ] ) ) { 
                   update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
                } else { 
                    delete_option( $value['id'] ); 
                } 
            }

            header("Location: admin.php?page=".$header_redirect_page.".php&saved=true");
            die;

        } elseif ( 'reset' == $_REQUEST['action'] ) {
            foreach ($current_options as $value) {
                delete_option( $value['id'] ); 
            }
            
            header("Location: admin.php?page=".$header_redirect_page.".php&reset=true");
            die;
        }
    }
    
    
    // required sub pages for menu
    require_once( dirname(__FILE__).'/admin-producer.php');
    require_once( dirname(__FILE__).'/admin-documentation.php');
    require_once( dirname(__FILE__).'/admin-configuration.php');
    require_once( dirname(__FILE__).'/admin-geotag.php');
    
    
    // build menu
    add_menu_page('CalPress Producer', 'CalPress', 'edit_others_posts', basename(__FILE__), 'calpress_producer_admin', PARENTIMAGES . '/admin/calpress_logo16.png');
    add_submenu_page( basename(__FILE__), 'CalPress Front Page Producer', 'Producer', 'edit_others_posts', basename(__FILE__), 'calpress_producer_admin');
    add_submenu_page( basename(__FILE__), 'CalPress Documentation', 'Documentation', 'edit_others_posts', 'admin-documentation.php', 'calpress_documentation_admin');
    add_submenu_page( basename(__FILE__), 'CalPress Configuration', 'Basic Configuration', 'manage_options', 'admin-configuration.php', 'calpress_configuration_admin');
    add_submenu_page( basename(__FILE__), 'CalPress Geo Options', 'Geotag Configuration', 'manage_options', 'admin-geotag.php', 'Geotag::displayOptions');
}

// Intenal CSS for calpress admin pages
add_action( 'admin_head', 'calpress_admin_css' );
function calpress_admin_css() {
    require_once( dirname(__FILE__).'/admin-css.php');
}

?>