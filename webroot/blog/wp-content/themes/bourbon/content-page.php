<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package bourbon
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <div class="entry">



    <?php if ( ! is_page_template('templates/template-column-hero.php') ): ?>
      <header class="entry__header">
        <?php the_title( sprintf( '<h1 class="entry__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
      </header>
      
      <figure>
        <?php if ( has_post_thumbnail() ) { ?>
            <div class="thumbnail__wrap">
              <?php echo '<a href=' . get_permalink( $post->ID ) . '>'; ?>
               <img src="<?php get_thumbnail_url_only( 'bourbon_thumbnail_xlarge' ); ?>" class="entry__thumbnail">
              <?php echo '</a>'; ?>
              <?php bourbon_get_post_label( $post->ID ); ?>
            </div>
        <?php }  ?>
      </figure>

    <?php endif; ?>

    <div class="entry__content">
      <?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				__( 'Continue %s <i class="fa fa-angle-right"></i>', 'bourbon' ),
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

    <div class="entry__footer">
      <div class="entry__tags right hide-for-small-only"><?php bourbon_entry_tags(); ?></div>
      <?php bourbon_theme_post_share(); ?>
    </div>

  </div><!-- entry -->

</article>
