<?php
/**
 * فهرست ساده نوشته‌ها با سربرگ؛ برای index، آرشیو و جستجو تا طراحی فاز ۶.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

if ( is_home() && ! is_front_page() ) {
	$ementash_title = single_post_title( '', false );
} elseif ( is_search() ) {
	/* translators: %s: عبارت جستجو */
	$ementash_title = sprintf( __( 'نتایج جستجو برای «%s»', 'ementash' ), get_search_query() );
} elseif ( is_archive() ) {
	$ementash_title = wp_strip_all_tags( get_the_archive_title() );
} else {
	$ementash_title = get_bloginfo( 'name' );
}

get_template_part( 'template-parts/page-head', null, array( 'title' => $ementash_title ) );
?>
<div class="em-section">
	<div class="em-container em-entry">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'em-entry__item' ); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'مطلبی پیدا نشد.', 'ementash' ); ?></p>
		<?php endif; ?>
	</div>
</div>
