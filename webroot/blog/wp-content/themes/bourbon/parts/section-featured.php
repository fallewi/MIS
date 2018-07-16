<?php
/**
 * Part: Featured section
 */
 ?>

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

          <?php $query = new WP_Query( array(
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
          while( $query->have_posts() ){ $query->the_post(); ?>

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
