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
include_once(ABSPATH . WPINC . '/feed.php');

// Basic weather from Yahoo for a given WOEID. Cached for 60 minutues.

class CalPress_Weather
{
    private $_woeid; // Yahoo Where on Earth ID
    private $_current_conditions;
    private $_validfeed = false;
    
    function __construct($woeid){
        $this->_woeid = $woeid;
        $this->_fetch_feed();
    }
    
    private function _fetch_feed(){
        
        // we want to cache our feed for 60 minutes
        function hourly_feed(){
            return 3600;
        }
        
        // yahoo feed for our woeid
        $feedsrc = 'http://weather.yahooapis.com/forecastrss?w='.$this->_woeid;
        
        add_filter( 'wp_feed_cache_transient_lifetime', 'hourly_feed');
        $feed = fetch_feed($feedsrc);
        remove_filter('wp_feed_cache_transient_lifetime', 'hourly_feed');
        
        // populate current_conditions global
        if (!is_wp_error( $feed ) ) {
            $weather = $feed->get_item(0);
            $this->_current_conditions = $weather->get_item_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'condition');
            $_validfeed = true;
        }
    }
    
    public function get_temperature(){
        return  $this->_current_conditions[0]['attribs']['']['temp'];
    }
    
    public function temperature(){
        echo $this->get_temperature();
    }
    
    public function get_current_condition(){
         return $this->_current_conditions[0]['attribs']['']['text'];
    }
    
    public function current_condition(){
        echo $this->get_current_condition();
    }
    
    public function get_text_attribution(){
        return "Yahoo! Weather";
    }
    
    public function text_attribution(){
        echo $this->get_text_attribution();
    }
    
    public function get_basic_weather(){
        $c = $this->get_current_condition();
        $t = $this->get_temperature();
        return $c . ', '. $t .'&#176;';
    }

    public function basic_weather(){
        echo $this->get_basic_weather();
    }
    
    public function basic_weather_formatted(){
        $a = $this->get_text_attribution();
        $w = $this->get_basic_weather();
        echo '<p class="weather">'. $a . ': ' . $w . '</p>';
    }

}
?>