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
    private $_units; // store units being used - mi / pressure / speed / distance 
    private $_wind; // store wind info  
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
            $this->_units = $feed->get_channel_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'units');
            $this->_wind = $feed->get_channel_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'wind');
            $_validfeed = true;
        }
    }
    
    /**
     * Return current temperature
     *
     * @return string
     */
    public function get_temperature(){
        return  $this->_current_conditions[0]['attribs']['']['temp'];
    }
    
    /**
     * Echo current temperature
     *
     * @return none
     */
    public function temperature(){
        echo $this->get_temperature();
    }
    
    /**
     * Return current conditions text (cloudy, sunny, etc)
     *
     * @return string
     */
    public function get_current_condition(){
         return $this->_current_conditions[0]['attribs']['']['text'];
    }
    
    /**
     * Print current conditions text (cloudy, sunny, etc)
     *
     * @return none
     */
    public function current_condition(){
        echo $this->get_current_condition();
    }
    
    /**
     * Return weather source attribution 
     *
     * @return string
     */
    public function get_text_attribution(){
        return "Yahoo! Weather";
    }
    
    /**
     * Print weather source attribution 
     *
     * @return none
     */
    public function text_attribution(){
        echo $this->get_text_attribution();
    }
    
    /**
     * Return basic weather string (cloudy, 64 with degrees ascii)
     *
     * @return string
     */
    public function get_basic_weather(){
        $c = $this->get_current_condition();
        $t = $this->get_temperature();
        return $c . ', '. $t .'&#176;';
    }

    /**
     * Print basic weather string (cloudy, 64 with degrees ascii)
     *
     * @return none
     */
    public function basic_weather(){
        echo $this->get_basic_weather();
    }
    
    /**
     * Return basic weather formatted in HTML with attribution(Yahoo! Weather: cloudy, 64 with degrees ascii)
     *
     * @return string
     */
    public function get_weather_formatted_html(){
        $a = $this->get_text_attribution();
        $w = $this->get_basic_weather();
        return '<p class="weather">'. $a . ': ' . $w . '</p>';
    }
    
    /**
     * Print basic weather formatted in HTML with attribution(Yahoo! Weather: cloudy, 64 with degrees ascii)
     *
     * @return string
     */
    public function weather_formatted_html(){
        echo $this->get_weather_formatted_html();
    }
    
    /**
     * Return current condition code, which corresponds to a text status (eg: 10 = freezing rain)
     * http://developer.yahoo.com/weather/
     *
     * @return string
     */
    public function get_condition_code(){
        return $this->_current_conditions[0]['attribs']['']['code'];
    }
    
    /**
     * Print current condition code, which corresponds to a text status (eg: 10 = freezing rain)
     * http://developer.yahoo.com/weather/
     *
     * @return none
     */
    public function condition_code(){
        echo $this->get_condition_code();
    }

    /**
     * Return date of weather report
     *
     * @return string
     */
    public function get_date(){
        return $this->_current_conditions[0]['attribs']['']['date'];
    }
    
    /**
     * Print date of weather report
     *
     * @return none
     */
    public function date(){
        echo $this->get_date();
    }
    
    /**
     * Return general text description of current weather
     *
     * @return string
     */
    public function get_content(){
        return $this->_weather->get_content();
    }
    
    /**
     * Print general text description of current weather
     *
     * @return none
     */
    public function content(){
        echo $this->get_content();
    }
    
    /**
     * Return Yahoo's weather title (brief description)
     *
     * @return string
     */
    public function get_title(){
        return $this->_weather->get_title();
    }
    
    /**
     * Print Yahoo's weather title (brief description)
     *
     * @return string
     */
    public function title(){
        echo $this->get_title();
    }
    
    /**
     * Return permalink to complete forecast at Yahoo! Weather
     *
     * @return string
     */
    public function get_permalink(){
        return $this->_weather->get_permalink();
    }
    
    /**
     * Print permalink to complete forecast at Yahoo! Weather
     *
     * @return string
     */
    public function permalink(){
        echo $this->get_permalink();
    }

    /**
     * Return sunrise time (eg: 6:57 am)
     *
     * @return string
     */
    public function get_sunrise(){
        return $this->_astronomy[0]['attribs']['']['sunrise'];
    }

    /**
     * Print sunrise time (eg: 6:57 am)
     *
     * @return none
     */
    public function sunrise(){
        echo $this->get_sunrise();
    }
    
    /**
     * Return sunset time (eg: 7:17 pm)
     *
     * @return string
     */
    public function get_sunset(){
        return $this->_astronomy[0]['attribs']['']['sunset'];
    }

    /**
     * Print sunset time (eg: 7:17 pm)
     *
     * @return none
     */
    public function sunset(){
        echo $this->get_sunset();
    }

    /**
     * Return name of city for current forecast
     *
     * @return string
     */
    public function get_city(){
        return $this->_location[0]['attribs']['']['city'];
    }
    
    /**
     * Print name of city for current forecast
     *
     * @return none
     */
    public function city(){
        echo $this->get_city();
    }
    
    /**
     * Return name of state or region for current forecast
     *
     * @return string
     */
    public function get_state(){
        return $this->_location[0]['attribs']['']['region'];
    }

    /**
     * Print name of state or region for current forecast
     *
     * @return none
     */
    public function state(){
        echo $this->get_state();
    }

    /**
     * Return name of country for current forecast
     *
     * @return string
     */
    public function get_country(){
        return $this->_location[0]['attribs']['']['country'];
    }

    /**
     * Print name of country for current forecast
     *
     * @return none
     */
    public function country(){
        echo $this->get_country();
    }

    /**
     * Return current humidity
     *
     * @return string
     */
    public function get_humidity(){
        return $this->_atmosphere[0]['attribs']['']['humidity'];
    }

    /**
     * Print current humidity
     *
     * @return string
     */
    public function humidity(){
        echo $this->get_humidity();
    }
    
    /**
     * Return current visibility
     *
     * @return string
     */
    public function get_visibility(){
        return $this->_atmosphere[0]['attribs']['']['visibility'];
    }

    /**
     * Print current visibility
     *
     * @return string
     */
    public function visibility(){
        echo $this->get_visibility();
    }
    
    /**
     * Return current barometric pressure
     *
     * @return string
     */
    public function get_barometric_pressure(){
        return $this->_atmosphere[0]['attribs']['']['pressure'];
    }

    /**
     * Print current barometric pressure
     *
     * @return string
     */
    public function barometric_pressure(){
        echo $this->get_barometric_pressure();
    }
    
    /**
     * Return current barometric pressure, complete with information about whether it's rising, falling or holding steady
     *
     * @return string
     */
    public function get_barometric_pressure_formatted(){
        $pressure = $this->get_barometric_pressure();
        $rising = $this->get_barometric_pressure_status();
        return $pressure . ' and ' . $rising;
    }
    
    /**
     * Print current barometric pressure, complete with information about whether it's rising, falling or holding steady
     *
     * @return string
     */
    public function barometric_pressure_formatted(){
        echo $this->get_barometric_pressure_formatted();
    }
    
    /**
     * Return whether barometric pressure is rising, falling or holding steady
     *
     * @return string
     */
    public function get_rising(){
        return $this->_atmosphere[0]['attribs']['']['rising'];
    }

    /**
     * Print whether barometric pressure is rising, falling or holding steady
     *
     * @return string
     */
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
    
    public function get_units_temperature(){
        return $this->_units[0]['attribs']['']['temperature'];
    }
    
    public function units_temperature(){
        echo $this->get_units_temperature();
    }
    
    public function get_units_distance(){
        return $this->_units[0]['attribs']['']['distance'];
    }

    public function units_distance(){
        echo $this->get_units_distance();
    }
    
    public function get_units_pressure(){
        return $this->_units[0]['attribs']['']['pressure'];
    }

    public function units_pressure(){
        echo $this->get_units_pressure();
    }
    
    public function get_units_speed(){
        return $this->_units[0]['attribs']['']['speed'];
    }

    public function units_speed(){
        echo $this->get_units_speed();
    }
    
    public function get_wind_chill(){
        return $this->_wind[0]['attribs']['']['chill'];
    }
    
    public function wind_chill(){
        echo $this->get_wind_chill();
    }
    
    public function get_wind_speed(){
        return $this->_wind[0]['attribs']['']['speed'];
    }

    public function wind_speed(){
        echo $this->get_wind_speed();
    }

    public function get_wind_speed_formatted(){
        $speed = $this->get_wind_speed();
        $unit = $this->get_units_speed();
        
        return $speed . ' ' . $unit;
    }
    
    public function wind_speed_formatted(){
        echo $this->get_wind_speed_formatted();
    }
    
    public function get_wind_degrees_formatted(){
        return $this->_wind[0]['attribs']['']['direction'];
    }

    public function wind_degrees(){
        echo $this->get_wind_degrees();
    }
    
    /*
        TODO 
        wind, forecast
    */

}
?>