<?php
/**
 * bourbon functions and definitions
 *
 * @package bourbon
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'bourbon_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function bourbon_setup() {

	/*
	 * Add Redux Framework
	 */
	require get_template_directory() . '/lib/admin-init.php';

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on bourbon, use a find and replace
	 * to change 'bourbon' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'bourbon', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'bourbon' ),
		// 'secondary' => __( 'Secondary Menu', 'bourbon' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'image', 'video', 'quote', 'audio', 'gallery',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'bourbon_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

}
endif; // bourbon_setup
add_action( 'after_setup_theme', 'bourbon_setup' );


/*******************************************
* Custom image sizes
********************************************/
function bourbon_thumbnail_setup() {
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'bourbon_thumbnail_xlarge', 1280 );
    add_image_size( 'bourbon_thumbnail_large', 1000 );
    add_image_size( 'bourbon_thumbnail_medium', 800 );
}
add_action('init', 'bourbon_thumbnail_setup');


/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */

function bourbon_widgets_init() {

		register_sidebar( array(
			'name'          => __( 'Sidebar', 'bourbon' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<div class="columns large-12 medium-6 end"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h5 class="widget__title">',
			'after_title'   => '</h5>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer left', 'bourbon' ),
			'id'            => 'footer-1',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="footer-widget widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h5 class="widget__title">',
			'after_title'   => '</h5>',
		) );
		register_sidebar( array(
			'name'          => __( 'Footer center', 'bourbon' ),
			'id'            => 'footer-2',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="footer-widget widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h5 class="widget__title">',
			'after_title'   => '</h5>',
		) );
		register_sidebar( array(
			'name'          => __( 'Footer right', 'bourbon' ),
			'id'            => 'footer-3',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="footer-widget widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h5 class="widget__title">',
			'after_title'   => '</h5>',
		) );
}
add_action( 'widgets_init', 'bourbon_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function bourbon_scripts() {

	wp_enqueue_script( 'jquery' );

	wp_enqueue_style( 'bourbon-foundation-normalize', get_template_directory_uri() . '/bower_components/foundation/css/normalize.css' );
	wp_enqueue_style( 'bourbon-foundation', get_template_directory_uri() . '/bower_components/foundation/css/foundation.css' );
	wp_enqueue_style( 'bourbon-font-awesome', get_template_directory_uri() . '/bower_components/fontawesome/css/font-awesome.min.css' );
	wp_enqueue_style( 'bourbon-slick-css', get_template_directory_uri() . '/bower_components/slick.js/slick/slick.css' );
	wp_enqueue_style( 'bourbon-style', get_stylesheet_uri() );

	wp_enqueue_script( 'jquery-masonry' );
	wp_enqueue_script( 'bourbon-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'bourbon-modernizr', get_template_directory_uri() . '/bower_components/modernizr/modernizr.js');
	wp_enqueue_script( 'bourbon-fastclick', get_template_directory_uri() . '/bower_components/fastclick/lib/fastclick.js');
	wp_enqueue_script( 'bourbon-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	wp_enqueue_script( 'bourbon-foundation-min', get_template_directory_uri() . '/bower_components/foundation/js/foundation.min.js');
	wp_enqueue_script( 'bourbon-js', get_template_directory_uri() . '/js/bourbon.js', array('jquery'), false, true );
	wp_enqueue_script( 'bourbon-slick-js', get_template_directory_uri() . '/bower_components/slick.js/slick/slick.min.js', array('jquery'));
	wp_enqueue_script( 'bourbon-infinite-scroll', get_template_directory_uri() . '/js/jquery.infinitescroll.min.js', array('jquery') );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bourbon_scripts' );



/**
 *
 * REDUX
 * Enqueue ADMIN scripts and styles.
 */
function bourbon_scripts_admin() {

	wp_enqueue_style( 'bourbon-font-awesome-admin', get_template_directory_uri() . '/bower_components/fontawesome/css/font-awesome.min.css' );

	// Admin js (category upload media image)
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 	wp_enqueue_script( 'bourbon-uploadscript', get_stylesheet_directory_uri() . '/js/admin.js', array('jquery'), null, false );

}
add_action( 'admin_enqueue_scripts', 'bourbon_scripts_admin' );


function bourbon_addPanelCSS() {
    wp_register_style('bourbon-redux-custom-css', get_template_directory_uri() . '/css/adminka.css',
        array( 'redux-admin-css' ), // Be sure to include redux-admin-css so it's appended after the core css is applied
        time(),
        'all'
    );
    wp_enqueue_style('bourbon-redux-custom-css');
}
// This example assumes your opt_name is set to redux_demo, replace with your opt_name value
add_action( 'redux/page/burocrate_bourbon/enqueue', 'bourbon_addPanelCSS' );



/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Widgets
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';



// Remove Redux Ads
function bourbon_custom_admin_styles() {
?> <style type="text/css">
	.rAds { display: none !important; height: 0; width: 0; }
</style>
<?php }
add_action('admin_head', 'bourbon_custom_admin_styles');
