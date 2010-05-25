<?php
/*
Plugin Name: CalPress Geotag
Plugin URI: http://www.bobsp.de/weblog/geotag/
Description: Modified Geotag plugin by Boris Pulyer. Provides geocoding features for Wordpress.
Version: 1.0
Author: Boris Pulyer / Josh Williams
Author URI: http://www.bobsp.de
Minimum WordPress Version Required: 2.7.0
Tested up to: 2.7.1
*/


/* ==================================================================== */
/* = Hooks, Filters, Globals etc.                                     = */
/* ==================================================================== */

global $geotag_maps, $geotag_options;
$geotag_options = get_option("geotag_options");

//register_activation_hook(__FILE__, array("Geotag", "registerPlugin"));
add_action("admin_menu", array("Geotag", "hookAdminMenu"));
add_action("admin_footer", array("Geotag", "hookAdminFooter"));
add_action("save_post", array("Geotag", "hookSavePost"));
add_action("wp_head", array("Geotag", "hookWPHeader"));
add_action("wp_footer", array("Geotag", "hookWPFooter"));
add_filter("the_content", array("Geotag", "filterTheContent"));
add_shortcode("gmap", array("Geotag", "parseShortcode"));
if ($geotag_options["misc_wpgeocompatibility"]["SHORTCODE"] == "true") {add_shortcode("wp_geo_map", array("Geotag", "parseShortcode"));};
if ($geotag_options["misc"]["GEOTAG_FEEDS"] == "true") {
	add_action("rss2_ns", array("Geotag", "hookFeedNamespace"));
	add_action("atom_ns", array("Geotag", "hookFeedNamespace"));
	add_action("rdf_ns", array("Geotag", "hookFeedNamespace"));
	add_action("rss_item", array("Geotag", "hookFeedItem"));
	add_action("rss2_item", array("Geotag", "hookFeedItem"));
	add_action("atom_entry", array("Geotag", "hookFeedItem"));
	add_action("rdf_item", array("Geotag", "hookFeedItem"));
}


/* ==================================================================== */
/* = Class Geotag                                                     = */
/* ==================================================================== */

class Geotag {
	
	/* ==================================================================== */
	/* = Register the plugin                                              = */
	/* ==================================================================== */
	
	function registerPlugin() {
		$options = array(
			"gmap_api_key" => "", 
			"gmap_display_page" => array("SINGLE" => "true", "PAGE" => "true"), 
			"auto_map" => array("SHOW" => null, "POSITION" => "BOTTOM"), 
			"gmap_type" => "G_HYBRID_MAP", 
			"gmap_controls_zoompan" => "GLargeMapControl3D",
			"gmap_controls_maptype" => array("G_NORMAL_MAP" => "true", "G_SATELLITE_MAP" => "true", "G_HYBRID_MAP" => "true", "G_PHYSICAL_MAP" => "true"),
			"gmap_controls_other" => null,
			"gmap_zoom" => array("LEVEL" => "5", "ZOOMOUT" => "true", "ZOOMIN" => null),
			"gmap_center" => array("MARKERS" => "true", "PHOTOS" => "true", "FILE" => "true"),
			"gmap_width" => "100%",
			"gmap_height" => "300px",
			"geotaged_photos" => array("READ_GEOTAGED_PHOTOS" => null, "ICON" => "CAMERA"),
			"misc" => array("GEOTAG_FEEDS" => "true", "GEOTAG_HTML" => "true", "QUICKGUIDE" => "true"),
			"misc_wpgeocompatibility" => array("DB" => "READ", "SHORTCODE" => "true")
		);
		add_option("geotag_options", $options);
	}
	
	
	/* ==================================================================== */
	/* = Administration                                                   = */
	/* ==================================================================== */
	
	/**
	 * Hooks
	 */
	
	function hookAdminMenu() {
		add_meta_box("geotag", "Geotag Post", array("Geotag", "displayEditPostForm"), "post", "normal", "low");
		add_meta_box("geotag", "Geotag Page", array("Geotag", "displayEditPostForm"), "page", "normal", "low");
		add_options_page("Geotag Configuration", "Geotag", 8, __FILE__, array("Geotag", "displayOptions"));
	}
	
	function hookAdminFooter() {
		global $geotag_options;
		if (empty($geotag_options["gmap_api_key"])) {return;}
		list($lat, $lon) = Geotag::getCoordinates();
		if (is_null($lat) || is_null($lon)) {
			// Default coordinates
			$lat = 37.82171764783966; $lon = -122.24590301513672; $geotag_options["gmap_zoom"]["LEVEL"] = 12;
			$no_position = true;
		}
		echo "
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$geotag_options["gmap_api_key"]."' type='text/javascript'></script>
			<script type='text/javascript'>
				function gmapInit() {
					if (!GBrowserIsCompatible() || !document.getElementById('gmap')) {gmap_init = false; return false;}
					var position = new GLatLng(".$lat.", ".$lon.");
					gmap = new GMap2(document.getElementById('gmap'));
					gmap.enableScrollWheelZoom();
					gmap.enableContinuousZoom();
					gmap.setMapType(".$geotag_options["gmap_type"].");
					gmap.setCenter(position, ".$geotag_options["gmap_zoom"]["LEVEL"].");
					gmap.addMapType(G_PHYSICAL_MAP);
					gmap.addControl(new GLargeMapControl3D);
					gmap.addControl(new GMapTypeControl());
					GEvent.addListener(gmap, 'click', function(overlay, point) {
						document.getElementById('geotag_lat').value = point.lat();
						document.getElementById('geotag_lon').value = point.lng();
						marker.setPoint(point);
						marker.show();
					});
					GEvent.addListener(gmap, 'moveend', function() {
						var center = gmap.getCenter();
						document.getElementById('map_view_lat').innerHTML = center.lat();
						document.getElementById('map_view_lon').innerHTML = center.lng();
						document.getElementById('map_view_zoom').innerHTML = gmap.getZoom();
					});
					GEvent.trigger(gmap, 'moveend');
					marker = new GMarker(position, {draggable: true});
					GEvent.addListener(marker, 'dragend', function() {
						var point = marker.getLatLng();
						document.getElementById('geotag_lat').value = point.lat();
						document.getElementById('geotag_lon').value = point.lng();
					});
					gmap.addOverlay(marker);";
		if (Geotag::getPolygon()) {
		    list($geotag_polygon, $post_id) = Geotag::getPolygon();
		    echo "
		            var polygon = '".$geotag_polygon."';
		            polygon.replace('/\|/g', 'Z');
		            document.getElementById('geotag_polygon').value = polygon;
		            gmapPolygon();";
		}
		if ($no_position) {
			echo "
					marker.hide();";
		}
		echo "
					geocoder = new GClientGeocoder();
					gmap_init = true;
					return true;
				}
				
				function gmapGeocode() {
					var search_string = document.getElementById('geotag_search').value;
					if (geocoder) {
						geocoder.getLatLng(search_string, function(point) {
							if (!point) {
								document.getElementById('geotag_search').style.border ='1px solid #d54e21';
								document.getElementById('geotag_search').style.backgroundColor ='#ffebe8';
								var message = document.createTextNode(' not found!');
								//alert(search_string + ' not found');
							} else {
								document.getElementById('geotag_search').style.border ='1px solid #dfdfdf';
								document.getElementById('geotag_search').style.backgroundColor ='#ffffff';
								document.getElementById('geotag_lat').value = point.lat();
								document.getElementById('geotag_lon').value = point.lng();
								gmap.setCenter(point);
								marker.setPoint(point);
								marker.show();
							}
						});
					}
				}
				
				function clearGeotagFields() {
					document.getElementById('geotag_search').value = '';
					document.getElementById('geotag_lat').value = '';
					document.getElementById('geotag_lon').value = '';
					marker.hide();
				}
				
				var gmap_polygon;
				function gmapPolygon() {
				    var polygon_string = document.getElementById('geotag_polygon').value;
				    var polygon_points = polygon_string.split('|');
				    var georss_string = '';
                    for (var i=0; i<polygon_points.length; ++i) {
                        points = polygon_points[i].split(',');
                        polygon_points[i] = new GLatLng(points[0], points[1]);
                        georss_string = georss_string + points[0] + ' ' + points[1] + ' ';
                    }
                    georss_string = jQuery.trim(georss_string);
                    document.getElementById('map_polygon_georss').innerHTML = georss_string;
				    gmap_polygon = new GPolygon(polygon_points, '#f33f00', 5, 1, '#ff0000', 0.2);
				    gmap.addOverlay(gmap_polygon);
				}
				
				function clearGmapPolygon() {
				    document.getElementById('geotag_polygon').value = '';
				    document.getElementById('map_polygon_georss').innerHTML = '';
				    gmap.removeOverlay(gmap_polygon);
				}
				
				gmapInit();
			</script>";
	}
	
