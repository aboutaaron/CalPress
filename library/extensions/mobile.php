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

// Manage mobile site options and templates

// Based very heavily on the awesome WPtouch iPhone Theme plugin 
// http://www.bravenewcode.com

class calPressMobile {
	var $mobiledevice;
	var $iphone;
	var $desired_view;
	var $load_mobile_templates;
	
	function calPressMobile() {
		$this->mobiledevice = false;
		$this->iphone = false;		
		$this->detectMobile();
		$this->setDesiredView();
	}
	
	function showMobile() {
	    if ($this->mobiledevice && $this->desired_view == "mobile") {
	        return true;
	    } else {
	        return false;
	    }
	}
	
	function setDesiredView() {
	    $key = 'calpress_switch_mobile_cookie';
	    
	    // switch view?
	    if (isset($_GET['theme_view'])) {
	   		if ($_GET['theme_view'] == 'mobile') {
				setcookie($key, 'mobile', 0); 
			} elseif ($_GET['theme_view'] == 'standard') {
				setcookie($key, 'standard', 0);
			}
			header('Location: ' . get_bloginfo('siteurl'));
			die;
		}
		
		
		// recall current view setting
		if (isset($_COOKIE[$key])) {
			$this->desired_view = $_COOKIE[$key];
		} else {
			/*
			if ( $settings['enable-regular-default'] ) {
				$this->desired_view = 'standard';
			} else {
		  		$this->desired_view = 'mobile';
			}
			*/
			$this->desired_view = 'mobile';
		}
		
	}
	
	function detectMobile($query = '') {
		$container = $_SERVER['HTTP_USER_AGENT'];
		// The below prints out the user agent array. Uncomment to see it shown on the page.
		//print_r($container); 
		// Add whatever user agents you want here to the array if you want to make this show on another device.
		// No guarantees it'll look pretty, though!
			$useragents = array(		
			"iphone",  				 // Apple iPhone
			"ipod", 					 // Apple iPod touch
			"aspen", 				 // iPhone simulator
			"dream", 				 // Pre 1.5 Android
			"android", 			 // 1.5+ Android
			"cupcake", 			 // 1.5+ Android
			"blackberry9500",	 // Storm
			"blackberry9530",	 // Storm
			"opera mini", 		 // Experimental
			"webos",				 // Experimental
			"incognito", 			 // Other iPhone browser
			"webmate" 			 // Other iPhone browser
		);
		
		// Iphone-only browsers
		    $iphonebrowers = array(
    			"iphone",  				 // Apple iPhone
    			"ipod", 					 // Apple iPod touch
    			"aspen", 				 // iPhone simulator		    
		);
		
		//$devfile =  compat_get_plugin_dir( 'wptouch' ) . '/include/developer.mode';
		$this->mobiledevice = false;

        //see if device is any mobile
		foreach ( $useragents as $useragent ) {
			if ( eregi( $useragent, $container ) || file_exists($devfile) ) {
				$this->mobiledevice = true;
				
				//see if mobile device is an iphone/ipod
				if ( in_array( $useragent, $iphonebrowers ) ) {
				    $this->iphone = true;
				}
			} 	
		}
	}
}

global $calpress_mobile;
$calpress_mobile = new calPressMobile();
?>