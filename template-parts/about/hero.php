<?php
/**
 * درباره ما: سربرگ تصویری. پس‌زمینه = تصویر شاخص برگه، آمار Customizer روی لبه پایین.
 * بدون تصویر شاخص همان زمینه سرمه‌ای با حلقه‌های برند می‌ماند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_image = get_post_thumbnail_id();
?>
<section class="em-ahero<?php echo $ementash_image ? ' em-ahero--img' : ''; ?>">
	<?php if ( $ementash_image ) : ?>
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
