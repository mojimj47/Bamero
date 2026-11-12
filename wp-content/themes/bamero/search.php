<?php
/** Search results. */
get_header();
?>
<main id="main-content" tabindex="-1" class="content-width page-content">
	<header class="page-header">
		<h1 class="page-title"><?php echo esc_html( sprintf( __( 'نتایج جست‌وجو برای: %s', 'bamero' ), get_search_query() ) ); ?></h1>
	</header>
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="post-content"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'نتیجه‌ای پیدا نشد. عبارت دیگری را جست‌وجو کنید.', 'bamero' ); ?></p>
		<?php bamero_product_search_form(); ?>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
