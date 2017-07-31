<?php
/**
 * The template for displaying Classic Blog
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


			<?php if ( $sidebar_position == '1' ) {
				echo '<div class="row">';
				echo '<div class="columns large-8">';
			} elseif ( $sidebar_position == '2' ) {
				echo '<div class="row sidebar__left">';
				get_sidebar();
				echo '<div class="columns large-8">';
			} elseif ( $sidebar_position == '3' ) {
				echo '<div class="row">';
				echo '<div class="columns large-12">';
			} else {
				echo '<div class="row">';
				echo '<div class="columns large-8">';
			}
			?>

					<?php if ( have_posts() ) : ?>
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

					<?php else : ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; ?>
				</div><!-- columns -->

				<?php ( $sidebar_position !== '1' ) ? '' : get_sidebar(); ?>

			</div><!-- row -->
	</main><!-- #main -->
</div><!-- #primary -->
