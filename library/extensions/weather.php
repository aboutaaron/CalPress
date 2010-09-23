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

// we want to cache our feed for 60 minutes
function _hourly_feed(){
    return 3600;
}

// Basic weather from Yahoo for a given WOEID. Cached for 60 minutues.

class CalPress_Weather
{
    private $_woeid; // Yahoo Where on Earth ID
    private $_weather; // stores feed
    private $_current_conditions; // stores current conditions in array
    private $_astronomy; // stores sunrise / sunset
    private $_location; // stores city/region/country
    private $_atmosphere; // stores humidity / pressure / visibilty / rising
    private $_validfeed = false;
    
    function __construct($woeid){
        $this->_woeid = $woeid;
        $this->_fetch_feed();
    }

    private function _fetch_feed(){
        
        // yahoo weather feed for our woeid
        $feedsrc = 'http://weather.yahooapis.com/forecastrss?w='.$this->_woeid;
        
        add_filter( 'wp_feed_cache_transient_lifetime', '_hourly_feed');
        $feed = fetch_feed($feedsrc);
        remove_filter('wp_feed_cache_transient_lifetime', '_hourly_feed');
        
        // populate current_conditions global
        if (!is_wp_error( $feed ) ) {
            $this->_weather = $feed->get_item(0);
            $this->_current_conditions = $this->_weather->get_item_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'condition');
            $this->_astronomy = $feed->get_channel_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'astronomy');
            $this->_location = $feed->get_channel_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'location');
            $this->_atmosphere = $feed->get_channel_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'atmosphere');
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
    
    public function get_weather_formatted(){
        $a = $this->get_text_attribution();
        $w = $this->get_basic_weather();
        return '<p class="weather">'. $a . ': ' . $w . '</p>';
    }
    
    public function basic_weather_formatted(){
        echo $this->get_weather_formatted();
    }
    
    public function get_condition_code(){
        return $this->_current_conditions[0]['attribs']['']['code'];
    }
    
    public function condition_code(){
        echo $this->get_condition_code();
    }

    public function get_date(){
        return $this->_current_conditions[0]['attribs']['']['date'];
    }
    
    public function date(){
        echo $this->get_date();
    }
    
    public function get_content(){
        return $this->_weather->get_content();
    }
    
    public function content(){
        echo $this->get_content();
    }
    
    public function get_title(){
        return $this->_weather->get_title();
    }
    
    public function title(){
        echo $this->get_title();
    }
    
    public function get_permalink(){
        return $this->_weather->get_permalink();
    }
    
    public function permalink(){
        echo $this->get_permalink();
    }

    public function get_sunrise(){
        return $this->_astronomy[0]['attribs']['']['sunrise'];
    }

    public function sunrise(){
        echo $this->get_sunrise();
    }
    
    public function get_sunset(){
        return $this->_astronomy[0]['attribs']['']['sunset'];
    }

    public function sunset(){
        echo $this->get_sunset();
    }

    public function get_city(){
        return $this->_location[0]['attribs']['']['city'];
    }

    public function city(){
        echo $this->get_city();
    }
    
    public function get_state(){
        return $this->_location[0]['attribs']['']['region'];
    }

    public function state(){
        echo $this->get_state();
    }

    public function get_country(){
        return $this->_location[0]['attribs']['']['country'];
    }

    public function country(){
        echo $this->get_country();
    }

    public function get_humidity(){
        return $this->_atmosphere[0]['attribs']['']['humidity'];
    }

    public function humidity(){
        echo $this->get_humidity();
    }
    
    public function get_visibility(){
        return $this->_atmosphere[0]['attribs']['']['visibility'];
    }

    public function visibility(){
        echo $this->get_visibility();
    }
    
    
    /* barometric pressure */
    public function get_barometric_pressure(){
        return $this->_atmosphere[0]['attribs']['']['pressure'];
    }

    public function barometric_pressure(){
        echo $this->get_barometric_pressure();
    }
    
    public function get_barometric_pressure_formatted(){
        $pressure = $this->get_barometric_pressure();
        $rising = $this->get_barometric_pressure_status();
        return $pressure . ' and ' . $rising;
    }
    
    public function barometric_pressure_formatted(){
        echo $this->get_barometric_pressure_formatted();
    }
    
    /* Is barometric pressure steady (0), rising (1) or 2 (falling)*/
    public function get_rising(){
        return $this->_atmosphere[0]['attribs']['']['rising'];
    }

    public function rising(){
        echo $this->get_rising();
    }
    
    public function get_barometric_pressure_status(){
        $status = $this->get_rising();
        $status_text = "";
        switch ($status) {
            case 0:
                $status_text = "holding steady";
                break;
            case 1:
                $status_text = "rising";
                break;
            case 2:
                $status_text = "falling";
                break;
        }
        return $status_text;
    }
    
    public function barometric_pressure_status(){
        echo $this->get_barometric_pressure_status();
    }
    
    /*
        TODO 
        units, wind, forecast
    */

}
?>