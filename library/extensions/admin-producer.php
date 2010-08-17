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

function calpress_producer_admin() {

    global $themename, $shortname, $calpress_nonadmin_options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('Producer settings saved.','calpress').'</strong></p></div>';
     if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('Producer settings reset.','calpress').'</strong></p></div>';
    
?>
<div id="calpress-options" class="wrap metabox-holder">
<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
<h2><?php echo $themename; ?> Front Page Producer</h2>

<p>CalPress allows you to set many Web site options, including front page templates.</p>

<hr />

<form method="post">

<table class="form-table">

<?php foreach ($calpress_nonadmin_options as $value) { 
	
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
	                            // see if a n
	                            $img_needed = str_replace(".php", ".jpg", $option);
	                            $imgpath_child = CURRENTTEMPLATEPATH . '/layouts/'.$img_needed;
	                            $imgpath_parent = TEMPLATEPATH . '/layouts/'.$img_needed;
	                            
	                            $img = PARENTIMAGES.'/admin/unknown.png'; // final url for img element 
	                            
	                            if ( file_exists( $imgpath_child ) ){
	                                $img = THEMEURL.'/layouts/'.$img_needed;
	                            } elseif ( file_exists( $imgpath_parent ) ) {
	                                $img = CALPRESSURI.'/layouts/'.$img_needed;
	                            }
	                        ?>
	                    <img style="width:300px; height:190px;border: 2px solid #a2a2a2" src="<?php echo($img); ?>" /><br />
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
<input name="save" type="submit" value="Make Live!" />    
<input type="hidden" name="action" value="save" />
</p>
</form>



<form method="post">  
<p class="submit reset">
<input name="reset" type="submit" value="Reset front page" />
<input type="hidden" name="action" value="reset" />
</p>
</form>

<?php require_once( dirname(__FILE__).'/admin-footer.php'); ?>
</div>
<?php
}
?>