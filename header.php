<?php
global $calpress_mobile;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">
	<title><?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ); echo(" : "); echo wp_specialchars( get_bloginfo('description'), 1 ) ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo(PRIMARYCSS); ?>" />
    <script type='text/javascript' src='<?php echo(PARENTJS) ?>/calpress-min.js'></script>
    <?php wp_head() // For plugins ?>
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="<?php printf( __( '%s latest posts', 'sandbox' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'sandbox' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<?php
	if ( $calpress_mobile->showMobile() && $calpress_mobile->iphone ) {
	    echo "<meta name=\"viewport\" content=\"user-scalable=no, width=device-width\" />";
    }
	?>
	<link rel="shortcut icon" href="<?php echo(THEMEURL); ?>/favicon.ico" />
</head>

<body class="<?php calpress_body_class(); ?>">
    <?php
    // if on mobile phone but not viewing mobile site, give option
    if (!$calpress_mobile->showMobile() && $calpress_mobile->mobiledevice) {
        echo("<div id=\"show-mobile\"><a href=\"?theme_view=mobile\">Switch to mobile version</a></div>");
    }
    ?>

<div id="wrapper" class="hfeed container_12">

	<div id="header" class="grid_12">
	    <div id="pre-title"></div>
		<div id="blog-title"><h1><span><a href="<?php bloginfo('home') ?>/" title="<?php echo wp_specialchars( get_bloginfo('name'), 1 ) ?>" rel="home"><?php bloginfo('name') ?></a></span></h1></div>
		<div id="post-title"></div>
		<div id="blog-description"><span><?php bloginfo('description') ?></span></div>
	</div><div class="clear"></div><!--  #header -->
	
	<?php calpress_globalnav("12") ?><!-- #menu -->

    <div id="container" class="grid_12">