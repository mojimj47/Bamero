<?php
/** Main fallback template. */
get_header();
?>
<main id="main-content" tabindex="-1" class="content-width page-content">
	<?php
	$posts_page_id = (int) get_option( 'page_for_posts' );
	if ( is_home() && $posts_page_id ) {
		$index_title = get_the_title( $posts_page_id );
	} elseif ( is_home() ) {
		$index_title = get_bloginfo( 'name' );
	} else {
		$index_title = __( 'محتوایی پیدا نشد', 'bamero' );
	}
	?>
	<h1 class="page-title"><?php echo esc_html( $index_title ); ?></h1>
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<h2 class="entry-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h2>
				<div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div>
				<div class="post-content"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'هنوز محتوایی برای نمایش وجود ندارد.', 'bamero' ); ?></p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
