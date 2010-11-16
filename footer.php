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
<?php 
if (current_user_can('level_10')) {
	echo '<!-- ' . get_num_queries() . ' queries in ' . timer_stop(0,3) . ' seconds -->';
} 
?>
</body>
</html>