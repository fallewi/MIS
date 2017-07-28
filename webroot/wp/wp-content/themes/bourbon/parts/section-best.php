<?php
/**
 * Part: Best Section
 */
?>
<?php // TODO: Какие посты показывать ?>
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
                    'ignore_sticky_posts'=> true,
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
