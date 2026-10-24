<?php
/**
 * صفحه اصلی: هیرو (رسانه پس‌زمینه، تیتر کلمه‌به‌کلمه، کلمات چرخان، دو دکمه).
 *
 * پوستر در HTML است تا بدون JS و برای کاربرانی که حرکت را کم کرده‌اند دیده شود؛
 * ویدئو یا گیف در assets/js/home.js و فقط برای اندازه صفحه مناسب بارگذاری می‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_poster = absint( ementash_get( 'hero_poster' ) );
$ementash_media  = array();
foreach ( array(
	'desktop' => 'hero_media',
	'mobile'  => 'hero_media_mobile',
) as $ementash_key => $ementash_setting ) {
	$ementash_id   = absint( ementash_get( $ementash_setting ) );
	$ementash_kind = $ementash_id ? ementash_media_kind( $ementash_id ) : '';
	if ( $ementash_kind ) {
		$ementash_media[ 'data-em-' . $ementash_key ]           = wp_get_attachment_url( $ementash_id );
		$ementash_media[ 'data-em-' . $ementash_key . '-type' ] = $ementash_kind;
	}
}
if ( $ementash_poster ) {
	$ementash_media['data-em-poster'] = '1';
}

$ementash_lines = ementash_get_lines( 'hero_title' );
if ( ! $ementash_lines ) {
	$ementash_lines = array( get_bloginfo( 'name' ) );
}
$ementash_words_lead = (string) ementash_get( 'hero_words_lead' );
$ementash_words      = ementash_get_lines( 'hero_words' );
$ementash_text       = (string) ementash_get( 'hero_text' );

$ementash_buttons = array(
	array(
		'label' => (string) ementash_get( 'hero_btn1_label' ),
		'url'   => (string) ementash_get( 'hero_btn1_url' ),
		'auto'  => ementash_shop_url(),
		'class' => 'em-btn em-btn--light',
		'icon'  => 'arrow',
	),
	array(
		'label' => (string) ementash_get( 'hero_btn2_label' ),
		'url'   => (string) ementash_get( 'hero_btn2_url' ),
		'auto'  => ementash_contact_url(),
		'class' => 'em-btn em-btn--ghost',
		'icon'  => 'quote',
	),
);
?>
<section class="em-hero" aria-labelledby="em-hero-title">
	<div class="em-hero__media" aria-hidden="true" data-em-hero-media
		<?php
		foreach ( $ementash_media as $ementash_attr => $ementash_value ) {
			printf( ' %s="%s"', esc_attr( $ementash_attr ), 'data-em-desktop' === $ementash_attr || 'data-em-mobile' === $ementash_attr ? esc_url( $ementash_value ) : esc_attr( $ementash_value ) );
		}
		?>
	>
		<?php
		if ( $ementash_poster ) {
			echo wp_get_attachment_image(
				$ementash_poster,
				'full',
				false,
				array(
					'class'         => 'em-hero__poster',
					'alt'           => '',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
					'sizes'         => '100vw',
				)
			);
		}
		?>
	</div>
	<div class="em-hero__shade" aria-hidden="true"></div>

	<div class="em-hero__in">
		<div class="em-hero__body">
			<h1 class="em-hero__title" id="em-hero-title">
				<?php
				// تأخیر هر کلمه مطابق طراحی: ۰٫۱ ثانیه بین کلمه‌ها و ۰٫۰۵ ثانیه اضافه برای هر خط.
				$ementash_index = 0;
				foreach ( $ementash_lines as $ementash_line_no => $ementash_line ) {
					echo '<span class="em-hero__line">';
					$ementash_out = array();
					foreach ( ementash_words( $ementash_line ) as $ementash_word ) {
						$ementash_out[] = sprintf(
							'<span class="em-w"><span class="em-wi" style="--em-d:%1$.2Fs">%2$s</span></span>',
							0.35 + 0.1 * $ementash_index + 0.05 * $ementash_line_no,
							esc_html( $ementash_word )
						);
						++$ementash_index;
					}
					echo implode( ' ', $ementash_out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- هر کلمه بالا escape شده.
					echo '</span> ';
				}
				?>
			</h1>

			<?php if ( $ementash_words ) : ?>
				<p class="em-hero__words em-rise">
					<?php if ( '' !== $ementash_words_lead ) : ?>
						<span class="em-hero__lead"><?php echo esc_html( $ementash_words_lead ); ?></span>
					<?php endif; ?>
					<span class="em-hero__rot" aria-hidden="true">
						<span class="em-hero__track" data-em-rotate>
							<?php foreach ( $ementash_words as $ementash_word ) : ?>
								<span><?php echo esc_html( $ementash_word ); ?></span>
							<?php endforeach; ?>
						</span>
					</span>
					<span class="screen-reader-text"><?php echo esc_html( implode( __( '، ', 'ementash' ), $ementash_words ) ); ?></span>
				</p>
			<?php endif; ?>

			<?php if ( '' !== $ementash_text ) : ?>
				<p class="em-hero__text em-rise"><?php echo esc_html( $ementash_text ); ?></p>
			<?php endif; ?>

			<div class="em-hero__actions em-rise">
				<?php
				foreach ( $ementash_buttons as $ementash_btn ) {
					if ( '' === $ementash_btn['label'] ) {
						continue;
					}
					$ementash_icon = ementash_icon( $ementash_btn['icon'], 'em-btn__icon' );
					printf(
						'<a href="%1$s" class="%2$s">%3$s%4$s</a>',
						esc_url( '' !== $ementash_btn['url'] ? $ementash_btn['url'] : $ementash_btn['auto'] ),
						esc_attr( $ementash_btn['class'] ),
						// آیکون «استعلام» قبل از متن و فلش بعد از متن، مطابق طراحی.
						'quote' === $ementash_btn['icon'] ? $ementash_icon . esc_html( $ementash_btn['label'] ) : esc_html( $ementash_btn['label'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'arrow' === $ementash_btn['icon'] ? $ementash_icon : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				}
				?>
			</div>
		</div>
	</div>
</section>
