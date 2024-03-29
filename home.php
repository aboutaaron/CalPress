<?php get_header() ?>
		<div id="content" class="grid_8 alpha">
		<?php
		// determine which category to show on front page. default from wordpress is all. 
         $front_category_to_use = get_option('cp_front_category');
         $front_category_set = false;
         
         if (is_numeric($front_category_to_use) && $front_category_to_use > 0) { //use a special category
             $front_category_set = true;
         }
         
          // in addition to front category, we can use another category to place featured spot
         $front_featured_category_to_use = get_option('cp_front_featured_category');
         $front_featured_category_set = false;
         $use_front_feature = false;
         if (is_numeric($front_featured_category_to_use) && $front_featured_category_to_use > 0) {
             $front_featured_category_set = true;
             $use_front_feature = true;
             $frontposts_ids = array();
             
         }
		?>
		<?php if (!is_paged()): // if true front page, show special layout ?>
            <?php // hook above loop ?>
            <?php calpress_hook_frontfeatures_above(); ?>

            <?php
            //setup variables that can be used in individual layouts
            if ( $front_category_set ) { // on front page, change default query to only include selected front page category
                
                // in addition to a front category above, default layouts in calpress allow the lead spot to 
                // be selected by hand by finding post in both the front category above (selected in CalPress Basic config options)
                // and in the featured category selected in CP basic config in the admin. This is only enabled in the front category
                // is also selected
                if ($front_featured_category_set) { 
                    
                    // Don't include articles from the front featured category in our main front feed b/c there could be duplicates
                    //query_posts( array('category__in' => array($front_category_to_use), 'category__not_in' => array($front_featured_category_to_use) ) );
                    $cptaxquery['tax_query'] = array(
									array(
										'taxonomy' => 'category',
										'terms' => array($front_category_to_use),
										'field' => 'id',
										'operator' => 'IN'
									),
									array(
										'taxonomy' => 'category',
										'terms' => array($front_featured_category_to_use),
										'field' => 'id',
										'operator' => 'NOT IN'									
									)
					);
					
					query_posts($cptaxquery);


                    // Store front features in a sepreate loop
                    //$featuredfrontposts = new WP_Query( array('category__and' => array($front_category_to_use, $front_featured_category_to_use) ) );
					$taxqueryfeaturedargs = array(
							'tax_query' => array(
								'relation' => 'AND',
								array(
									'taxonomy' => 'category',
									'terms' => array($front_category_to_use),
									'field' => 'id'
									),
								array(
									'taxonomy' => 'category',
									'terms' => array($front_featured_category_to_use),
									'field' => 'id'
									)
							)
						);
					$featuredfrontposts = new WP_Query($taxqueryfeaturedargs);
                    $featuredfrontposts_ids = array();
                    while ($featuredfrontposts->have_posts()){
                        $featuredfrontposts->the_post();
                        $featuredfrontposts_ids[] = get_the_ID();
                    }
                    $featuredfrontposts->rewind_posts();
                } else {
                    query_posts('cat='.$front_category_to_use);
                }
            }

            
            // save the value of the "lead story override" in calpress producer here. Layouts should 
            // take that value into account, and show that code instead of the automated first post. 
            $get_leadstoryoverride = THEMESHORTNAME."_front_feature_override"; // get value from admin
            $leadstoryoverride_content = trim(get_option($get_leadstoryoverride));
            if ($leadstoryoverride_content != ""){
                $leadstoryoverride = true;
            } else {
                $leadstoryoverride = false;
            }
            
            ?>

            <?php // include the blog.php layout, or any optional layout selected in CalPress Producer ?>
            <?php require( $front_template ); ?>
            
            <?php //hook below loop ?>
            <?php calpress_hook_frontfeatures_below(); ?>
        <?php else: //!is_paged()?> 
			<?php
				if ( $front_category_set ){
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					query_posts('cat='.$front_category_to_use.'&paged=' . $paged);
				}
				while ( have_posts() ) {
					the_post();
					calpress_loop_content();
				}
			?>
			<div id="nav-below" class="navigation">
		    	<div class="nav-previous"><?php next_posts_link(__( 'Older posts <span class="meta-nav">&raquo;</span>', 'sandbox' )) ?></div>
		    	<div class="nav-next"><?php previous_posts_link(__( '<span class="meta-nav">&laquo;</span>Newer posts', 'sandbox' )) ?></div>
		    </div>
		<?php endif //!is_paged()?>    
		</div><!-- #content -->
        <?php get_sidebar() ?>
        
<?php get_footer() ?>