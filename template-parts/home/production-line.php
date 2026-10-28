<?php
/**
 * صفحه اصلی: خط تولید (نقشه نمادین کارخانه با انیمیشن ترسیم + ۴ مرحله از Customizer).
 *
 * برچسب‌های روی نقشه از کد مراحل خوانده می‌شوند: مرحله ۱ دو مخزن دارد («T-101 / 102»)،
 * مرحله ۲ ستون فرآوری، مرحله ۳ فشارسنج و مرحله ۴ سیلوی بسته‌بندی.
 * انیمیشن تا رسیدن سکشن به صفحه در assets/js/home.js متوقف می‌ماند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_title = (string) ementash_get( 'pl_title' );
$ementash_text  = (string) ementash_get( 'pl_text' );

$ementash_steps = array();
for ( $ementash_n = 1; $ementash_n <= 4; $ementash_n++ ) {
	$ementash_steps[ $ementash_n ] = array(
		'code'  => (string) ementash_get( 'pl_' . $ementash_n . '_code' ),
		'title' => (string) ementash_get( 'pl_' . $ementash_n . '_title' ),
		'text'  => (string) ementash_get( 'pl_' . $ementash_n . '_text' ),
	);
}

// «T-101 / 102» ← T-101 و T-102 برای دو مخزن.
$ementash_tanks = array_map( 'trim', explode( '/', $ementash_steps[1]['code'], 2 ) ) + array( '', '' );
if ( preg_match( '/^\d+$/', $ementash_tanks[1] ) && preg_match( '/^(.*?)\d+$/', $ementash_tanks[0], $ementash_m ) ) {
	$ementash_tanks[1] = $ementash_m[1] . $ementash_tanks[1];
}
$ementash_labels = array(
	array( 54, 328, '2.3s', $ementash_tanks[0] ),
	array( 284, 458, '2.4s', $ementash_tanks[1] ),
	array( 492, 178, '2.5s', $ementash_steps[2]['code'] ),
	array( 650, 296, '2.6s', $ementash_steps[4]['code'] ),
	array( 374, 214, '2.7s', $ementash_steps[3]['code'] ),
);
?>
<section class="em-section em-pl"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-pl-title"' : ''; ?> data-em-pl>
	<div class="em-container">
		<?php // دسکتاپ: سربرگ بالای ستون مراحل کنار نقشه؛ موبایل: سربرگ، نقشه، مراحل (grid-template-areas). ?>
		<div class="em-pl__grid">
			<?php if ( '' !== $ementash_title || '' !== $ementash_text ) : ?>
				<div class="em-pl__head">
					<?php if ( '' !== $ementash_title ) : ?>
						<h2 class="em-h2 em-pl__h2" id="em-pl-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $ementash_text ) : ?>
						<p class="em-pl__intro"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php // کادر نقشه به خود شکل بریده شده (طرح ۹۰۰×۷۲۰ بود و بالا و راستش خالی). ?>
			<svg class="em-pl__map" dir="ltr" viewBox="0 100 740 620" data-em-viewbox-mobile="0 120 740 600" role="img" aria-label="<?php esc_attr_e( 'شمای نمادین خط تولید کارخانه', 'ementash' ); ?>">
				<path class="em-pl__d" pathLength="1" style="animation-delay:.2s" d="M10 680 H720"/>
				<path class="em-pl__d2" pathLength="1" style="animation-delay:.4s" d="M10 698 H720 M10 710 H720"/>
				<ellipse class="em-pl__d" pathLength="1" style="animation-delay:.3s" cx="140" cy="360" rx="90" ry="20"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:.35s" d="M50 360 V680 M230 360 V680 M50 470 H230 M50 580 H230"/>
				<path class="em-pl__d2" pathLength="1" style="animation-delay:.8s" d="M240 680 V350 M252 680 V350 M240 650 H252 M240 620 H252 M240 590 H252 M240 560 H252 M240 530 H252 M240 500 H252 M240 470 H252 M240 440 H252 M240 410 H252 M240 380 H252"/>
				<ellipse class="em-pl__d" pathLength="1" style="animation-delay:.5s" cx="330" cy="480" rx="50" ry="12"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:.55s" d="M280 480 V680 M380 480 V680 M280 580 H380"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:.6s" d="M430 680 V190 A25 25 0 0 1 480 190 V680"/>
				<path class="em-pl__d2" pathLength="1" style="animation-delay:1.2s" d="M436 240 H474 M436 300 H474 M436 360 H474 M436 420 H474 M436 480 H474 M436 540 H474 M436 600 H474"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:1.1s" d="M416 330 H494 M416 500 H494 M416 330 V318 M494 330 V318 M416 500 V488 M494 500 V488"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:.9s" d="M540 300 H640 V440 L600 510 V560 H580 V510 L540 440 Z"/>
				<path class="em-pl__d2" pathLength="1" style="animation-delay:1.2s" d="M548 440 V680 M632 440 V680 M548 520 L632 600 M632 520 L548 600"/>
				<rect class="em-pl__d2" pathLength="1" style="animation-delay:1.4s" x="560" y="636" width="60" height="44"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:1.5s" d="M230 640 H280"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:1.55s" d="M380 650 H430"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:1.6s" d="M455 165 V120 H590 V300"/>
				<path class="em-pl__d" pathLength="1" style="animation-delay:1.65s" d="M140 340 V290 H330 V468"/>
				<circle class="em-pl__d" pathLength="1" style="animation-delay:1.7s" cx="330" cy="210" r="34"/>
				<path class="em-pl__d2" pathLength="1" style="animation-delay:1.9s" d="M330 244 V290 M300 210 H308 M352 210 H360 M330 180 V188"/>
				<path class="em-pl__fl" d="M230 640 H280"/>
				<path class="em-pl__fl" d="M380 650 H430"/>
				<path class="em-pl__fl" d="M455 165 V120 H590 V300"/>
				<path class="em-pl__fl" d="M140 340 V290 H330 V468"/>
				<path class="em-pl__fl em-pl__fl--dim" d="M10 698 H720"/>
				<g class="em-pl__fi" style="animation-delay:2.2s">
					<path class="em-pl__nd" d="M330 210 L330 186"/>
					<circle class="em-pl__bl" cx="455" cy="260" r="4"/>
					<circle class="em-pl__bl" cx="455" cy="450" r="4" style="animation-delay:.7s"/>
				</g>
				<g class="em-pl__fi" style="animation-delay:2.4s">
					<circle class="em-pl__pd" cx="586" cy="566" r="2.5"/>
					<circle class="em-pl__pd" cx="594" cy="566" r="2" style="animation-delay:.5s"/>
					<circle class="em-pl__pd" cx="590" cy="566" r="2.5" style="animation-delay:1s"/>
				</g>
				<?php foreach ( $ementash_labels as $ementash_label ) : ?>
					<?php if ( '' !== $ementash_label[3] ) : ?>
						<text class="em-pl__lb" x="<?php echo esc_attr( $ementash_label[0] ); ?>" y="<?php echo esc_attr( $ementash_label[1] ); ?>" style="animation-delay:<?php echo esc_attr( $ementash_label[2] ); ?>"><?php echo esc_html( $ementash_label[3] ); ?></text>
					<?php endif; ?>
				<?php endforeach; ?>
			</svg>

			<ol class="em-pl__steps">
				<?php foreach ( $ementash_steps as $ementash_step ) : ?>
					<?php
					if ( '' === $ementash_step['title'] ) {
						continue;
					}
					?>
					<li class="em-pl__step">
						<span class="em-pl__code"><?php echo ementash_code( $ementash_step['code'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<h3 class="em-pl__title"><?php echo ementash_bdi( $ementash_step['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
							<?php if ( '' !== $ementash_step['text'] ) : ?>
								<p class="em-pl__text"><?php echo ementash_bdi( $ementash_step['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>