	function hookSavePost($post_id) {
		if (isset($_POST["geotag_lat"]) && isset($_POST["geotag_lon"])) {
			Geotag::putCoordinates($_POST["geotag_lat"], $_POST["geotag_lon"], $post_id);
			if (isset($_POST["geotag_polygon"])) {
			   Geotag::putPolygon($_POST["geotag_polygon"], $post_id);
			}
		}
	}
	
	/**
	 * Create HTML to display
	 */
	
	function displayEditPostForm() {
		global $geotag_options;
		list($lat, $lon) = Geotag::getCoordinates();
		echo "
			<table cellspacing='2' cellpadding='2' class='form-table'>
				<tr style='border-bottom: 1px solid #CCC;'>
					<th style='width: 140px;'>Search for an address</th>
					<td><input name='geotag_search' id='geotag_search' type='text' value='$search' style='width: 300px; margin-right: 10px;' />
						<input type='button' id='geotag_search_button' name='geotag_search_button' value='Search' onclick='gmapGeocode();' class='button' /></td>
				</tr>
				
				<tr>
					<th style='width: 140px;'>Type of map</th>
					<td>
					    <div style='width: 290px; float: left; margin-top: 5px;'>
					        <input name='geotag_type' id='geotag_type_inline' type='radio' value='inline' checked=\"checked\" /><span style='margin-left: 4px;'>Inline</span>
    						<input name='geotag_type' id='geotag_type_lead' type='radio' value='lead' /><span style='margin-left: 4px;'>Lead</span>
						</div>
					</td>
				</tr>
				
				<tr>
					<th style='width: 140px;'>Latitude, Longitude</th>
					<td>
					    <div style='width: 290px; float: left; margin-top: 5px;'>
					        <input name='geotag_lat' id='geotag_lat' type='text' value='$lat' style='width: 200px; margin-right: 10px;' /><br />
    						<input name='geotag_lon' id='geotag_lon' type='text' value='$lon' style='width: 200px; margin-right: 10px;' />
    						<input type='button' id='geotag_search_button' name='geotag_search_button' value='Clear' onclick='clearGeotagFields();' class='button' />
						</div>
						<div style='width: 400px; float: left; border-left: 1px solid #CCC;'>
						    <p> The latitude and longitude listed in these boxes are used to draw the map in your post. Clicking the map updates these values. Simply panning the map does not, but it does update the information below, which is only for your reference.</p>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<div id='gmap' style='height:300px; width:100%; padding:0px; margin:0px;'></div>
					</td>
				</tr>
				<tr>
					<td style='vertical-align: top;'>Current map details</td>
					<td>
						<table>
							<tr>
								<td style='width: 210px; padding: 0px;'>Latitude: <div id='map_view_lat' style='background-color: #ccc;'></div></td>
								<td style='width: 210px; padding: 0px;'>Longitude:<div id='map_view_lon' style='background-color: #ccc;'></div></td>
								<td style='width: 210px; padding: 0px;'>Zoomlevel: <div id='map_view_zoom' style='background-color: #ccc;'></div></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr style='border-top: 1px solid #CCC;'>
					<td colspan='2' style='margin-bottom: 0px; padding-bottom: 2px;'>
						<h4 style='margin-bottom: -5px; margin-left: 0px;'>Additional map information</h4>
					</td>
				</tr>
				<tr>
					<th style='width: 140px;'>Optional Polygon <br /> (pipe delimited points)</th>
					<td>
					    <input name='geotag_polygon' id='geotag_polygon' type='text' value='$polygon' style='width: 300px; margin-right: 10px;' />
					    <input type='button' id='geotag_polygon_button' name='geotag_polygon_button' value='Show' onclick='gmapPolygon();' class='button' />
					    <input type='button' id='geotag_polygon_clear_button' name='geotag_polygon_clear_button' value='Clear' onclick='clearGmapPolygon();' class='button' /> <br />
					    <span style='margin-left: 5px;'>GeoRSS Format: <span id='map_polygon_georss' style='min-width: 150px; height: 15px; background-color: #ccc; margin-left: 30px;'> </span></span>
					</td>
				</tr>
			</table>";
		if ($geotag_options["misc"]["QUICKGUIDE"] == "true") {
			echo "
			<h4>Quick Guide</h4>
			<p>Add <code>[gmap]</code> into your post where the map should be displayed. If you activated Auto Maps, the first [gmap]
				is to configure this map. See the documentation at the configuration page for details.</p>
			<p>Possible properties for the [gmap] shortcode:</p>
			<table>
			<tr>
				<td style='padding: 10px;'><code>width='...'</code><br /><code>height='...'</code></td>
				<td style='padding: 10px;'>Add the unit <em>px</em> or <em>%</em> to the value.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>type='...'</code></td>
				<td style='padding: 10px;'>Possible values are <code>G_NORMAL_MAP</code>, <code>G_SATELLITE_MAP</code>, <code>G_HYBRID_MAP</code>, 
					<code>G_PHYSICAL_MAP</code> or <code>G_STATIC_MAP</code>.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>zoom='...'</code></td>
				<td style='padding: 10px;'>Values are from <code>0</code> (zoomed out) to <code>19</code> (zoomed in).</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>lat='...' lon='...'</code></td>
				<td style='padding: 10px;'>Adds the coordinates to the post.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>center_lat='...'<br />center_lon='...'</code></td>
				<td style='padding: 10px;'>Changes the center of the map.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>marker_lat='...'<br />marker_lon='...'</code></td>
				<td style='padding: 10px;'>Displays a marker at the given position.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>marker_query='...'</code></td>
				<td style='padding: 10px;'>Look at the <a href='http://codex.wordpress.org/Template_Tags/get_posts'>Wordpress Codex</a> for the query parameters.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>file='...'</code></td>
				<td style='padding: 10px;'>A KML or KMZ file to display. Use <code>__UPLOAD__</code> for your default upload location.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>display_photos='...'</code></td>
				<td style='padding: 10px;'>Values may be <code>true</code> or <code>false</code>.</td>
			</tr>
			<tr>
				<td style='padding: 10px;'><code>photos_icon='...'</code></td>
				<td style='padding: 10px;'>Possible values are <code>DEFAULT</code>, <code>CAMERA</code> (a camera icon) or <code>THUMBNAIL</code>.</td>
			</tr>
			</table>
			<p>You may disable this list at the configuration page.</p>";
		}
	}
	
