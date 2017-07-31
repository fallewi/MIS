<?php
/**
 * The template for displaying Rows
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */
?>

<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

					<?php if ( have_posts() ) : ?>
					<div class="row ">
						<div class="columns large-9 large-centered">
							<div class="section_rows">

								<?php /* Start the Loop */ ?>
								<?php while ( have_posts() ) : the_post(); ?>

									<div class="rows">
						          <?php if ( has_post_thumbnail() ) { ?>
						            <?php echo '<a href=' . get_permalink( $post->ID ) . '>'; ?>
						                <div class="rows__thumbnail hide-for-small-only" style="background-image: url('<?php get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>'); ">
						                </div>
						            <?php echo '</a>'; ?>
						          <?php }  ?>

						        <div class="rows__wrap">
						          <?php the_title( sprintf( '<h2 class="news__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
						          <div class="rows__content hide-for-small-only"><?php the_excerpt(); ?></div>

						          <div class="rows__meta">
						            <div class="rows__author"><?php printf('%s<span class="rows__author--in">in</span>', bourbon_author() )?></div>
						            <div class="rows__category"><?php bourbon_entry_category(); ?></div>
						            <div class="rows__posted_on right "><?php if ( 'post' == get_post_type() ) bourbon_posted_on(); ?></div>
						          </div>

						        </div>
						      </div>

						<?php endwhile; ?>
						</div>
					</div><!-- columns -->
				</div><!-- row -->

					<div class="row">
						<div class="columns large-9 large-centered">
							<?php global $burocrate_bourbon;
							if ( $burocrate_bourbon['pagination'] == '1' ) {
						  	the_posts_pagination();
						  } else {
						  	bourbon_posts_navigation();
						  } ?>
						</div>
					</div>


					<?php else : ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->
