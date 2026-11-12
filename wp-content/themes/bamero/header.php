<?php
/** Site header. */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'رفتن به محتوای اصلی', 'bamero' ); ?></a>
<div class="site-shell">
	<header class="site-header">
		<div class="header-inner">
			<div class="brand">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a class="brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="brand-mark" aria-hidden="true">ب</span>
						<span><?php bloginfo( 'name' ); ?></span>
					</a>
				<?php endif; ?>
			</div>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-label-open="<?php esc_attr_e( 'باز کردن فهرست', 'bamero' ); ?>" data-label-close="<?php esc_attr_e( 'بستن فهرست', 'bamero' ); ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'باز کردن فهرست', 'bamero' ); ?></span>
				<span aria-hidden="true">☰</span>
			</button>
			<nav id="primary-navigation" class="primary-nav" aria-label="<?php esc_attr_e( 'فهرست اصلی', 'bamero' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'bamero_primary_menu_fallback',
				) );
				?>
			</nav>
			<div class="header-actions">
				<?php bamero_product_search_form(); ?>
				<?php
				$bamero_mini_cart_available = class_exists( 'WooCommerce' )
					&& class_exists( 'WP_Block_Type_Registry' )
					&& WP_Block_Type_Registry::get_instance()->is_registered( 'woocommerce/mini-cart' );
				if ( $bamero_mini_cart_available ) :
					?>
					<div class="bamero-mini-cart">
						<?php echo do_blocks( '<!-- wp:woocommerce/mini-cart /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Render the registered WooCommerce Mini Cart block. ?>
					</div>
				<?php elseif ( function_exists( 'wc_get_cart_url' ) ) : ?>
					<a class="header-action cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'سبد خرید', 'bamero' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>