	function displayOptions() {
		global $geotag_options;
		echo "
			<div class='wrap'>
				<h2>Geotag Configuration</h2>
				<h3>Documentation</h3>
				<p>If you want to learn more about how to use this plugin, take a look at the <a href='?page=geotag/documentation.php'>documentation</a>. You may also check out the <a href='http://www.bobsp.de/weblog/geotag'><em>Geotag</em> website</a>.</p>
				<h3 style='margin-top: 3em;'>General Options</h3>
				<form method='post' action='options.php'>";
				wp_nonce_field("update-options");
		echo "
				<table class='form-table'>
					<tr valign='top'>
						<th scope='row'>Google API Key</th>
						<td><input name='geotag_options[gmap_api_key]' type='text' value='".$geotag_options["gmap_api_key"]."' size='50' ";
		if (empty($geotag_options["gmap_api_key"])) {echo "style='border: 1px solid #d54e21; background-color: #ffebe8;'";}
		echo " /><br />
							You need a unique Google API Key for your website to display Google Maps. If you don't have one yet, <a href='http://code.google.com/apis/maps/signup.html' target='_blank'>sign up</a> to get one!</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Show Map</th>
						<td>Show maps only on these pages:<br />".Geotag::displayOptions_Checkbox("geotag_options[gmap_display_page]", array("HOME" => "Home", "SINGLE" => "Single posts", "PAGE" => "Pages", "DATE" => "Date archives", "CATEGORY" => "Category Archives"), $geotag_options["gmap_display_page"])."</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Auto Map</th>
						<td>".Geotag::displayOptions_Checkbox("geotag_options[auto_map]", array("SHOW" => "Automatically show a map..."), $geotag_options["auto_map"])."
							".Geotag::displayOptions_Select("geotag_options[auto_map][POSITION]", array("TOP" => "at the top of every post", "BOTTOM" => "at the bottom of every post"), $geotag_options["auto_map"]["POSITION"])."</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Add Geotags</th>
						<td>".Geotag::displayOptions_Checkbox("geotag_options[misc]", array("GEOTAG_FEEDS" => "Add geographical information to feeds"), $geotag_options["misc"])."
							".Geotag::displayOptions_Checkbox("geotag_options[misc]", array("GEOTAG_HTML" => "Add geographical information to HTML Header"), $geotag_options["misc"])."</td>
					</tr>
				</table>
				<h3 style='margin-top: 3em;'>Default Map Appearance</h3>
				<p>Most of these settings can be overwritten in every post. See the <a href='?page=geotag/documentation.php'>documentation</a> for details.</p>
				<table class='form-table'>
					<tr valign='top'>
						<th scope='row'>Map Type</th>
						<td>".Geotag::displayOptions_Select("geotag_options[gmap_type]", array("G_NORMAL_MAP" => "Normal", "G_SATELLITE_MAP" => "Satellite", "G_HYBRID_MAP" => "Hybrid", "G_PHYSICAL_MAP" => "Physical (terrain information)", "G_STATIC_MAP" => "Static map (no gadgets but fast)"), $geotag_options["gmap_type"])."</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Map Controls</th>
						<td><strong>Zoom/Pan Controls</strong><br />".Geotag::displayOptions_Select("geotag_options[gmap_controls_zoompan]", array("GLargeMapControl3D" => "Large zoom/pan controls (new style)", "GLargeMapControl" => "Large zoom/pan controls", "GSmallMapControl" => "Small zoom/pan controls", "GSmallZoomControl3D " => "Small zoom control (new style)", "GSmallZoomControl" => "Small zoom control", "" => "No controls"), $geotag_options["gmap_controls_zoompan"])."<br />&nbsp;<br />
							<strong>Map Type Controls</strong><br />".Geotag::displayOptions_Checkbox("geotag_options[gmap_controls_maptype]", array("G_NORMAL_MAP" => "Normal", "G_SATELLITE_MAP" => "Satellite", "G_HYBRID_MAP" => "Hybrid", "G_PHYSICAL_MAP" => "Physical (terrain information)"), $geotag_options["gmap_controls_maptype"])."<br />
							<strong>Other Map Controls</strong><br />".Geotag::displayOptions_Checkbox("geotag_options[gmap_controls_other]", array("GScaleControl" => "Show map scale", "GOverviewMapControl" => "Show small overview map"), $geotag_options["gmap_controls_other"])."</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Map Center</th>
						<td>Automatically center the map on the following items:<br />
							".Geotag::displayOptions_Checkbox("geotag_options[gmap_center]", array("MARKERS" => "Markers", "PHOTOS" => "Geotagged photos", "FILE" => "KML/KMZ files"), $geotag_options["gmap_center"])."</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Map Zoom</th>
						<td>Default zoom level: ".Geotag::displayOptions_Select("geotag_options[gmap_zoom][LEVEL]", array("0" => "0 - Zoomed out", "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10", "11" => "11", "12" => "12", "13" => "13", "14" => "14", "15" => "15", "16" => "16", "17" => "17", "18" => "18", "19" => "19 - Zoomed In"), $geotag_options["gmap_zoom"]["LEVEL"])."<br />
							If the map is centered on multiple items (see above)...<br />
							".Geotag::displayOptions_Checkbox("geotag_options[gmap_zoom]", array("ZOOMOUT" => "automatically zoom out, if the default zoomlevel is too close", "ZOOMIN" => "automatically zoom in to display all items completly filling the map"), $geotag_options["gmap_zoom"])."</td>
							
					</tr>
					<tr valign='top'>
						<th scope='row'>Map Width</th>
						<td><input name='geotag_options[gmap_width]' type='text' value='".$geotag_options["gmap_width"]."' size='10' /> Please add <em>%</em> or <em>px</em></td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Map Height</th>
						<td><input name='geotag_options[gmap_height]' type='text' value='".$geotag_options["gmap_height"]."' size='10' /> Please add <em>%</em> or <em>px</em></td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Geotaged Photos</th>
						<td>".Geotag::displayOptions_Checkbox("geotag_options[geotaged_photos]", array("READ_GEOTAGED_PHOTOS" => "Try to read the geotags from every photo of the post and display an icon on the map"), $geotag_options["geotaged_photos"])."
							Icon style: ".Geotag::displayOptions_Select("geotag_options[geotaged_photos][ICON]", array("DEFAULT" => "Standard icon", "CAMERA" => "Camera icon", "THUMBNAIL" => "Thumbnail of the photos"), $geotag_options["geotaged_photos"]["ICON"])."</td>
					</tr>
				</table>
				<h3 style='margin-top: 3em;'>Miscellaneous</h3>
				<table class='form-table'>
					<tr valign='top'>
						<th scope='row'><em>WP Geo</em> Compatibility</th>
						<td><strong>Database</strong><br />".Geotag::displayOptions_Radio("geotag_options[misc_wpgeocompatibility][DB]", array("NULL" => "No compatibility - <em>WP Geo</em> coordinates will be ignored", "READ" => "Read compatibility - read the <em>WP Geo</em> coordinates only if no <em>Geotag</em> coordinates were saved", "WRITE" => "Read and write compatibility - read the <em>WP Geo</em> coordinates and save new coordinates in the <em>WP Geo</em> database field"), $geotag_options["misc_wpgeocompatibility"]["DB"])."<br />
							<strong>Shortcode</strong><br />".Geotag::displayOptions_Checkbox("geotag_options[misc_wpgeocompatibility]", array("SHORTCODE" => "Process the <em>WP Geo</em> Shortcode <code>[wp_geo_map]</code> in posts"), $geotag_options["misc_wpgeocompatibility"])."</td>
					</tr>
					<tr valign='top'>
						<th scope='row'>Quick Guide</th>
						<td>".Geotag::displayOptions_Checkbox("geotag_options[misc]", array("QUICKGUIDE" => "Add a short documentation to the post writing page."), $geotag_options["misc"])."</td>
					</tr>
				</table>
				<p class='submit'>
					<input type='submit' name='submit' value='Save Changes' class='button-primary' />
					<input type='hidden' name='action' value='update' />
					<input type='hidden' name='page_options' value='geotag_options' />
				</p>
				</form>
			</div>";
			//echo "<pre>"; print_r($geotag_options); echo "</pre>";
	}
	
