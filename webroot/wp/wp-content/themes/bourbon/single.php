<?php
/**
 * The template for displaying all single posts.
 *
 * @package bourbon
 */

?>

<?php get_header(); ?>

<?php global $burocrate_bourbon; $post_layout = $burocrate_bourbon['post-layout']; ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

			<div class="row">

				<?php if ( $post_layout == '1' ) {
					echo '<div class="columns large-8">';
				} elseif ( $post_layout == '2' ) {
					get_sidebar();
					echo '<div class="columns large-8">';
				} elseif ( $post_layout == '3' ) {
					echo '<div class="columns large-12">';
				} else {
					echo '<div class="columns large-9 large-centered">';
				}
				?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>


					<?php $burocrate_bourbon['author-post'] ? get_template_part( 'parts/section', 'author' ) : ''; ?>

					<?php bourbon_post_navigation(); ?>

					<!-- News Section -->
					<?php global $burocrate_bourbon;
					if ( $burocrate_bourbon['news-items'] > 0 ) {
						get_template_part( 'parts/section' , $burocrate_bourbon['news-format'] );
					} ?>

					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- columns -->

			<?php ( $post_layout == '1' ) ? get_sidebar() : ''; ?>

		</div><!-- row -->
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
