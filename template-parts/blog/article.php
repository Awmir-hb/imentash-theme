<?php
/**
 * متن نوشته وبلاگ: تصویر شاخص (بدون lazy، اولین تصویر صفحه)، محتوا در ستون خواندن و برچسب‌ها.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_tags = get_the_tags();
?>
<article <?php post_class( 'em-section em-article' ); ?>>
	<div class="em-container">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="em-article__img">
				<?php
				// alt از کتابخانه رسانه؛ width/height خود وردپرس جای عکس را از قبل نگه می‌دارد.
				the_post_thumbnail(
					'large',
					array(
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'sizes'         => '(max-width: 1000px) 100vw, 960px',
					)
				);
				?>
			</figure>
		<?php endif; ?>

		<div class="em-article__body em-entry">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="em-article__pages" aria-label="' . esc_attr__( 'صفحه‌های نوشته', 'ementash' ) . '">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<?php if ( $ementash_tags ) : ?>
			<div class="em-article__tags">
				<h2 class="screen-reader-text"><?php esc_html_e( 'برچسب‌ها', 'ementash' ); ?></h2>
				<ul class="em-tags">
					<?php foreach ( $ementash_tags as $ementash_tag ) : ?>
						<li><a class="em-tag" href="<?php echo esc_url( get_tag_link( $ementash_tag ) ); ?>" rel="tag"><?php echo ementash_bdi( $ementash_tag->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</article>
