<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package bourbon
 */

get_header(); ?>

<!-- Hero section -->
<?php get_template_part( 'parts/section', 'hero' );  ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<div class="row">

								<div class="columns large-3 medium-6"><?php the_widget( 'WP_Widget_Recent_Posts' ); ?><div class="widget__line"></div></div>

								<?php if ( bourbon_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
								<div class="columns large-3 medium-6"><div class="widget widget_categories">
										<h2 class="widget-title"><?php _e( 'Most Used Categories', 'bourbon' ); ?></h2>
										<ul>
										<?php
											wp_list_categories( array(
												'orderby'    => 'count',
												'order'      => 'DESC',
												'show_count' => 1,
												'title_li'   => '',
												'number'     => 10,
											) );
										?>
										</ul>
									</div><!-- .widget -->
									<div class="widget__line"></div>
									</div>
								<?php endif; ?>

								<div class="columns large-3 medium-6"><?php
									$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'bourbon' ), convert_smilies( ':)' ) ) . '</p>';
									the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
								?><div class="widget__line"></div></div>

								<div class="columns large-3 medium-6"><?php the_widget( 'WP_Widget_Tag_Cloud' ); ?><div class="widget__line"></div></div>

			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
