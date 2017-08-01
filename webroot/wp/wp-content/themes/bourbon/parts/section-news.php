<?php
/**
 * Part: News Section
 */
?>

<?php global $burocrate_bourbon; $news_items = ( $burocrate_bourbon['news-items'] ) ? $burocrate_bourbon['news-items'] : '2'; ?>

<div class="section__news">
    <?php
  $catod = get_the_category();
  $query = new WP_Query(array(
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
  while( $query->have_posts() ){ $query->the_post(); ?>

      <div class="news">
          <?php if ( has_post_thumbnail() ) { ?>
            <?php echo '<a href=' . get_permalink( $post->ID ) . '>'; ?>
                <div class="news__thumbnail hide-for-small-only" style="background-image: url('<?php get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>'); ">
                </div>
            <?php echo '</a>'; ?>
          <?php }  ?>

        <div class="news__wrap">
          <?php the_title( sprintf( '<h2 class="news__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
          <div class="news__content hide-for-small-only"><?php the_excerpt(); ?></div>

          <div class="news__meta">
            <div class="news__author"><?php printf('%s<span class="news__author--in">in</span>', bourbon_author() )?></div>
            <div class="news__category"><?php bourbon_entry_category(); ?></div>
            <div class="news__posted_on right "><?php if ( 'post' == get_post_type() ) bourbon_posted_on(); ?></div>
          </div>

        </div>

      </div>

      <?php } wp_reset_postdata(); ?>

</div>
