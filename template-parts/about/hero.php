<?php
/**
 * درباره ما: سربرگ تصویری. پس‌زمینه = ویدئوی متاباکس برگه، وگرنه تصویر شاخص برگه؛ آمار Customizer روی لبه پایین.
 * بدون ویدئو و تصویر شاخص همان زمینه سرمه‌ای با حلقه‌های برند می‌ماند.
 *
 * ویدئو در assets/js/about.js و فقط برای اندازه صفحه مناسب ساخته می‌شود (مثل هیروی صفحه اصلی).
 * بدون JS تصویر شاخص (اگر باشد) از داخل noscript نمایش داده می‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_image = get_post_thumbnail_id();
$ementash_video = ementash_about_hero_videos( get_the_ID() );

$ementash_class = 'em-ahero';
if ( $ementash_image || $ementash_video ) {
	$ementash_class .= ' em-ahero--img';
}
if ( $ementash_video ) {
	$ementash_class .= ' em-ahero--video';
}
?>
<section class="<?php echo esc_attr( $ementash_class ); ?>">
	<?php if ( $ementash_video ) : ?>
		<div class="em-ahero__media" aria-hidden="true" data-em-ahero-video data-em-desktop="<?php echo esc_url( $ementash_video['desktop'] ); ?>"<?php echo isset( $ementash_video['mobile'] ) ? ' data-em-mobile="' . esc_url( $ementash_video['mobile'] ) . '"' : ''; ?>>
			<?php if ( $ementash_image ) : ?>
				<noscript><?php echo wp_get_attachment_image( $ementash_image, 'full', false, array( 'alt' => '', 'sizes' => '100vw' ) ); ?></noscript>
			<?php endif; ?>
		</div>
		<button type="button" class="em-ahero__toggle" data-em-ahero-toggle hidden aria-label="<?php esc_attr_e( 'توقف ویدئو', 'ementash' ); ?>" data-em-label-pause="<?php esc_attr_e( 'توقف ویدئو', 'ementash' ); ?>" data-em-label-play="<?php esc_attr_e( 'پخش ویدئو', 'ementash' ); ?>">
			<?php
			echo ementash_icon( 'pause', 'em-ahero__ic-pause' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG فیلترشده با kses.
			echo ementash_icon( 'play', 'em-ahero__ic-play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</button>
	<?php elseif ( $ementash_image ) : ?>
		<div class="em-ahero__media" aria-hidden="true">
			<?php
			// تصویر بالای صفحه و احتمالاً LCP: بدون lazy.
			echo wp_get_attachment_image(
				$ementash_image,
				'full',
				false,
				array(
					'alt'           => '',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
					'sizes'         => '100vw',
				)
			);
			?>
		</div>
	<?php endif; ?>

	<?php
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title'  => get_the_title(),
			'desc'   => ementash_page_subtitle(),
			'crumbs' => ementash_page_crumbs(),
		)
	);
	?>

	<?php
	// نوار آمار روی لبه پایین سربرگ می‌نشیند؛ اگر آماری نباشد جعبه‌اش هم چاپ نمی‌شود.
	ob_start();
	get_template_part( 'template-parts/stats' );
	$ementash_stats = trim( (string) ob_get_clean() );
	if ( '' !== $ementash_stats ) {
		echo '<div class="em-ahero__stats">' . $ementash_stats . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی escape‌شده template-parts/stats.php.
	}
	?>
</section>
