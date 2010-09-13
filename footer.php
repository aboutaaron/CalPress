<?php
global $calpress_mobile;
?>
    </div><div class="clear"></div><!-- #container -->
    
    <?php 
	    if ($calpress_mobile->showMobile()) {
	       calpress_globalnav("12"); 
	    }
	?>
    
    <div id="footer" class="grid_12">
		
		
		<?php calpress_hook_footer(); ?>
		
		
		
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
    
</div><!-- #wrapper .hfeed -->

<?php wp_footer() ?>
<!-- <?php echo get_num_queries(); ?> queries in <?php timer_stop(1,5); ?> -->
</body>
</html>