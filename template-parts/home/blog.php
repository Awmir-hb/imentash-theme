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
$ementash_posts_page = absint( get_option( 'page_for_posts' ) );
$ementash_all_url    = $ementash_posts_page ? get_permalink( $ementash_posts_page ) : '';
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
			foreach ( $ementash_posts as $ementash_post ) :
				$ementash_cats = get_the_category( $ementash_post->ID );
				$ementash_cat  = $ementash_cats ? $ementash_cats[0]->name : '';
				?>
				<li class="em-posts__item">
					<a class="em-post" href="<?php echo esc_url( get_permalink( $ementash_post ) ); ?>">
						<span class="em-post__img">
							<?php
							if ( has_post_thumbnail( $ementash_post ) ) {
								// تزئینی: عنوان مقاله کنار عکس آمده است.
								echo get_the_post_thumbnail(
									$ementash_post,
									'ementash-card',
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => '(max-width: 767px) 260px, (max-width: 1000px) 50vw, 320px',
									)
								);
							} else {
								echo '<span class="em-ph" aria-hidden="true"></span>';
							}
							?>
						</span>
						<div class="em-post__body">
							<span class="em-post__meta">
								<?php if ( '' !== $ementash_cat ) : ?>
									<span class="em-post__cat"><?php echo ementash_bdi( $ementash_cat ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>
								<time datetime="<?php echo esc_attr( get_the_date( 'c', $ementash_post ) ); ?>"><?php echo ementash_bdi( get_the_date( '', $ementash_post ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></time>
							</span>
							<h3 class="em-post__title"><?php echo ementash_bdi( get_the_title( $ementash_post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
							<span class="em-post__more" aria-hidden="true">
								<?php esc_html_e( 'خواندن مقاله', 'ementash' ); ?>
								<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
