<?php
/**
 * درباره ما: متن شرکت = محتوای خود برگه (ویرایشگر)، عکس = تصویر شاخص برگه.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_content = trim( (string) get_the_content() );
$ementash_image   = get_post_thumbnail_id();
if ( '' === $ementash_content && ! $ementash_image ) {
	return;
}
?>
<section class="em-section em-story">
	<div class="em-container em-story__in<?php echo $ementash_image ? '' : ' em-story__in--full'; ?>">
		<?php if ( '' !== $ementash_content ) : ?>
			<div class="em-entry em-story__text">
				<?php echo ementash_bdi_html( apply_filters( 'the_content', $ementash_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی the_content؛ فقط bdi اضافه می‌شود. ?>
			</div>
		<?php endif; ?>
		<?php if ( $ementash_image ) : ?>
			<div class="em-story__img">
				<?php
				echo wp_get_attachment_image(
					$ementash_image,
					'large',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 900px) 100vw, 560px',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