	function displayOptions_Select($name, $options, $selected=null) {
		$output = "<select name='".$name."' style='width: 270px;'>";
		foreach ($options as $key => $val) {
			$output .= "<option value='".$key."'";
			if ($selected == $key) {$output .= " selected='selected'";}
			$output .= ">".$val."</option>";
		}
		$output .= "</select>";
		return $output;
	}
	
	function displayOptions_Checkbox($name, $options, $selected=null) {
		$output = null;
		foreach ($options as $key => $val) {
			$output .= "<input name='".$name."[".$key."]' type='checkbox' value='true' style='margin-right: 5px;'";
			if ($selected[$key] == "true") {$output .= " checked='checked'";}
			$output .= " />".$val."<br />";
		}
		return $output;
	}
	
	function displayOptions_Radio($name, $options, $selected=null) {
		foreach ($options as $key => $val) {
			$output .= "<input name='".$name."' type='radio' value='".$key."'";
			if ($selected == $key) {$output .= " checked='checked'";}
			$output .= ">".$val."<br />";
		}
		return $output;
	}
	
	
	/* ==================================================================== */
	/* = Display the posts                                                = */
	/* ==================================================================== */
	
	/**
	 * Hooks, Shortcode, Filter
	 */
	
	function filterTheContent($content="") {
		if (!Geotag::getDisplayMap()) {return $content;}
		global $geotag_options, $geotag_maps, $post;
		if (!is_null(Geotag::getCoordinates()) && $geotag_options["auto_map"]["SHOW"] == "true") {
			// Load the settings
			$post_id = $post->ID;
			list($lat, $lon) = Geotag::getCoordinates();
			$default_atts = array(
				"width" => $geotag_options["gmap_width"],
				"height" => $geotag_options["gmap_height"],
				"type" => $geotag_options["gmap_type"],
				"zoom" => $geotag_options["gmap_zoom"]["LEVEL"],
				"zoomout" => $geotag_options["gmap_zoom"]["ZOOMOUT"],
				"zoomin" => $geotag_options["gmap_zoom"]["ZOOMIN"],
				"center_markers" => $geotag_options["gmap_center"]["MARKERS"],
				"center_photos" => $geotag_options["gmap_center"]["PHOTOS"],
				"center_file" => $geotag_options["gmap_center"]["FILE"],
				"marker" => array(0 => array("lat" => $lat, "lon" => $lon)),
				"photos" => "",
			);
			if ($geotag_options["geotaged_photos"]["READ_GEOTAGED_PHOTOS"] == "true") {
				$default_atts["photos"] = Geotag::getGeotagsFromPhotos();
				$default_atts["photos_icon"] = $geotag_options["geotaged_photos"]["ICON"];
			}
			// Add the Google Map container
			switch ($geotag_options["auto_map"]["POSITION"]) {
			case "TOP":
				if ($geotag_options["gmap_type"] == "G_STATIC_MAP") {
					$content = Geotag::getDisplayStaticMap().$content;
				} else {
					$content = "<div id='gmap_".$post_id."_automap' class='gmap' style='width:".$geotag_options["gmap_width"]."; height:".$geotag_options["gmap_height"].";'></div>".$content;
					$geotag_maps[$post_id]["automap"] = $default_atts;
				}
			break;
			case "BOTTOM":
				if ($geotag_options["gmap_type"] == "G_STATIC_MAP") {
					$content = $content.Geotag::getDisplayStaticMap();
				} else {
					$content = $content."<div id='gmap_".$post_id."_automap' class='gmap' style='width:".$geotag_options["gmap_width"]."; height:".$geotag_options["gmap_height"].";'></div>";
					$geotag_maps[$post_id]["automap"] = $default_atts;
				}
			break;
			}
		}
		return $content;
	}
	
