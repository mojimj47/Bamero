<?php
/**
 * Bamero theme setup and compatibility.
 *
 * @package Bamero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Set up theme features and menus. */
function bamero_setup() {
	load_theme_textdomain( 'bamero', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 96,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 640,
		'single_image_width'    => 1200,
		'product_grid'          => array(
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 4,
			'default_rows'    => 3,
			'min_rows'        => 1,
			'max_rows'        => 8,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'فهرست اصلی', 'bamero' ),
		'footer'  => __( 'فهرست پایین صفحه', 'bamero' ),
	) );

	add_image_size( 'bamero-product-card', 640, 800, true );
}
add_action( 'after_setup_theme', 'bamero_setup' );


/** Give WooCommerce's existing breadcrumb navigation a Persian accessible name. */
function bamero_breadcrumb_defaults( $defaults ) {
	if ( isset( $defaults['wrap_before'] ) && is_string( $defaults['wrap_before'] ) ) {
		$defaults['wrap_before'] = str_replace(
			'aria-label="Breadcrumb"',
			'aria-label="' . esc_attr__( 'مسیر راهنما', 'bamero' ) . '"',
			$defaults['wrap_before']
		);
	}

	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'bamero_breadcrumb_defaults' );


/** Open the theme's main landmark for WooCommerce archive and single-product templates. */
function bamero_woocommerce_wrapper_start() {
	echo '<div id="primary" class="content-area"><main id="main-content" tabindex="-1" class="content-width page-content site-main" role="main">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, theme-owned wrapper markup.
}

/** Close the theme's WooCommerce main landmark. */
function bamero_woocommerce_wrapper_end() {
	echo '</main></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, theme-owned wrapper markup.
}

/** Replace WooCommerce's default main wrapper using its documented theme hooks. */
function bamero_woocommerce_setup_wrappers() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'bamero_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'bamero_woocommerce_wrapper_end', 10 );
}
add_action( 'after_setup_theme', 'bamero_woocommerce_setup_wrappers', 20 );

/** Load the small, dependency-free theme script. */
function bamero_enqueue_assets() {
	$theme = wp_get_theme();
	$version = $theme->get( 'Version' );
	wp_enqueue_style( 'bamero-style', get_stylesheet_uri(), array(), $version );
	wp_enqueue_script( 'bamero-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), $version, true );
}
add_action( 'wp_enqueue_scripts', 'bamero_enqueue_assets' );

/** Render a useful front-end menu when the merchant has not assigned one yet. */
function bamero_primary_menu_fallback() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	?>
	<ul class="menu">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'bamero' ); ?></a></li>
		<li><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'فروشگاه', 'bamero' ); ?></a></li>
	</ul>
	<?php
}

/** Use the store's own product catalog as the header search target when available. */
function bamero_product_search_form() {
	?>
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="bamero-search-field"><?php esc_html_e( 'جست‌وجوی محصولات', 'bamero' ); ?></label>
		<input id="bamero-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جست‌وجوی محصول…', 'bamero' ); ?>">
		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<input type="hidden" name="post_type" value="product">
		<?php endif; ?>
		<button type="submit"><?php esc_html_e( 'جست‌وجو', 'bamero' ); ?></button>
	</form>
	<?php
}
