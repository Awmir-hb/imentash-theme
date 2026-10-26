<?php
/**
 * صفحه اصلی: چهار نوشته آخر وبلاگ (کارت‌های هم‌اندازه با عکس ۴:۳). موبایل: اسلایدر افقی.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_posts = get_posts(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'suppress_filters'    => false,
	)
);
if ( ! $ementash_posts ) {
	return;
}

$ementash_title = (string) ementash_get( 'blog_title' );
$ementash_text  = (string) ementash_get( 'blog_text' );
$ementash_label = (string) ementash_get( 'blog_link_label' );
// «همه مقاله‌ها» فقط وقتی برگه نوشته‌ها تعیین شده باشد.
$ementash_all_url = ementash_blog_url();
?>
<section class="em-section em-section--soft em-blog"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-blog-title"' : ''; ?>>
	<div class="em-container">
		<div class="em-head">
			<div>
				<?php if ( '' !== $ementash_title ) : ?>
					<h2 class="em-h2 em-blog__h2" id="em-blog-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-head__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
			<?php if ( '' !== $ementash_label && $ementash_all_url ) : ?>
				<a class="em-link em-blog__all" href="<?php echo esc_url( $ementash_all_url ); ?>"><?php echo ementash_bdi( $ementash_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
		</div>

		<ul class="em-posts">
			<?php
			foreach ( $ementash_posts as $ementash_post ) {
				get_template_part(
					'template-parts/blog/card',
					null,
					array(
						'post'  => $ementash_post,
						'sizes' => '(max-width: 767px) 260px, (max-width: 1000px) 50vw, 320px',
					)
				);
			}
			?>
		</ul>
	</div>
</section>
