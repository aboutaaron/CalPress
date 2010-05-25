<?php
// This file is part of the CalPress Theme for WordPress
// http://calpresstheme.org
//
// CalPress is a project of the University of California 
// Graduate School of Journalism
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

// Theme options adapted from Thematics theme-options.php, which is derived from 
// "A Theme Tip For WordPress Theme Authors" 
// http://literalbarrage.org/blog/archives/2007/05/03/a-theme-tip-for-wordpress-theme-authors/

$themename = "CalPress";
$shortname = THEMESHORTNAME;


/* 
    Create theme options for ad
*/
$options = array (
            	array(	"name" => __('Hide Page-based Nav Menu','calpress'),
            			"desc" => __("By default, CalPress lists pages as navigation elements under the header. This behavior can be turned off.",'calpress'),
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

function mytheme_add_admin() {

    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=theme-options.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=theme-options.php&reset=true");
            die;

        } else if ( 'reset_widgets' == $_REQUEST['action'] ) {
            $null = null;
            update_option('sidebars_widgets',$null);
            header("Location: themes.php?page=theme-options.php&reset=true");
            die;
        }
    }

    add_theme_page($themename." Config", "CalPress Config", 'edit_themes', basename(__FILE__), 'mytheme_admin');

}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings saved.','calpress').'</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings reset.','calpress').'</strong></p></div>';
    if ( $_REQUEST['reset_widgets'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('widgets reset.','calpress').'</strong></p></div>';
    
?>
<div class="wrap">
<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
<h2><?php echo $themename; ?> Configuration Options</h2>

<form method="post">

<table class="form-table">

<?php foreach ($options as $value) { 
	
	switch ( $value['type'] ) {
		case 'img':
		?>
		<tr valign="top"> 
		    <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
		    <td>
		        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
			    <?php echo __($value['desc'],'calpress'); ?>
		    </td>
		</tr>
		<?php
		break;
		
		case 'text':
		?>
		<tr valign="top"> 
		    <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
		    <td>
		        <input name="<?php echo $value['id']; ?>" size="70" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" /><br />
			    <?php echo __($value['desc'],'calpress'); ?>
		    </td>
		</tr>
		<?php
		break;
		
		case 'select':
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
	        <td>
	            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
	                <?php foreach ($value['options'] as $option) { ?>
	                <option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	                <?php } ?>
	            </select>
	        </td>
	    </tr>
		<?php
		break;
		
		case 'textarea':
		$ta_options = $value['options'];
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
	        <td>
			    <?php echo __($value['desc'],'calpress'); ?>
				<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="<?php echo $ta_options['cols']; ?>" rows="<?php echo $ta_options['rows']; ?>"><?php 
				if( get_settings($value['id']) != "") {
						echo __(stripslashes(get_settings($value['id'])),'calpress');
					}else{
						echo __($value['std'],'calpress');
				}?></textarea>
	        </td>
	    </tr>
		<?php
		break;

		case "radio":
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
	        <td>
	            <?php foreach ($value['options'] as $key=>$option) { 
				$radio_setting = get_settings($value['id']);
				if($radio_setting != ''){
		    		if ($key == get_settings($value['id']) ) {
						$checked = "checked=\"checked\"";
						} else {
							$checked = "";
						}
				}else{
					if($key == $value['std']){
						$checked = "checked=\"checked\"";
					}else{
						$checked = "";
					}
				}?>
	            <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
	            <?php } ?>
	        </td>
	    </tr>
		<?php
		break;
		
		case "checkbox":
		?>
			<tr valign="top"> 
		        <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
		        <td>
		           <?php
						if(get_settings($value['id'])){
							$checked = "checked=\"checked\"";
						}else{
							$checked = "";
						}
					?>
		            <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
		            <?php  ?>
			    <?php echo __($value['desc'],'calpress'); ?>
		        </td>
		    </tr>
			<?php
		break;

		default:

		break;
	}
}
?>

</table>

<p class="submit">
<input name="save" type="submit" value="<?php _e('Save changes','calpress'); ?>" />    
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="<?php _e('Reset','calpress'); ?>" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset_widgets" type="submit" value="<?php _e('Reset Widgets','calpress'); ?>" />
<input type="hidden" name="action" value="reset_widgets" />
</p>
</form>

<p><?php _e('CalPress is an open source learning and publication theme from the <a href="http://journalism.berkeley.edu/">UC Berkeley Graduate School of Journalism</a>. For more information, visit <a href="'. THEMEURI . '">' . THEMEURI . '</a>', 'calpress'); ?></p>

<?php
}

// add theme menu
add_action('admin_menu' , 'mytheme_add_admin'); 

?>
