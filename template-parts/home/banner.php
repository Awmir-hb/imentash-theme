<?php
/**
 * صفحه اصلی: بنر تصویری تمام‌عرض (عکس دوتون سرمه‌ای + تیتر بزرگ + دکمه).
 * عکس با اسکرول کمی جابه‌جا می‌شود (فقط مرورگرهای دارای view()؛ با کاهش حرکت ثابت).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_image   = absint( ementash_get( 'banner_image' ) );
$ementash_eyebrow = (string) ementash_get( 'banner_eyebrow' );
$ementash_lines   = ementash_get_lines( 'banner_title' );
$ementash_text    = (string) ementash_get( 'banner_text' );
$ementash_label   = (string) ementash_get( 'banner_btn_label' );
$ementash_url     = (string) ementash_get( 'banner_btn_url' );
if ( '' === $ementash_url ) {
	$ementash_url = ementash_contact_url();
}

if ( ! $ementash_image && ! $ementash_lines ) {
	return;
}
?>
<section class="em-banner"<?php echo $ementash_lines ? ' aria-labelledby="em-banner-title"' : ''; ?>>
	<div class="em-duo em-banner__media" aria-hidden="true">
		<?php
		if ( $ementash_image ) {
			echo wp_get_attachment_image(
				$ementash_image,
				'full',
				false,
				array(
					'alt'     => '',
					'loading' => 'lazy',
					'sizes'   => '100vw',
					'class'   => 'em-banner__img',
				)
			);
		}
		?>
	</div>
	<span class="em-banner__shade" aria-hidden="true"></span>

	<div class="em-container em-banner__in">
		<div class="em-banner__body">
			<?php if ( '' !== $ementash_eyebrow ) : ?>
				<p class="em-eyebrow em-banner__eyebrow"><?php echo ementash_bdi( $ementash_eyebrow ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( $ementash_lines ) : ?>
				<h2 class="em-h2 em-banner__title" id="em-banner-title">
					<?php // هر خط یک span؛ انیمیشن ورود تیتر هر خط را یک واحد می‌گیرد. ?>
					<?php foreach ( $ementash_lines as $ementash_line ) : ?>
						<span class="em-banner__line"><?php echo ementash_bdi( $ementash_line ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endforeach; ?>
				</h2>
			<?php endif; ?>
			<?php if ( '' !== $ementash_text ) : ?>
				<p class="em-banner__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( '' !== $ementash_label && '' !== $ementash_url ) : ?>
				<a class="em-btn em-btn--light em-banner__btn" href="<?php echo esc_url( $ementash_url ); ?>">
					<?php echo ementash_bdi( $ementash_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
