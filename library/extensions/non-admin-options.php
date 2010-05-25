<?php
$themename = "CalPress";
$shortname = THEMESHORTNAME;

// get dyanmic front page templats. 
// based on a snippet from Premium News from Woo Themes, downloaded 8.19.2009.
// accorrding to their terms and conditions on that date, all themes 
// are GPL: http://www.woothemes.com/terms-conditions/
// "All our themes are licensed under the GNU general public license (http://www.gnu.org/licenses/gpl.html). Our themes may be used by our customers on as many websites as they like."

$layout_path = CURRENTTEMPLATEPATH . '/layouts/'; 
$layouts = array();
if ( is_dir($layout_path) ) {
	if ($layout_dir = opendir($layout_path) ) { 
		while ( ($layout_file = readdir($layout_dir)) !== false ) {
			if(stristr($layout_file, ".php") !== false) {
				$layouts[] = $layout_file;
			}
		}	
	}
}

// WP-Poll
// http://lesterchan.net/wordpress/readme/wp-polls.html
// Some CalPress themes support WP-Poll in various templates.
// Grab a list for a featured poll
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

// Create producer options
$nonadmin_options = array (
            array(	"name" => "Front Page Layout",
					"desc" => "Choose the layout of to be used for the other entries on your homepage.",
		    		"id" => $shortname."_layout",
		    		"std" => "default.php",
		    		"type" => "layout",
		    		"options" => $layouts),
		    		
            array(	"name" => __('Lead Story Override','calpress'),
    				"desc" => __('HTML code here replaces the lead story on the page. To restore the main feature, this code box *must* be empty.<br />','calpress'),
    				"id" => $shortname."_front_feature_override",
    				"std" => "",
    				"type" => "textarea",
    				"options" => array(	"rows" => "5",
    									"cols" => "94") ),
		    		
            array(	"name" => __('Front Features','calpress'),
					"desc" => __('HTML code here shows up in the feature block of your front page. It is usually below the main feature of a site.<br />','calpress'),
					"id" => $shortname."_front_features",
					"std" => "",
					"type" => "textarea",
					"options" => array(	"rows" => "5",
										"cols" => "94") ),
										
			array(	"name" => __('Front Page Poll','calpress'),
					"desc" => __("Requires WP-Poll and CalPress Poll-aware theme (like Mission and Oakland). Leave blank for no poll.",'calpress'),
					"id" => $shortname."_front_poll",
					"std" => "",
					"type" => "arraylist",
					"options" => $poll_list),
					
            array(	"name" => __('Front Page Extra CSS','calpress'),
    				"desc" => __('Path to CSS only applied to front page','calpress'),
    				"id" => $shortname."_front_extra_css",
    				"std" => "",
    				"type" => "text"),
					
	        array(	"name" => __('Front Page Extra Javascript','calpress'),
    				"desc" => __('Path to J/S only applied to front page','calpress'),
    				"id" => $shortname."_front_extra_js",
    				"std" => "",
    				"type" => "text"),
					
			/*
	        array(	"name" => "Lead Headline Size Override",
					"desc" => "Pixel size of your lead block headlines (h2). If blank, theme defaults",
		    		"id" => $shortname."_lead_headline_override",
		    		"std" => "--",
		    		"type" => "select",
		    		"options" => array("--",14,16,18,20,22,24,26,28,30,32,34,36,42,48,52,56,60,64)),		
			    		
            array(	"name" => "Aux Headline Size Override",
					"desc" => "Pixel size of your auxiliary block of headlines (h2). If blank, theme defaults",
		    		"id" => $shortname."_aux_headline_override",
		    		"std" => "--",
		    		"type" => "select",
		    		"options" => array("--",14,16,18,20,22,24,26,28,30,32)),						
				
			array(	"name" => __('Front Text Box','calpress'),
					"desc" => __('This could be used for something.<br />','calpress'),
					"id" => $shortname."_front_text",
					"std" => "",
					"type" => "textarea",
					"options" => array(	"rows" => "5",
										"cols" => "94") ),
			*/
		  );


add_action('admin_menu', 'calpress_nonadmin_menus');

