<?php
/**
 * Template Name: Homepage Big Slider
 */
 ?>
<?php get_header(); ?>
 <?php global $burocrate_bourbon; ?>

 <?php
 global $burocrate_bourbon;
 $best_items = ( $burocrate_bourbon['best-items'] ) ? $burocrate_bourbon['best-items'] : '8';
 $best_fullwidth = $burocrate_bourbon['best-fullwidth'];
 $best_autoplay = $burocrate_bourbon['best-autoplay'];
 ?>

 <div class="section__best">

   <?php echo esc_attr( $best_fullwidth ) ? '' : '<div class="row"><div class="columns large-12">'; ?>

           <div id="slick-best" data-slick='{"slidesToShow": 1, "slidesToScroll": 1, "autoplay": <?php echo ( $best_autoplay ) ? 'true' : 'false'; ?>}' data-equalizer="best">

             <?php $wp_query = new WP_Query( array(
                     'posts_per_page' => $best_items,
                     'meta_query'    => array (
                       array (
                         'key' => 'post_best',
                         'value' => 'best',
                         )
                       ),
                   ));
             while( $wp_query->have_posts() ){ $wp_query->the_post(); ?>

                   <div class="best" style="background-image: url(<?php if ( has_post_thumbnail() ) get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>);">

                       <div class="best__fill" style="<?php echo 'background-color:' . bourbon_get_post_color( $post->ID ) ; ?>;">
                         <div class="row">
                           <div class="columns large-12">

                             <div class="best__table">
                               <div class="best__table--cell">
                                 <div class="best__wrap">
                                   <div class="best__header">
                                     <div class="best__category"><?php bourbon_entry_category(1, false); ?></div>
                                   </div>
                                   <?php the_title( sprintf( '<h2 class="best__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                                   <div class="best__content">
                                     <?php the_excerpt(); ?>
                                   </div>
                                   <div class="best__line"></div>
                                   <div class="best__footer">
                                     <div class="best__posted_on"><?php bourbon_posted_on( false ); ?></div>
                                     <div class="best__comments"><?php bourbon_entry_comments(); ?></div>
                                   </div>
                                 </div><!-- best__wrap -->
                               </div>
                             </div><!-- best__table -->


                           </div>
                         </div>
                       </div>

                   </div><!-- best -->

             <?php }; wp_reset_query(); ?>

           </div><!-- #slick-best -->

   <?php echo esc_attr( $best_fullwidth ) ? '' : '</div></div>'; ?><!-- /columns --><!-- /row -->

 </div><!-- section__best -->



 <div id="primary" class="content-area">
 		<main id="main" class="site-main" role="main">

 			<?php
 				echo '<div class="row">';
 				echo '<div class="columns large-8">';
 			?>

      <?php  if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
          elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
          else { $paged = 1; }
          $wp_query = new WP_Query( array(
                'post_type' => 'post',
                'posts_per_page' => 6,
                'paged' => $paged,
          )); ?>

 					<?php if ( $wp_query->have_posts() ) : ?>
 						<?php /* Start the Loop */ ?>
 						<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
 							<?php
 								/* Include the Post-Format-specific template for the content.
 								 * If you want to override this in a child theme, then include a file
 								 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
 								 */
 								get_template_part( 'content', get_post_format() );

 							?>
 						<?php endwhile; ?>
            <?php wp_reset_postdata(); ?>

 							<?php global $burocrate_bourbon;
 							if ( $burocrate_bourbon['pagination'] == '1' AND !wp_is_mobile() ) {
 						  	the_posts_pagination();
 						  } else {
 						  	bourbon_posts_navigation();
 						  } ?>

 					<?php else : ?>

 						<?php get_template_part( 'content', 'none' ); ?>

 					<?php endif; ?>
 				</div><!-- columns -->

 				<?php get_sidebar(); ?>

 			</div><!-- row -->
 	</main><!-- #main -->
 </div><!-- #primary -->



<?php get_footer(); ?>
