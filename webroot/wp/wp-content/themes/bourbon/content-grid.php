<?php
/**
 * @package bourbon
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'template-grid' ); ?>>



  <div class="grid">

    <header class="grid__header">
      <?php the_title( sprintf( '<h3 class="grid__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
    </header>

    <div class="grid__meta">
        <div class="grid__author"><?php printf('%s<span class="grid__author--in">in</span>', bourbon_author() )?></div>
        <div class="grid__category"><?php bourbon_entry_category(1); ?></div>
        <div class="grid__posted_on right "><?php if ( 'post' == get_post_type() ) bourbon_posted_on(); ?></div>
    </div>


    <?php if ( get_post_format() == 'video' ) : ?>
    <!-- Video -->
      <figure>
            <div class="thumbnail__wrap">
              <div class="flex-video widescreen vimeo">
                <?php bourbon_content_extract_iframe(); ?>
              </div>
            </div>
      </figure>

    <?php elseif ( get_post_format() == 'audio' ) : ?>
    <!-- Audio -->
      <figure>
        <div class="thumbnail__wrap">
          <div class="thumbnail--audio">
            <?php bourbon_content_extract_iframe(); ?>
          </div>
        </div>
      </figure>

    <?php else : ?>
      <figure>
        <?php if ( has_post_thumbnail() ) { ?>
            <div class="grid__thumbnail">

              <?php echo '<a href=' . get_permalink( $post->ID ) . '>'; ?>
               <img src="<?php get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>">
              <?php echo '</a>'; ?>

              <?php bourbon_get_post_label( $post->ID ); ?>
            </div>
        <?php }  ?>
      </figure>

    <?php endif; ?>

    <div class="grid__content">
      <?php
			/* translators: %s: Name of current post */
			the_excerpt( sprintf(
				__( 'Continue reading %s <i class="fa fa-angle-right"></i>', 'bourbon' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
			?>

			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'bourbon' ),
					'after'  => '</div>',
				) );
			?>
    </div>



  </div><!-- grid -->

</article>