	function parseShortcode ($atts, $content = null) {
		if (!Geotag::getDisplayMap()) {return $content;}
		global $geotag_options, $geotag_maps, $post;
		$post_id = $post->ID;
		$default_atts = array(
			"width" => $geotag_options["gmap_width"],
			"height" => $geotag_options["gmap_height"],
			"lat" => false,
			"lon" => false,
			"file" => false,
			"type" => $geotag_options["gmap_type"],
			"zoom" => false,
			"center_lat" => false,
			"center_lon" => false,
			"center" => false,
			"marker_lat" => false,
			"marker_lon" => false,
			"marker_query" => false,
			"display_photos" => $geotag_options["geotaged_photos"]["READ_GEOTAGED_PHOTOS"],
			"photos_icon" => $geotag_options["geotaged_photos"]["ICON"]
		);
		$atts = shortcode_atts($default_atts, $atts);
		// Store the given coordinates to the database if no coordinates were stored before
		if (!empty($atts["lat"]) && !empty($atts["lon"]) && is_null(Geotag::getCoordinates())) {
			Geotag::putCoordinates($atts["lat"], $atts["lon"], $post_id);
		}
		unset($atts["lat"], $atts["lon"]);
		// Handle the zoom level
		if (strtolower($atts["zoom"]) == "auto") {
			// Zoom level shall be set automatically
			$atts["zoom"] = $geotag_options["gmap_zoom"]["LEVEL"];
			$atts["zoomout"] = "true";
			$atts["zoomin"] = "true";
		} elseif (!$atts["zoom"]) {
			// No zoom level was set manually, go with the default values
			$atts["zoom"] = $geotag_options["gmap_zoom"]["LEVEL"];
			$atts["zoomout"] = $geotag_options["gmap_zoom"]["ZOOMOUT"];
			$atts["zoomin"] = $geotag_options["gmap_zoom"]["ZOOMIN"];
		}
		// Handle the center
		if (empty($atts["center_lat"]) && empty($atts["center_lon"])) {
			// No center attributes were given
			if (empty($atts["center"])) {
				if ($geotag_options["gmap_center"]["MARKERS"] == "true") {$atts["center_markers"] = "true";}
				if ($geotag_options["gmap_center"]["PHOTOS"] == "true") {$atts["center_photos"] = "true";}
				if ($geotag_options["gmap_center"]["FILE"] == "true") {$atts["center_file"] = "true";}
			} else {
				$center = explode(",", $atts["center"]);
				foreach ($center as $val) {
					switch ($val) {
					case "markers":
					case "marker":
						$atts["center_markers"] = "true";
					break;
					case "photos":
					case "photo":
						$atts["center_photos"] = "true";
					break;
					case "files":
					case "file":
						$atts["center_file"] = "true";
					break;
					}
				}
			}
			unset($atts["center_lat"], $atts["center_lon"]);
		} else {
			// Center attributes were given
			list($atts["center_lat"], $atts["center_lon"]) = Geotag::getConvertedCoordinates($atts["center_lat"], $atts["center_lon"]);
		}
		unset($atts["center"]);
		// Create the markers
		if (!empty($atts["marker_query"])) {
			$posts = get_posts(str_replace("&#038;", "&", $atts["marker_query"]));
			foreach ($posts as $val) {
				list($lat, $lon) = Geotag::getCoordinates($val->ID);
				if (!empty($lat) && !empty($lon)) {$marker[] = array("lat" => $lat, "lon" => $lon, "uri" => $val->guid, "title" => $val->post_title, "date" => date(get_option("date_format"), strtotime($val->post_date)));}
			}
		} else {
			if (empty($atts["marker_lat"]) && empty($atts["marker_lon"])) {
				// No marker attributes were given
				list($lat, $lon) = Geotag::getCoordinates();
			} else {
				// Marker attributes were given
				list($lat, $lon) = Geotag::getConvertedCoordinates($atts["marker_lat"], $atts["marker_lon"]);
			}
			if (!empty($lat) && !empty($lon)) {$marker[] = array("lat" => $lat, "lon" => $lon);}
		}
		unset($atts["marker_lat"], $atts["marker_lon"], $atts["marker_query"]);
		// Create the photos
		if ($atts["display_photos"] == "true") {
			$photos = Geotag::getGeotagsFromPhotos();
		} else {
			unset($atts["photos_icon"]);
		}
		unset($atts["display_photos"]);
		// Add a KML or KMZ file
		if (!empty($atts["file"])) {
			$upload_url_path = get_option("upload_url_path");
			if (!empty($upload_url_path)) {
				$atts["file"] = str_replace("__UPLOAD__", $upload_url_path, $atts["file"]);
			} else {
				$atts["file"] = str_replace("__UPLOAD__", get_option("siteurl")."/".get_option("upload_path"), $atts["file"]);
			}
		} else {
			unset($atts["file"]);
		}
		// Get the changes or display a map
		$geotag_maps[$post_id]["count"]++;
		if ($geotag_options["auto_map"]["SHOW"] == "true" && $geotag_maps[$post_id]["count"] == 1) {
			if ($geotag_options["gmap_type"] != "G_STATIC_MAP") {
				// Get the changes for the top/bottom map if the map type is not static
				$geotag_maps[$post_id]["automap"] = $atts;
				$geotag_maps[$post_id]["automap"]["marker"] = $marker;
				$geotag_maps[$post_id]["automap"]["photos"] = $photos;
			}
			return null;
		} else {
			// Display a map
			$geotag_maps[$post_id][$geotag_maps[$post_id]["count"]] = $atts;
			$geotag_maps[$post_id][$geotag_maps[$post_id]["count"]]["marker"] = $marker;
			$geotag_maps[$post_id][$geotag_maps[$post_id]["count"]]["photos"] = $photos;
			switch ($atts["type"]) {
			case "G_STATIC_MAP":
				$output = Geotag::getDisplayStaticMap($atts);
			break;
			default:
				$output = "<div id='gmap_".$post_id."_".$geotag_maps[$post_id]["count"]."' class='gmap' style='width:".$atts["width"]."; height:".$atts["height"].";'>".$content."</div>";
			break;
			}
			return $output;
		}
	}
	
