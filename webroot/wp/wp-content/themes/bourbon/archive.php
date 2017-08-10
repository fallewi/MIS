<?php
/**
 * The template for displaying Archive pages.
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
	$template_name = $burocrate_bourbon['archives-template'];

	if ( isset( $template_name ) AND $template_name !== '' ) {

		get_template_part( 'parts/section', $template_name );

	} else {

		get_template_part( 'parts/section', 'rows' );

	}; ?>


<?php	get_footer(); ?>
