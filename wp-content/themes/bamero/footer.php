	<footer class="site-footer">
		<div class="footer-inner">
			<div class="footer-brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="brand-mark" aria-hidden="true">ب</span>
					<span><?php bloginfo( 'name' ); ?></span>
				</a>
				<p><?php esc_html_e( 'رنگ و چسب بامرو؛ دسته‌بندی‌ها و اطلاعات ثبت‌شدهٔ محصولات را پیش از انتخاب بررسی کنید.', 'bamero' ); ?></p>
			</div>
			<nav class="footer-nav" aria-label="<?php esc_attr_e( 'فهرست پایین صفحه', 'bamero' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => 'bamero_primary_menu_fallback',
				) );
				?>
			</nav>
			<div class="footer-bottom">
				<span><?php echo esc_html( sprintf( __( '© %1$s %2$s', 'bamero' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) ); ?></span>
				<?php if ( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ) : ?>
					<span> · <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'حریم خصوصی', 'bamero' ); ?></a></span>
				<?php endif; ?>
			</div>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