	function hookWPFooter () {
		global $geotag_options, $geotag_maps, $posts, $post;
		
		// Add Stamen Flash Vars before standard map
		if ( get_post_custom_values('stamen_map') ) {
		    echo "
    		    <!-- my stamen flash vars here -->
    		";
		}
		
		if (!Geotag::getDisplayMap() || empty($geotag_maps)) {return;}
		
		// Convert the $geotag_maps array to JSON
		// PHP 5.2 has a json_encode function
		$js = array();
		foreach ($geotag_maps as $post_id => $maps) {
			foreach ($maps as $map_id => $map) {
				if ($map_id == "count") {continue;}
				$map["post_id"] = $post_id;
				$map["map_id"] = $map_id;
				$js[] = "{".implode(", ", Geotag::getJSON($map))."}";
			}
		}
		$js = implode(",\n", $js);
		// Create the JS code to init the Google Map
		echo "
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$geotag_options["gmap_api_key"]."' type='text/javascript'></script>
			<script type='text/javascript'>
				function gmapInit() {
					if (!GBrowserIsCompatible()) {return false;}
					var maps = [$js];
					gmap = new Array();
					for (var i=0; i<maps.length; i++) {gmap[maps[i].post_id] = new Array();}
					for (var i=0; i<maps.length; i++) {
						gmap[maps[i].post_id][maps[i].map_id] = new GMap2(document.getElementById('gmap_'+maps[i].post_id+'_'+maps[i].map_id));
						gmap[maps[i].post_id][maps[i].map_id].enableScrollWheelZoom();
						gmap[maps[i].post_id][maps[i].map_id].enableContinuousZoom();
						maps[i].bounds = new GLatLngBounds();
						var getMarker = function (markersSrc) {
							var marker = new GMarker(new GLatLng(markersSrc.lat, markersSrc.lon));
							if (typeof markersSrc.uri != 'undefined') {
								GEvent.addListener(marker, 'click', function() {
									marker.openInfoWindowHtml('<div class=\'gmap_infowindow\'><p class=\'headline\'><a href='+markersSrc.uri+'>'+markersSrc.title+'</a></p><p class=\'date\'>'+markersSrc.date+'</p></div>');
								});
							}
							return marker;
						}
						var getMarkerPhoto = function (markersSrc, marker_icon) {
							if (marker_icon == 'THUMBNAIL') {markerPhotoIcon.image = markersSrc.uri;}
							var marker = new GMarker(new GLatLng(markersSrc.lat, markersSrc.lon), markerPhotoOptions);
							if (typeof markersSrc.uri != 'undefined') {
								GEvent.addListener(marker, 'click', function() {
									marker.openInfoWindowHtml('<div class=\'gmap_infowindow\'><img src=\''+markersSrc.uri+'\' height=\'133\' /></div>');
								});
							}
							return marker;
						}
						var setZoomCenter = function (map) {
							if ((map.zoomin && (gmap[map.post_id][map.map_id].getBoundsZoomLevel(map.bounds) > map.zoom)) || (map.zoomout && (gmap[map.post_id][map.map_id].getBoundsZoomLevel(map.bounds) < map.zoom))) {
								gmap[map.post_id][map.map_id].setCenter(map.bounds.getCenter(), gmap[map.post_id][map.map_id].getBoundsZoomLevel(map.bounds));
							} else {
								gmap[map.post_id][map.map_id].setCenter(map.bounds.getCenter(), map.zoom);
							}
						}
						// Load markers
						for (var j=0; j<maps[i].marker.length; j++) {
							gmap[maps[i].post_id][maps[i].map_id].addOverlay(getMarker(maps[i].marker[j]));
							if (maps[i].center_markers) {maps[i].bounds.extend(new GLatLng(maps[i].marker[j].lat, maps[i].marker[j].lon));}
						}
						// Load photos
						switch (maps[i].photos_icon) {
							case 'CAMERA':
								markerPhotoIcon = new GIcon();
								markerPhotoIcon.iconSize = new GSize(32, 32);
								markerPhotoIcon.shadowSize = new GSize(59, 32);
								markerPhotoIcon.iconAnchor = new GPoint(16, 16);
								markerPhotoIcon.infoWindowAnchor = new GPoint(16, 10);
								markerPhotoIcon.infoShadowAnchor = new GPoint(16, 10);
								markerPhotoIcon.image = 'http://maps.google.com/mapfiles/kml/pal4/icon46.png';
								markerPhotoIcon.shadow = 'http://maps.google.com/mapfiles/kml/pal4/icon46s.png';
								markerPhotoOptions = {icon:markerPhotoIcon};
							break;
							case 'THUMBNAIL':
								markerPhotoIcon = new GIcon();
								markerPhotoIcon.iconSize = new GSize(40, 40);
								markerPhotoIcon.iconAnchor = new GPoint(20, 20);
								markerPhotoIcon.infoWindowAnchor = new GPoint(20, 10);
								markerPhotoOptions = {icon:markerPhotoIcon};
							break;
							default:
								markerPhotoOptions = '';
							break;
						}
						for (var j=0; j<maps[i].photos.length; j++) {
							gmap[maps[i].post_id][maps[i].map_id].addOverlay(getMarkerPhoto(maps[i].photos[j], maps[i].photos_icon));
							if (maps[i].center_photos) {maps[i].bounds.extend(new GLatLng(maps[i].photos[j].lat, maps[i].photos[j].lon));}
						}
						// Load KML file
						if (typeof maps[i].file != 'undefined') {
							geoXml = new GGeoXml(maps[i].file);
							geoXml.map = maps[i];
							gmap[maps[i].post_id][maps[i].map_id].addOverlay(geoXml);
							if (maps[i].center_file) {
								GEvent.addListener(geoXml,'load', function() {
									var default_bounds = this.getDefaultBounds();
									this.map.bounds.extend(default_bounds.getSouthWest());
									this.map.bounds.extend(default_bounds.getNorthEast());
									setZoomCenter(this.map);
								});
							}
						}
						setZoomCenter(maps[i]);";
		if (!empty($geotag_options["gmap_controls_zoompan"])) {
			// Display a Zoom/Pan Map Control
			echo "
						gmap[maps[i].post_id][maps[i].map_id].addControl(new ".$geotag_options["gmap_controls_zoompan"].");";
		}
		if (count($geotag_options["gmap_controls_maptype"]) > 1) {
			// Display at least two different Map Type Controls
			if ($geotag_options["gmap_controls_maptype"]["G_NORMAL_MAP"] != "true") {
				echo "
						gmap[maps[i].post_id][maps[i].map_id].removeMapType(G_NORMAL_MAP);";
			}
			if ($geotag_options["gmap_controls_maptype"]["G_SATELLITE_MAP"] != "true") {
				echo "
						gmap[maps[i].post_id][maps[i].map_id].removeMapType(G_SATELLITE_MAP);";
			}
			if ($geotag_options["gmap_controls_maptype"]["G_HYBRID_MAP"] != "true") {
				echo "
						gmap[maps[i].post_id][maps[i].map_id].removeMapType(G_HYBRID_MAP);";
			}
			if ($geotag_options["gmap_controls_maptype"]["G_PHYSICAL_MAP"] == "true") {
				echo "
						gmap[maps[i].post_id][maps[i].map_id].addMapType(G_PHYSICAL_MAP);";
			}
			if ($geotag_options["gmap_controls_maptype"]["G_SATELLITE_MAP"] == "true" && $geotag_options["gmap_controls_maptype"]["G_HYBRID_MAP"] == "true") {
				echo "
						var mapControl = new GHierarchicalMapTypeControl();
						mapControl.clearRelationships();
						mapControl.addRelationship(G_SATELLITE_MAP, G_HYBRID_MAP, 'Labels', true);
						gmap[maps[i].post_id][maps[i].map_id].addControl(mapControl);";
			} else {
				echo "
						gmap[maps[i].post_id][maps[i].map_id].addControl(new GMapTypeControl());";
			}
		}
		if ($geotag_options["gmap_controls_other"]["GScaleControl"] == "true") {
			// Add GScaleControl
			echo "
						gmap[maps[i].post_id][maps[i].map_id].addControl(new GScaleControl());";
		}
		if ($geotag_options["gmap_controls_other"]["GOverviewMapControl"] == "true") {
			// Add GOverviewMapControl
			echo "
						gmap[maps[i].post_id][maps[i].map_id].addControl(new GOverviewMapControl());";
		}
		echo "
						gmap[maps[i].post_id][maps[i].map_id].setMapType(maps[i].type);
					}
					return true;
				}
				gmapInit();
			</script>
			<!-- This page uses Geotag by Boris Pulyer to provide geocoding features for Wordpress - see http://www.bobsp.de/weblog/geotag/ for details -->";
	}
	
