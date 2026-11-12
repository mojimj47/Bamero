<?php
/** Not-found template. WordPress returns a 404 status for this template. */
get_header();
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<main id="main-content" tabindex="-1" class="content-width page-content">
	<section class="entry">
		<p class="eyebrow"><?php esc_html_e( 'خطای ۴۰۴', 'bamero' ); ?></p>
		<h1 class="entry-title"><?php esc_html_e( 'این صفحه پیدا نشد.', 'bamero' ); ?></h1>
		<p><?php esc_html_e( 'نشانی را بررسی کنید یا برای دیدن محصولات به فروشگاه بروید.', 'bamero' ); ?></p>
		<a class="button" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'رفتن به فروشگاه', 'bamero' ); ?></a>
	</section>
</main>
<?php get_footer(); ?>
