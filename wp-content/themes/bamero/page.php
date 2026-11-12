<?php
/** Default page template. */
get_header();
?>
<main id="main-content" tabindex="-1" class="content-width page-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="post-content"><?php the_content(); ?></div>
			<?php wp_link_pages(); ?>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
