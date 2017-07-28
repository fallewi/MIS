<?php
/**
 * Part: Recent Section
 */
?>

<?php
global $burocrate_bourbon;
$best_items = ( $burocrate_bourbon['best-items'] ) ? $burocrate_bourbon['best-items'] : '8';
$best_divider = 0;
$best_fullwidth = $burocrate_bourbon['best-fullwidth'];
$best_autoplay = $burocrate_bourbon['best-autoplay'];
?>

<div class="section__best">

          <div id="slick-recent" data-slick='{"autoplay": true}' data-equalizer="recent">
            <?php
            $catod = get_the_category();
            $recent_query = new WP_Query(array(
                  'posts_per_page'=> $burocrate_bourbon['news-items'],
                  'cat' => $catod[0]->cat_ID,
                  'post__not_in' => array( $post->ID ),
                  'tax_query' => array(
                      array(
                        'taxonomy' => 'post_format',
                        'field'    => 'slug',
                        'terms'    => array( 'post-format-quote', 'post-format-image' ),
                        'operator' => 'NOT IN',
                      ),
                    ),
                  ));

            while( $recent_query->have_posts() ){ $recent_query->the_post(); ?>

                  <div class="best" style="
                    background-image: url(<?php if ( has_post_thumbnail() ) get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>); ">

                    <div class="best__wrap" style="<?php echo 'background-color:' . bourbon_get_post_color( $post->ID ); ?>;" data-equalizer-watch="recent">
                      <?php the_title( sprintf( '<h2 class="recent__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

                      <div class="recent__content">
                        <?php the_excerpt(); ?>
                      </div>

                      <div class="best__footer">
                        <div class="best__posted_on"><?php bourbon_posted_on( false ); ?></div>
                        <div class="best__comments right"><?php bourbon_entry_comments(); ?></div>
                      </div>
                    </div><!-- best__wrap -->

                  </div><!-- best -->

            <?php } wp_reset_postdata(); ?>

          </div><!-- #slick-best -->


</div><!-- section__best -->
