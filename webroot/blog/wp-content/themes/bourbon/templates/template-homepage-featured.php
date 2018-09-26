<?php
/**
 * Template Name: Homepage Small Slider
 */
 ?>
<?php get_header(); ?>

<?php
global $burocrate_bourbon;
$featured_items = ( $burocrate_bourbon['featured-items'] ) ? $burocrate_bourbon['featured-items'] : '10';
$featured_columns = ( $burocrate_bourbon['featured-columns'] ) ? $burocrate_bourbon['featured-columns'] : '4';
$featured_divider = $burocrate_bourbon['featured-divider'] ? 'featured__divider' : '';
$featured_fullwidth = $burocrate_bourbon['featured-fullwidth'];
$featured_autoplay = $burocrate_bourbon['featured-autoplay'];
$cat_id = get_query_var( 'cat' );

?>

<div class="section__featured">

  <?php echo esc_attr( $featured_fullwidth ) ? '<div class="featured__fullwidth">' : '<div class="row"><div class="columns large-12">'; ?>

        <div id="slick-featured" data-slick='{"slidesToShow": <?php echo esc_attr( $featured_columns ); ?>, "slidesToScroll": 4, "autoplay": <?php echo ( $featured_autoplay ) ? 'true' : 'false'; ?>}' data-equalizer="featured">

          <?php $wp_query = new WP_Query( array(
                'cat' => $cat_id,
                'posts_per_page' => esc_attr( $featured_items ),
                'tax_query' => array(
                  array(
                    'taxonomy' => 'post_format',
                    'field'    => 'slug',
                    'terms'    => array( 'post-format-quote', 'post-format-image' ),
                    'operator' => 'NOT IN',
                  ),
                ),
                ));
          while( $wp_query->have_posts() ){ $wp_query->the_post(); ?>

            <div class="featured <?php echo esc_attr( $featured_divider ); ?>">

              <!-- Image -->
              <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                <div class="featured__image" style=" background-image: url(<?php if ( has_post_thumbnail() ) get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>);">
                </div>
              </a>

                <div class="featured__wrap" data-equalizer-watch="featured">
                  <!-- <div class="featured__category"><?php bourbon_entry_category(1); ?></div> -->
                  <?php the_title( sprintf( '<h5 class="featured__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h5>' ); ?>
                </div>
                  <div class="featured__footer">
                    <div class="featured__posted_on"><?php bourbon_posted_on( false ); ?></div>
                    <div class="featured__comments"><?php bourbon_entry_comments(); ?></div>
                  </div>
              </div>


          <?php }; wp_reset_postdata(); ?>

        </div>

    <?php echo esc_attr( $featured_fullwidth ) ? '</div>' : '</div></div>'; ?><!-- /columns --><!-- /row -->

</div><!-- section -->


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
