<?php
/* Slightly modified version of Easy Relative Date by Levani Melikishvili */

/*
Plugin Name: Easy Relative Date
Plugin URI: http://wp123.info/plugins/easy-relative-date/
Description: Easy Relative Date is a simple plugin to add relative date supports on wordpress powered websites, like 'Today', 'Yesterday', '2 Days Ago', '2 Weeks Ago', '2 'Seconds Ago', '2 Minutes Ago', '2 Hours Ago'.
Author: Levani Melikishvili
Version: 1.0
Author URI: http://wp123.info/
*/

/*
Copyright (C) 2008-2009 Levani Melikishvili / wp123.info (levani9191 AT gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if(!function_exists('easy_relative_date')){
    function calpress_relativetime($timestamp){
        $difference = time() - $timestamp;

        if($difference >= 60*60*24*365){        // if more than a year ago
            $int = intval($difference / (60*60*24*365));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' year' . $s . ' ago';
        } elseif($difference >= 60*60*24*7*5){  // if more than five weeks ago
            $int = intval($difference / (60*60*24*30));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' month' . $s . ' ago';
        } elseif($difference >= 60*60*24*7){        // if more than a week ago
            $int = intval($difference / (60*60*24*7));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' week' . $s . ' ago';
        } elseif($difference >= 60*60*24){      // if more than a day ago
            $int = intval($difference / (60*60*24));
          if ($int == 1) {
            $r = 'Yesterday';
        } else {
            $r = $int . ' days ago';
        }
        } elseif($difference >= 60*60){         // if more than an hour ago
            $int = intval($difference / (60*60));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' hour' . $s . ' ago';
        } elseif($difference >= 60){            // if more than a minute ago
            $int = intval($difference / (60));
            $s = ($int > 1) ? 's' : '';
            $r = $int . ' minute' . $s . ' ago';
        } else {                                // if less than a minute ago
            $r = 'moments ago';
        }

        return $r;
    }
}	
?>
