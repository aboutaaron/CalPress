
    	<div id="footer" class="grid_12 alpha omega">
    		<span class="copyright">Copyright <?php echo date('Y'); ?>, <?php bloginfo('name') ?></span>
    		<span class="meta-sep">|</span>
    		<span class="poweredby">Powered by <span id="generator-link"><a href="http://wordpress.org/" title="<?php _e( 'WordPress', 'sandbox' ) ?>" rel="generator"><?php _e( 'WordPress', 'sandbox' ) ?></a></span> and the <span id="theme-link"><a href="http://calpresstheme.org" title="<?php _e( 'CalPress theme for WordPress', 'sandbox' ) ?>" rel="designer"><?php _e( 'CalPress', 'sandbox' ) ?></a> theme.</span></span>
    		<?php
    		    global $calpress_mobile;
    		    if ($calpress_mobile->showMobile()) {
    		?>
    		    <span class="meta-sep">|</span> 
    		    <span class="switch-mode"><a href="?theme_view=standard">Standard site</a></span>
    		<?php        
    		    }
    		?>
    		
    	</div><div class="clear"></div><!-- #footer -->
    </div><div class="clear"></div><!-- #container -->
</div><!-- #wrapper .hfeed -->

<?php wp_footer() ?>
<!-- <?php echo get_num_queries(); ?> queries in <?php timer_stop(1,5); ?> -->
</body>
</html>