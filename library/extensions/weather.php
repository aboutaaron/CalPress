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
    private $_weather; // stores full feed
    private $_current_conditions; // stores current conditions in array
    private $_astronomy; // stores sunrise / sunset
    private $_location; // stores city/region/country
    private $_atmosphere; // stores humidity / pressure / visibilty / rising
    private $_units; // store units being used - mi / pressure / speed / distance 
    private $_wind; // store wind info 
    private $_forecast; // store mutliday forecast 
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
            $this->_forecast = $this->_weather->get_item_tags('http://xml.weather.yahoo.com/ns/rss/1.0', 'forecast');
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
     * Return current temperature with the degree sign
     *
     * @return string
     */
    public function get_temperature_formatted(){
        $t = $this->get_temperature();
        return $t .'&#176;';
    }
    
    /**
     * Echo current temperature with the degree sign
     *
     * @return none
     */
    public function temperature_formatted(){
        echo $this->get_temperature_formatted();
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
     * Return current conditions text (cloudy, sunny, etc)
     *
     * @return string
     */
    public function get_current_condition_image(){
         return "http://l.yimg.com/a/i/us/we/52/12.gif";
    }
    
    /**
     * Print current conditions text (cloudy, sunny, etc)
     *
     * @return none
     */
    public function current_condition_image(){
        echo $this->get_current_condition_image();
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
     * @return none
     */
    public function humidity(){
        echo $this->get_humidity();
    }
    
    /**
     * Return current humidity with unit
     *
     * @return string
     */
    public function get_humidity_formatted(){
        $h = $this->get_humidity();
        return $h . '%';
    }

    /**
     * Print current humidity with unit
     *
     * @return none
     */
    public function humidity_formatted(){
        echo $this->get_humidity_formatted();
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
     * @return none
     */
    public function visibility(){
        echo $this->get_visibility();
    }
    
    /**
     * Return current visibility
     *
     * @return string
     */
    public function get_visibility_formatted(){
        $v = $this->get_visibility();
        $u = $this->get_units_distance();
        return $v . ' ' . $u;
    }

    /**
     * Print current visibility
     *
     * @return none
     */
    public function visibility_formatted(){
        echo $this->get_visibility_formatted();
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
        $units = $this->get_units_pressure();
        $rising = $this->get_barometric_pressure_status();
        return $pressure . ' ' . $units . ' and ' . $rising;
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
    
    /**
     * Return textual representation of int-based get_rising() (eg: 1 = "rising")
     *
     * @return string
     */
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
    
    /**
     * Print textual representation of int-based get_rising() (eg: 1 = "rising")
     *
     * @return none
     */
    public function barometric_pressure_status(){
        echo $this->get_barometric_pressure_status();
    }
    
    /**
     * Return temperature unit being used, either C of F
     *
     * @return string
     */
    public function get_units_temperature(){
        return $this->_units[0]['attribs']['']['temperature'];
    }
    
    /**
     * Print temperature unit being used, either C of F
     *
     * @return none
     */
    public function units_temperature(){
        echo $this->get_units_temperature();
    }
    
    /**
     * Return distance unit being used, either m or km
     *
     * @return string
     */
    public function get_units_distance(){
        return $this->_units[0]['attribs']['']['distance'];
    }

    /**
     * Print distance unit being used, either m or km
     *
     * @return none
     */
    public function units_distance(){
        echo $this->get_units_distance();
    }
    
    /**
     * Return barometric pressure unit being used
     *
     * @return string
     */
    public function get_units_pressure(){
        return $this->_units[0]['attribs']['']['pressure'];
    }

    /**
     * Print barometric pressure unit being used
     *
     * @return none
     */
    public function units_pressure(){
        echo $this->get_units_pressure();
    }
    
    /**
     * Return speed unit being used, for wind speed etc
     *
     * @return string
     */
    public function get_units_speed(){
        return $this->_units[0]['attribs']['']['speed'];
    }

    /**
     * Print speed unit being used, for wind speed etc
     *
     * @return none
     */
    public function units_speed(){
        echo $this->get_units_speed();
    }
    
    /**
     * Return wind chill
     *
     * @return string
     */
    public function get_wind_chill(){
        return $this->_wind[0]['attribs']['']['chill'];
    }
    
    /**
     * Print wind chill
     *
     * @return string
     */
    public function wind_chill(){
        echo $this->get_wind_chill();
    }
    
    /**
     * Return wind chill with degree symbol
     *
     * @return string
     */
    public function get_wind_chill_formatted(){
        $wc = $this->get_wind_chill();
        return $wc .'&#176;';
    }
    
    /**
     * Print wind chill with degree symbol
     *
     * @return string
     */
    public function wind_chill_formatted(){
        echo $this->get_wind_chill_formatted();
    }
    
    /**
     * Return wind speed
     *
     * @return string
     */
    public function get_wind_speed(){
        return $this->_wind[0]['attribs']['']['speed'];
    }

    /**
     * Print wind speed
     *
     * @return string
     */
    public function wind_speed(){
        echo $this->get_wind_speed();
    }

    /**
     * Return wind speed, formatted with appropriate unit
     *
     * @return string
     */
    public function get_wind_speed_formatted(){
        $speed = $this->get_wind_speed();
        $unit = $this->get_units_speed();
        
        return $speed . ' ' . $unit;
    }
    
    /**
     * Print wind speed, formatted with appropriate unit
     *
     * @return none
     */
    public function wind_speed_formatted(){
        echo $this->get_wind_speed_formatted();
    }
    
    /**
     * Return wind direction in degrees
     *
     * @return string
     */
    public function get_wind_degrees_formatted(){
        return $this->_wind[0]['attribs']['']['direction'];
    }

    /**
     * Print wind direction in degrees
     *
     * @return none
     */
    public function wind_degrees(){
        echo $this->get_wind_degrees();
    }
    
    public function get_forecast(){
        $forecasts = array();
        
        foreach ($this->_forecast as $forecast) {
            $forecasts[] = new CalPress_Forecast($forecast);
        }
        
        return $forecasts;
    }
    
}

class CalPress_Forecast{
    
    private $_day;
    private $_date; 
    private $_low;
    private $_high;
    private $_code;
    private $_text;
    
    private $_imgsrc;
    private $_imgext;
    
    function __construct($src, $imgsrc='http://l.yimg.com/a/i/us/we/52/', $imgext="gif"){
        $this->_day = $src['attribs']['']['day'];
        $this->_date = $src['attribs']['']['date'];
        $this->_low = $src['attribs']['']['low'];
        $this->_high = $src['attribs']['']['high'];
        $this->_code = $src['attribs']['']['code'];
        $this->_text = $src['attribs']['']['text'];
        
        // source of image graphics for each condition code
        $this->_imgsrc = $imgsrc;
        $this->_imgext = $imgext;
    }
    
    public function set_imgsrc($src){
        $this->_imgsrc = $src;
    }
    
    public function set_imgext($ext){
        $this->_imgext = $ext;
    }
    
    public function get_day(){
        return $this->_day;
    }
    
    public function day(){
        echo $this->get_day();
    }
    
    public function get_date(){
        return $this->_date;
    }

    public function date(){
        echo $this->get_date();
    }
    
    public function get_low(){
        return $this->_low;
    }

    public function low(){
        echo $this->get_low();
    }
    
    public function get_high(){
        return $this->_high;
    }

    public function high(){
        echo $this->get_high();
    }
    
    public function get_code(){
        return $this->_code;
    }

    public function get_high_formatted(){
        $h = $this->get_high();
        return $h .'&#176;';
    }

    public function high_formatted(){
        echo $this->get_high_formatted();
    }

    public function get_low_formatted(){
        $l = $this->get_low();
        return $l .'&#176;';
    }

    public function low_formatted(){
        echo $this->get_low_formatted();
    }

    public function code(){
        echo $this->get_code();
    }
    
    public function get_text(){
        return $this->_text;
    }

    public function text(){
        echo $this->get_text();
    }
    
    public function get_image(){
        $imgdir = $this->_imgsrc;
        $code = $this->get_code();
        $extension = $this->_imgext;
        
        return $imgdir . $code . '.' . $extension;
    }
    
    public function image(){
        echo $this->get_image();
    }
}

?>