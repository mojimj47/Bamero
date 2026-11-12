<?php
/**
 * Front page template.
 *
 * @package Bamero
 */
get_header();
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<main id="main-content" tabindex="-1" class="content-width">
	<section class="hero" aria-labelledby="hero-title">
		<div class="hero-content">
			<p class="eyebrow"><?php esc_html_e( 'رنگ و چسب بامرو', 'bamero' ); ?></p>
			<h1 id="hero-title"><?php esc_html_e( 'رنگ و چسب موردنیازت را راحت‌تر پیدا کن.', 'bamero' ); ?></h1>
			<p><?php esc_html_e( 'از دسته‌بندی‌ها شروع کن یا نام محصول را جست‌وجو کن؛ اطلاعات هر کالا را پیش از انتخاب در صفحهٔ خودش ببین.', 'bamero' ); ?></p>
			<div class="hero-actions">
				<a class="button" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'رفتن به فروشگاه', 'bamero' ); ?></a>
				<a class="button-secondary" href="#latest-products"><?php esc_html_e( 'دیدن محصولات تازه', 'bamero' ); ?></a>
			</div>
		</div>
		<div class="hero-art" aria-hidden="true">
			<span class="hero-art-orbit"></span>
			<span class="hero-art-shape hero-art-shape--forest"></span>
			<span class="hero-art-shape hero-art-shape--lime"></span>
			<span class="hero-art-shape hero-art-shape--paper"></span>
		</div>
	</section>

	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<?php
		$product_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 3,
			'orderby'    => 'count',
			'order'      => 'DESC',
		) );
		?>
		<?php if ( ! is_wp_error( $product_categories ) && ! empty( $product_categories ) ) : ?>
			<section class="section" aria-labelledby="category-heading">
				<div class="section-heading">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'دسته‌بندی‌ها', 'bamero' ); ?></p>
						<h2 id="category-heading"><?php esc_html_e( 'از کجا شروع کنیم؟', 'bamero' ); ?></h2>
					</div>
					<a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'همه محصولات', 'bamero' ); ?></a>
				</div>
				<div class="category-list">
					<?php
					foreach ( $product_categories as $category ) :
						$category_url = get_term_link( $category );
						if ( is_wp_error( $category_url ) ) {
							continue;
						}
					?>
						<a class="category-card" href="<?php echo esc_url( $category_url ); ?>">
							<strong><?php echo esc_html( $category->name ); ?></strong>
							<span aria-hidden="true">←</span>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<section id="latest-products" class="section" aria-labelledby="products-heading">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'تازه‌های فروشگاه', 'bamero' ); ?></p>
					<h2 id="products-heading"><?php esc_html_e( 'محصولات تازه', 'bamero' ); ?></h2>
				</div>
				<a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'مشاهده همه', 'bamero' ); ?></a>
			</div>
			<?php
			$product_markup = trim( do_shortcode( '[products limit="4" columns="4" orderby="date" order="DESC" visibility="visible"]' ) );
			if ( '' !== $product_markup ) {
				echo $product_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce generates escaped product markup.
			} else {
				?>
				<p><?php esc_html_e( 'محصولی برای نمایش ثبت نشده است. پس از افزودن محصولات در پیشخوان، این بخش خودکار تکمیل می‌شود.', 'bamero' ); ?></p>
				<?php
			}
			?>
		</section>
	<?php else : ?>
		<section class="section" aria-labelledby="setup-heading">
			<h2 id="setup-heading"><?php esc_html_e( 'فروشگاه آماده تکمیل است', 'bamero' ); ?></h2>
			<p><?php esc_html_e( 'برای فعال‌شدن دسته‌بندی‌ها، محصولات، سبد خرید و پرداخت، افزونه WooCommerce را نصب و پیکربندی کنید.', 'bamero' ); ?></p>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
