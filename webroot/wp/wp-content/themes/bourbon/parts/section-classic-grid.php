<?php
/**
 * The template for displaying Classic And Grid Blog
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */
?>

<?php global $burocrate_bourbon; $sidebar_position = $burocrate_bourbon['sidebar-position']; ?>

<!-- Classic Blog with Sidebar -->
<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">


			<div class="row">
				<div class="columns large-8">

					<?php if ( have_posts() ) : ?>

						<?php the_post(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>

						<?php /* Start the Loop */ ?>
						<div class="row masonry-container grid__template--2">
						<?php while ( have_posts() ) : the_post(); ?>

							<div class="columns medium-6 masonry-item">
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

							<?php global $burocrate_bourbon;
							if ( $burocrate_bourbon['pagination'] == '1' ) {
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


<!-- Infinite scroll + Masonry function -->
<?php bourbon_infinite_masonry(); ?>
