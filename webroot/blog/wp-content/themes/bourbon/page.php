<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package bourbon
 */
?>

<?php get_header(); ?>

<?php global $burocrate_bourbon; $sidebar_position = $burocrate_bourbon['sidebar-position']; ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<div class="row ">

			<?php if ( $sidebar_position == '1' ) {
				echo '<div class="columns large-8">';
			} elseif ( $sidebar_position == '2' ) {
				get_sidebar();
				echo '<div class="columns large-8">';
			} elseif ( $sidebar_position == '3' ) {
				echo '<div class="columns large-12">';
			} else {
				echo '<div class="columns large-8">';
			}
			?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- columns -->

			<?php ( $sidebar_position !== '1' ) ? '' : get_sidebar(); ?>

		</div><!-- row -->
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
