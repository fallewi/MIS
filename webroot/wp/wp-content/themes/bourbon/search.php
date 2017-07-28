<?php
/**
 * The template for displaying search results pages.
 *
 * @package bourbon
 */

get_header(); ?>

<!-- Hero section -->
<?php get_template_part( 'parts/section', 'hero' );  ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<div class="row">
			<div class="columns large-9 large-centered">

			<?php if ( have_posts() ) : ?>

					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>

						<?php
						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'content', 'search' );
						?>

					<?php endwhile; ?>

					<?php bourbon_posts_navigation(); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>
			</div>
		</div>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer(); ?>
