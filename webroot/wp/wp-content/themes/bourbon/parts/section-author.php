<?php
/**
 * The template for displaying Author
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */
?>

<div class="author__wrap">
	<div class="author__avatar"><?php echo get_avatar( get_the_author_meta('user_email'), 60 ); ?></div>
	<div class="author__name"><?php the_author_posts_link(); ?></div>
	<div class="author__description"><?php the_author_meta('description'); ?></div>
</div>
