<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package bourbon
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<?php global $burocrate_bourbon; ?>
<?php $topbar_fullwidth = $burocrate_bourbon['topbar-fullwidth'];?>
<?php $topbar_background = $burocrate_bourbon['topbar-background'];?>
<?php $topbar_invert = $burocrate_bourbon['topbar-invert'];?>
<?php
if ( $burocrate_bourbon['topbar-sticker'] ) {
$topbar_sticker = ( $burocrate_bourbon['topbar-sticker'][0] !== '0' ) ? 'sticky_class: sticker; sticky_on: ' . $burocrate_bourbon['topbar-sticker'][0] : '';
$topbar_sticker .= ( $burocrate_bourbon['topbar-sticker'][1] !== '0' AND $burocrate_bourbon['topbar-sticker'][1] !== $burocrate_bourbon['topbar-sticker'][0] ) ? ', ' . $burocrate_bourbon['topbar-sticker'][1] : '';
$topbar_sticker .= ( $burocrate_bourbon['topbar-sticker'][2] !== '0' AND $burocrate_bourbon['topbar-sticker'][2] !== $burocrate_bourbon['topbar-sticker'][0] ) ? ', ' . $burocrate_bourbon['topbar-sticker'][2] : '';
$topbar_sticker .= ';';
} else { $topbar_sticker = ''; }
?>
<body <?php $topbar_invert ? body_class('invert__topbar') : body_class(); ?> >

<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'bourbon' ); ?></a>
	<header id="masthead" class="site-header" role="banner">
		<div class="top-bar-background <?php echo esc_attr( $topbar_fullwidth ) ? '' : 'contain-to-grid' ?> primary sticker">
		  <nav class="top-bar" data-topbar role="navigation" data-options="is_hover: true; mobile_show_parent_link: false; <?php echo esc_attr( $topbar_sticker ); ?> ">

	    		<!-- Header-image -->
	    		  <?php if ( get_header_image() ) : ?>
							<div class="top-bar__logo left">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
									<img src="<?php header_image(); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="">
								</a>
							</div>
						<?php endif; // End header image check. ?>

		    <ul class="title-area">
		      <li class="name">
		        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		      </li>
		       <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
		      <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
		    </ul>

		    <section class="top-bar-section">
		    <?php wp_nav_menu(array(
		               'container' => false,                           // remove nav container
		               'container_class' => '',                        // class of container
		               'menu' => '',                                   // menu name
		               'menu_class' => 'right',           							// adding custom nav class
		               'theme_location' => 'primary',                  // where it's located in the theme
		               'before' => '',                                 // before each link <a>
		               'after' => '',                                  // after each link </a>
		               'link_before' => '',                            // before each link text
		               'link_after' => '',                             // after each link text
		               'depth' => 3,                                   // limit the depth of the nav
		               'fallback_cb' => false,                         // fallback function (see below)
		               'walker' => new bourbon_walker()
		             )); ?>
		    </section>
		  </nav>

		</div>

	</header><!-- #masthead -->
