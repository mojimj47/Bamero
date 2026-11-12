<?php
/** Archive fallback; WooCommerce keeps control of its own product templates. */
get_header();
?>
<main id="main-content" tabindex="-1" class="content-width page-content">
	<header class="page-header">
		<h1 class="page-title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
				<div class="post-content"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'موردی برای نمایش پیدا نشد.', 'bamero' ); ?></p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
