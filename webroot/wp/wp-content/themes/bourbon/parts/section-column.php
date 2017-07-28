<?php
/**
 * The template for displaying One Column Blog
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */
?>

<!-- 1 Column Blog -->
<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

					<?php if ( have_posts() ) : ?>
					<div class="row">
						<div class="columns large-9 large-centered">

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php
								/* Include the Post-Format-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
								 */
								get_template_part( 'content', get_post_format() );
							?>
						<?php endwhile; ?>

							<?php global $burocrate_bourbon;
							if ( $burocrate_bourbon['pagination'] == '1' ) {
						  	the_posts_pagination();
						  } else {
						  	bourbon_posts_navigation();
						  } ?>

					</div><!-- columns -->

					<?php else : ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; ?>
				</div><!-- row -->

	</main><!-- #main -->
</div><!-- #primary -->