function calpress_nonadmin_menus()
{
    global $themename, $shortname, $nonadmin_options;

       if ( $_GET['page'] == basename(__FILE__) ) {

           if ( 'save' == $_REQUEST['action'] ) {

                   foreach ($nonadmin_options as $value) {
                       update_option( $shortname."_layout", $_REQUEST[ $value['id'] ] ); }

                   foreach ($nonadmin_options as $value) {
                       if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                       header("Location: admin.php?page=non-admin-options.php&saved=true");
                       die;

            }else if( 'reset' == $_REQUEST['action'] ) {
                /* Delete all Front Page changes */
                foreach ($nonadmin_options as $value) {
                   delete_option( $value['id'] ); 
                }
                /* Reset Front Page to the default template */
                update_option( $shortname."_layout", "default.php" );
                header("Location: admin.php?page=non-admin-options.php&reset=true");
                die;
            }
       }
    
    add_menu_page(__('CalPress Producer', 'calpress'), __('CalPress Producer', 'calpress'), 'edit_others_posts', basename(__FILE__), 'calpress_front_admin');
    //add_submenu_page(basename(__FILE__), __('Front Page', 'calpress'), __('Front Page', 'calpress'), 'edit_others_posts', basename(__FILE__), 'calpress_front_admin'); 
    //add_submenu_page(basename(__FILE__), __('Instructions', 'calpress'), __('Instructions', 'calpress'), 'edit_others_posts', basename(__FILE__), 'calpress_front_admin_instructions'); 
}

function calpress_front_admin() {

    global $themename, $shortname, $nonadmin_options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings saved.','calpress').'</strong></p></div>';
     if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings reset.','calpress').'</strong></p></div>';
    
?>
<div class="wrap">
<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
<h2><?php echo $themename; ?> Producer</h2>

<p>CalPress allows you to set many Web site options, including front page templates.</p>

<hr />

<form method="post">

<table class="form-table">

<?php foreach ($nonadmin_options as $value) { 
	
	switch ( $value['type'] ) {
		case 'layout':
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
	        <td>
	                <ul class="layouts">
	                <?php foreach ($value['options'] as $option) { ?>
	                    <li style="float:left;width:300px;padding:10px;text-align: center;">
	                        <?php 
	                            $img = str_replace(".php", ".jpg", $option);
	                            $imgpath = CURRENTTEMPLATEPATH . '/layouts/'.$img;
	                            if (file_exists($imgpath)){
	                                $img = THEMEURL.'/layouts/'.$img;
	                            }else{
	                                $img = PARENTIMAGES.'/admin/unknown.png';
	                            }
	                        ?>
	                        <img style="width:300px; height:190px;" src="<?php echo($img); ?>" /><br />
	                    <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $option; ?>" <?php if ( get_settings( $value['id'] ) == $option) { echo ' checked=""'; } elseif ($option == $value['std']) { echo ' checked=""'; } ?> /> <?php echo(ucwords(str_replace("-", " ", str_replace(".php", "", $option)))); ?>
	                    </li>
	                <?php } ?>
	                </ul>
	        </td>
	    </tr>
		<?php
		break;
		
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
		        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
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
		
		case 'arraylist':
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo __($value['name'],'calpress'); ?>:</th>
	        <td>
	            <?php echo __($value['desc'],'calpress'); ?><br />
	            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
	                <?php foreach ($value['options'] as $key => $option) { ?>
	                <option value="<?php echo($key); ?>"<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } elseif ($key == get_settings( $value['id'] )) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
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
<input name="save" type="submit" value="<?php _e('Make Live!','calpress'); ?>" />    
<input type="hidden" name="action" value="save" />
</p>
</form>

<hr />

<form method="post">
<p class="submit">
<input name="reset" type="submit" value="<?php _e('Reset the '.TEMPLATENAME,'calpress'); ?> front page." />
<input type="hidden" name="action" value="reset" />
</p>
</form>

<hr />

<?php _e('Running '.THEMENAME.' version '.CALPRESSVERSION.' by ' . THEMEAUTHOR); ?>. This software is Copyright 2009 UC Regents. CalPress is released under the terms of the <a href="<?php echo(CALPRESSURI); ?>/license.txt">GNU General Public License, Version 2</a>.

<?php
}

function calpress_front_admin_instructions() {
    global $themename, $shortname, $nonadmin_options;
?>
<div class="wrap">
<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
<h2><?php echo $themename; ?> Instructions</h2>

<p>CalPress allows you to set many Web site options, including front page templates.</p>

<hr />
<table class="form-table">
    <tr valign="top"> 
        <th scope="row">Header:</th>
        <td>Value</td>
    </tr>
</table>
<hr />
<?php
}


?>