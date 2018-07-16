<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bourbon
 */

get_header(); ?>

<?php

	global $burocrate_bourbon;
	$layout = $burocrate_bourbon['homepage-sorter']['enabled'];
	$homepage_template = $burocrate_bourbon['homepage-template'];

	if ( $layout ) : foreach ( $layout as $key=>$value ) {

	    switch( $key ) {

	        case 'hero':
		        get_template_part( 'parts/section', 'hero' );
	        break;

	        case 'best':
	        		get_template_part( 'parts/section', 'best' );
	        break;

	        case 'featured':
	        		get_template_part( 'parts/section', 'featured' );
	        break;

	        case 'posts':
		        get_template_part( 'parts/section', $homepage_template );
	        break;
	    }
	}

	endif;
?>

<?php get_footer(); ?>
