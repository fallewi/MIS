<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package bourbon
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments__area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h3 class="comments__title">
			<?php
				printf( _nx( 'One comment', '%1$s comments', get_comments_number(), 'comments title', 'bourbon' ),
					number_format_i18n( get_comments_number() ), '' . '' );
			?>
		</h3>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav class="comments__navigation" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'bourbon' ); ?></h2>
			<div class="comments__navigation">

				<div class="comments__navigation-prev"><?php previous_comments_link( __( 'Older Comments', 'bourbon' ) ); ?></div>
				<div class="comments__navigation-next"><?php next_comments_link( __( 'Newer Comments', 'bourbon' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comments__list">
			<?php
				wp_list_comments( array(
					'style'      => 'div',
					'short_ping' => true,
					'avatar_size' => 40,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav class="comments__navigation" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'bourbon' ); ?></h2>
			<div class="comments__navigation">

				<div class="comments__navigation-prev"><?php previous_comments_link( __( 'Older Comments', 'bourbon' ) ); ?></div>
				<div class="comments__navigation-next"><?php next_comments_link( __( 'Newer Comments', 'bourbon' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="comments__no-comments"><?php _e( 'Comments are closed.', 'bourbon' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
