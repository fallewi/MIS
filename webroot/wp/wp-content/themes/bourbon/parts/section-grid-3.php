<?php
/**
 * The template for displaying Three Columns Grid archive.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */
?>


<!-- 3 Grid Masonry -->
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<div class="row  masonry-container grid__template--3">
				<?php while ( have_posts() ) : the_post(); ?>

					<div class="columns large-4 medium-6 masonry-item">
						<?php if ( get_post_format() == 'quote' ): ?>
							<?php get_template_part( 'content', 'quote' ); ?>
						<?php elseif ( get_post_format() == 'image' ): ?>
							<?php get_template_part( 'content', 'image' ); ?>
						<?php else: ?>
							<?php get_template_part( 'content', 'grid' ); ?>
						<?php endif; ?>
					</div><!-- columns -->

				<?php endwhile; ?>
			</div><!-- row -->

			<div class="row">
				<nav class="columns large-12 navigation post-navigation">

					<?php global $burocrate_bourbon;
					if ( $burocrate_bourbon['pagination'] == '1' ) {
				  	the_posts_pagination();
				  } else {
				  	bourbon_posts_navigation();
				  } ?>

				</nav>
			</div>

		<?php else : ?>

			<div class="row">
				<div class="columns large-9 large-centered">
					<?php get_template_part( 'content', 'none' ); ?>
				</div>
			</div>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<!-- Infinite scroll + Masonry function -->
<?php bourbon_infinite_masonry(); ?>