	function hookWPHeader() {
		global $geotag_options;
		if ($geotag_options["misc"]["GEOTAG_HTML"] == "true") {
			list($lat, $lon) = Geotag::getCoordinates();
			if (!is_null($lat) && !is_null($lon)) {
				echo "
					<meta name='geo.position' content='$lat;$lon' />
					<meta name='ICBM' content='$lat, $lon' />";
			}
		}
	}
	
	/**
	 * Other
	 */
	
	function getDisplayStaticMap($atts=array()) {
		global $geotag_options;
		extract($atts);
		if (empty($width)) {$width = $geotag_options["gmap_width"];}
		if (empty($height)) {$height = $geotag_options["gmap_height"];}
		if (empty($zoom)) {$zoom = $geotag_options["gmap_zoom"];}
		if (empty($center_lat) || empty($center_lon)) {list($center_lat, $center_lon) = Geotag::getCoordinates();}
		if (empty($marker_lat) || empty($marker_lon)) {list($marker_lat, $marker_lon) = Geotag::getCoordinates();}
		$parameter[] = "key=".$geotag_options["gmap_api_key"];
		$parameter[] = "size=".rtrim($width, "%px")."x".rtrim($height, "%px");
		$parameter[] = "zoom=".$zoom;
		$parameter[] = "center=".$center_lat.",".$center_lon;
		$parameter[] = "markers=".$marker_lat.",".$marker_lon;
		$parameter = implode("&", $parameter);
		return "<img class='gmap' src='http://maps.google.com/staticmap?".$parameter."' titel='' />";
	}
	
	function getGeotagsFromPhotos() {
		global $post;
		$geotags = array();
		preg_match_all("/<img[^>]+src=['\"]*([^'\" ]*)['\" ][^>]*>/i", $post->post_content, $result); 
		foreach ($result[1] as $image_uri) {
			$image_abs = $_SERVER["DOCUMENT_ROOT"].parse_url($image_uri, PHP_URL_PATH);
			$exif = exif_read_data($image_abs, 0, true);
			if (!empty($exif["GPS"])) {
				$lat_sec = explode("/", $exif["GPS"]["GPSLatitude"][2]);
				$lon_sec = explode("/", $exif["GPS"]["GPSLongitude"][2]);
				$lat = intval($exif["GPS"]["GPSLatitude"][0]) + ((intval($exif["GPS"]["GPSLatitude"][1]) + ($lat_sec[0] / $lat_sec[1] / 60)) / 60);
				$lon = intval($exif["GPS"]["GPSLongitude"][0]) + ((intval($exif["GPS"]["GPSLongitude"][1]) + ($lon_sec[0] / $lon_sec[1] / 60)) / 60);
				if($exif["GPS"]["GPSLatitudeRef"] == "S") {$lat = -$lat;}
				if($exif["GPS"]["GPSLongitudeRef"] == "W") {$lon = -$lon;}
				//list($width, $height) = getimagesize($image_abs);
				//$ratio = $width / $height;
				//if ($width > $height) {$width = 200; $height = $width / $ratio;} else {$height = 200; $width = $height * $ratio;}
				$geotags[] = array("lat" => $lat, "lon" => $lon, "uri" => $image_uri);
			}
		}
		if (empty($geotags)) {return null;} else {return $geotags;}
	}
	
	function getJSON(&$map, $json = array()) {
		foreach ($map as $key => $val) {
			if (is_array($val)) {
				$json_tmp = array();
				foreach ($val as $key_tmp =>$val_tmp) {
					if (is_numeric($key_tmp)) {
						$tmp = Geotag::getJSON($val_tmp);
						$json_tmp[] = "{".implode(",", $tmp)."}";
					}
				}
				if (!empty($json_tmp)) {
					$json[] = "'$key':[".implode(",", $json_tmp)."]";
				} else {
					$json_tmp = Geotag::getJSON($val);
					$json[] = "'$key':[{".implode(",", $json_tmp)."}]";
				}
			} else {
				if (is_numeric($val) || $key == "type" || $val == "true" || $val == "false") {
					$json[] = "'$key':$val";
				} else {
					$json[] = "'$key':'$val'";
				}
			}
		}
		return $json;
	}
	
	
	/* ==================================================================== */
	/* = Feeds                                                            = */
	/* ==================================================================== */
	
	function hookFeedNamespace() {
		echo "xmlns:georss=\"http://www.georss.org/georss\"";
	}
	
	function hookFeedItem() {
		if (Geotag::getCoordinates()) {
			list ($lat, $lon) = Geotag::getCoordinates();
			echo "<georss:point>".$lat." ".$lon."</georss:point>";
			if (Geotag::getPolygon()) {
			    list($polygon, $post_id) = Geotag::getPolygon();
			    $polygon = trim(str_replace(array(',','|'), ' ', $polygon));
			    echo "<georss:polygon>".$polygon."</georss:polygon>";
			}
		}
	}
	
	
	/* ==================================================================== */
	/* = Auxiliary functions                                              = */
	/* ==================================================================== */
	
