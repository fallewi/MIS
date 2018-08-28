<?php
/**
 * The template for displaying Category pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */
?>

<?php get_header(); ?>

<!-- Hero section -->
<?php get_template_part( 'parts/section', 'hero' );  ?>

<?php

	global $burocrate_bourbon;
  $template_name = $burocrate_bourbon['categories-template'];

	if ( isset( $template_name ) && $template_name !== '' ) {

		get_template_part( 'parts/section', $template_name );

	} else {

		get_template_part( 'parts/section', 'classic' );

	}; ?>

<?php get_footer(); ?>



