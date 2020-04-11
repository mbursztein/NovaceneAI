<?php
/**
 * Arctic functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Arctic Black
 */

/**
 * Arctic only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

if ( ! function_exists( 'arctic_black_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function arctic_black_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Arctic, use a find and replace
	 * to change 'arctic-black' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'arctic-black' );

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
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	set_post_thumbnail_size( 1280, 842, array( 'center', 'top' ) );

	add_image_size( 'arctic-large', 1600, 1600, array( 'center', 'top' ) );

	// Set the default content width.
	$GLOBALS['content_width'] = 780;

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		/** Primary (menu location) */
		'menu-1' => esc_html__( 'Primary', 'arctic-black' ),
		/** SOcial Link (menu location) */
		'menu-2' => esc_html__( 'Social Link', 'arctic-black' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'assets/css/editor-style.min.css' ) );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Gutenberg
	add_theme_support( 'align-wide' );

}
endif;
add_action( 'after_setup_theme', 'arctic_black_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function arctic_black_content_width() {

	$content_width = $GLOBALS['content_width'];

	$GLOBALS['content_width'] = apply_filters( 'arctic_black_content_width', $content_width );

}
add_action( 'template_redirect', 'arctic_black_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function arctic_black_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'arctic-black' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'arctic-black' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'arctic-black' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__( 'Add widgets here.', 'arctic-black' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'arctic-black' ),
		'id'            => 'sidebar-3',
		'description'   => esc_html__( 'Add widgets here.', 'arctic-black' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	if( function_exists( 'wpiw_init' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Instagram widget', 'arctic-black' ),
			'id'            => 'sidebar-4',
			'description'   => esc_html__( 'Add widgets here.', 'arctic-black' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}

}
add_action( 'widgets_init', 'arctic_black_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function arctic_black_scripts() {

	$rtl = ( is_rtl() ) ? '-rtl' : '';

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_dequeue_style( 'contact-form-7' );

	/** Styles */
	wp_enqueue_style( "arctic-style{$rtl}", get_theme_file_uri( "/style{$rtl}{$suffix}.css" ) );

	/** lt IE 9 script */
	wp_enqueue_script( 'html5shiv', get_theme_file_uri( "/assets/js/ie/html5shiv$suffix.js" ), array(), '3.7.3' );
	wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'respond', get_theme_file_uri( "/assets/js/ie/respond$suffix.js" ), array(), '1.4.2' );
	wp_script_add_data( 'respond', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'nwmatcher', get_theme_file_uri( "/assets/js/ie/nwmatcher$suffix.js" ), array(), '1.4.2' );
	wp_script_add_data( 'nwmatcher', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'selectivizr', get_theme_file_uri( "/assets/js/ie/selectivizr$suffix.js" ), array(), '1.0.2' );
	wp_script_add_data( 'selectivizr', 'conditional', 'lt IE 9' );

	/** jQuery plugins */
	wp_enqueue_script( 'jquery-fitvids', get_template_directory_uri() . '/assets/js/fitvids/jquery.fitvids.min.js', array( 'jquery' ), '1.2.0', true );
	wp_enqueue_script( 'jquery-slick', get_template_directory_uri() . '/assets/js/slick/slick.min.js', array( 'jquery' ), '1.8.1', true );

	/** Theme script */
	wp_enqueue_script( 'arctic-script', get_theme_file_uri( "/assets/js/frontend$suffix.js" ), array( 'jquery' ), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
add_action( 'wp_enqueue_scripts', 'arctic_black_scripts' );


/**
 * Arctic vendor script config.
 */
require get_template_directory() . '/inc/vendor/vendor.php';

/**
 * Custom helper functions.
 */
require get_template_directory() . '/inc/utility.php';

/**
 * Widgets additions.
 */
require get_template_directory() . '/inc/widgets/widgets.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Svg icons.
 */
require get_template_directory() . '/inc/icons.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