	function getConvertedCoordinates($latitude=null, $longitude=null) {
		// Convert latitude if necessary
		if (!is_null($latitude)) {
			$latitude = str_replace(",", ".", $latitude);
			list($lat, $minutes, $seconds) = explode(" ", $latitude);
			$lat = floatval($lat);
			if (is_numeric($seconds)) {$minutes = ($seconds / 60) + $minutes;}
			if (is_numeric($minutes)) {$lat = ($minutes / 60) + $lat;}
			if (stripos($latitude, "s") !== false) {$lat = 0 - $lat;}
		}
		// Convert longitude if necessary
		if (!is_null($longitude)) {
			$longitude = str_replace(",", ".", $longitude);
			list($lon, $minutes, $seconds) = explode(" ", $longitude);
			$lon = floatval($lon);
			if (is_numeric($seconds)) {$minutes = ($seconds / 60) + $minutes;}
			if (is_numeric($minutes)) {$lon = ($minutes / 60) + $lon;}
			if (stripos($longitude, "w") !== false) {$lon = 0 - $lon;}
		}
		// Return coordinates
		return array($lat, $lon);
	}

	function getDisplayMap() {
		global $geotag_options;
		if (empty($geotag_options["gmap_api_key"])) {return false;}
		if (is_home() && $geotag_options["gmap_display_page"]["HOME"] == "true") {return true;}
		if (is_single() && $geotag_options["gmap_display_page"]["SINGLE"] == "true") {return true;}
		if (is_page() && $geotag_options["gmap_display_page"]["PAGE"] == "true") {return true;}
		if (is_date() && $geotag_options["gmap_display_page"]["DATE"] == "true") {return true;}
		if (is_category() && $geotag_options["gmap_display_page"]["CATEGORY"] == "true") {return true;}
		return false;
	}
	
	function getCoordinates($post_id=null) {
		global $geotag_options, $post;
		if (is_null($post_id)) {
			$post_id = $post->ID;
		}
		switch ($geotag_options["misc_wpgeocompatibility"]["DB"]) {
		case "NULL":
			$lat = get_post_meta($post_id, "_geotag_lat", true);
			$lon = get_post_meta($post_id, "_geotag_lon", true);
		break;
		case "READ":
		default:
			$lat = get_post_meta($post_id, "_geotag_lat", true);
			$lon = get_post_meta($post_id, "_geotag_lon", true);
			// Try to get WP Geo meta information
			if (empty($lat)) {$lat = get_post_meta($post_id, "_wp_geo_latitude", true);}
			if (empty($lon)) {$lon = get_post_meta($post_id, "_wp_geo_longitude", true);}
		break;
		case "WRITE":
			$lat = get_post_meta($post_id, "_wp_geo_latitude", true);
			$lon = get_post_meta($post_id, "_wp_geo_longitude", true);
		break;
		}
		if (empty($lat) || empty($lon)) {return null;} else {return array($lat, $lon, $post_id);}
	}
	
	function putCoordinates($lat=null, $lon=null, $post_id=null) {
		global $geotag_options, $post;
		if (is_null($post_id)) {
			$post_id = $post->ID;
		}
		if (!empty($lat) && !empty($lon)) {
			// Save coordinates
			list($lat, $lon) = Geotag::getConvertedCoordinates($lat, $lon);
			switch ($geotag_options["misc_wpgeocompatibility"]["DB"]) {
			case "NULL":
			case "READ":
			default:
				add_post_meta($post_id, "_geotag_lat", $lat, true) or update_post_meta($post_id, "_geotag_lat", $lat);
				add_post_meta($post_id, "_geotag_lon", $lon, true) or update_post_meta($post_id, "_geotag_lon", $lon);
			break;
			case "WRITE":
				add_post_meta($post_id, "_wp_geo_latitude", $lat, true) or update_post_meta($post_id, "_wp_geo_latitude", $lat);
				add_post_meta($post_id, "_wp_geo_longitude", $lon, true) or update_post_meta($post_id, "_wp_geo_longitude", $lon);
			break;
			}
		} else {
			// Delete coordinates
			switch ($geotag_options["misc_wpgeocompatibility"]["DB"]) {
			case "NULL":
			case "READ":
			default:
				delete_post_meta($post_id, "_geotag_lat");
				delete_post_meta($post_id, "_geotag_lon");
			break;
			case "WRITE":
				delete_post_meta($post_id, "_wp_geo_latitude");
				delete_post_meta($post_id, "_wp_geo_latitude");
			break;
			}
		}
	}
	
	function getPolygon($post_id=null) {
		global $post;
		if (is_null($post_id)) {
			$post_id = $post->ID;
		}
		$polygon = (string) get_post_meta($post_id, "_geotag_polygon", true);
		if (empty($polygon)) {return null;} else {return array($polygon, $post_id);}
	}
	
	function putPolygon($polygon=null, $post_id=null) {
	    global $post;
		if (is_null($post_id)) {
			$post_id = $post->ID;
		}
		if (!empty($polygon)) {
	        add_post_meta($post_id, "_geotag_polygon", $polygon, true) or update_post_meta($post_id, "_geotag_polygon", $polygon);
	    } else {
	        // Delete polygon
	        delete_post_meta($post_id, "_geotag_polygon");
	    }
	}
	
	/* ==================================================================== */
	/* = Functions for the templates                                      = */
	/* ==================================================================== */
	
	function the_coordinates($options=null) {
		// Displays the coordinates with a Google Map link
		global $geotag_options;
		list ($lat, $lon) = Geotag::getCoordinates();
		switch ($options) {
		case "geotaged":
			if (!empty($lat) && !empty($lon)) {return true;} else {return false;}
		break;
		case "coordinates":
			if (!empty($lat) && !empty($lon)) {
				$latitude = floor(abs($lat))."&deg; ";
				$latitude = $latitude.round((abs($lat) - floor(abs($lat))) * 60, 3)."' ";
				if ($lat >= 0) {$latitude = $latitude."N";} else {$latitude = $latitude."S";}
				$longitude = floor(abs($lon))."&deg; ";
				$longitude = $longitude.round((abs($lon) - floor(abs($lon))) * 60, 3)."' ";
				if ($lon >= 0) {$longitude = $longitude."E";} else {$longitude = $longitude."W";}
				echo $latitude.", ".$longitude;
			}
		break;
		default:
			if (!empty($lat) && !empty($lon)) {
				$latitude = floor(abs($lat))."&deg; ";
				$latitude = $latitude.round((abs($lat) - floor(abs($lat))) * 60, 3)."' ";
				if ($lat >= 0) {$latitude = $latitude."N";} else {$latitude = $latitude."S";}
				$longitude = floor(abs($lon))."&deg; ";
				$longitude = $longitude.round((abs($lon) - floor(abs($lon))) * 60, 3)."' ";
				if ($lon >= 0) {$longitude = $longitude."E";} else {$longitude = $longitude."W";}
				echo "<a href='http://maps.google.de/maps?f=q&hl=de&geocode=&q=".$lat.",".$lon."&ie=UTF8&t=k&z=".$geotag_options["gmap_zoom"]."'>".$latitude.", ".$longitude."</a>";
			}
		break;
		}
	}
}
?>