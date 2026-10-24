<?php
/**
 * صفحه اصلی: درباره ما (متن با رنگ‌گیری کلمه‌به‌کلمه هنگام اسکرول، عکس قاب‌دار، دکمه) + آمار.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_eyebrow = (string) ementash_get( 'home_about_eyebrow' );
$ementash_text    = (string) ementash_get( 'home_about_text' );
$ementash_image   = absint( ementash_get( 'home_about_image' ) );
$ementash_label   = (string) ementash_get( 'home_about_btn_label' );
$ementash_url     = (string) ementash_get( 'home_about_url' );
$ementash_url     = '' !== $ementash_url ? $ementash_url : ementash_about_url();
?>
<section class="em-section em-about"<?php echo '' !== $ementash_eyebrow ? ' aria-labelledby="em-about-title"' : ''; ?>>
	<div class="em-container">
		<div class="em-about__grid">
			<div class="em-about__body">
				<?php if ( '' !== $ementash_eyebrow ) : ?>
					<h2 class="em-eyebrow" id="em-about-title"><?php echo ementash_bdi( $ementash_eyebrow ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-about__text">
						<?php
						$ementash_out = array();
						foreach ( ementash_words( $ementash_text ) as $ementash_word ) {
							$ementash_out[] = '<span class="em-mw">' . ementash_bdi( $ementash_word ) . '</span>';
						}
						echo implode( ' ', $ementash_out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- هر کلمه escape شده.
						?>
					</p>
				<?php endif; ?>

				<?php if ( '' !== $ementash_label ) : ?>
					<a href="<?php echo esc_url( $ementash_url ); ?>" class="em-btn em-btn--dark">
						<?php echo ementash_bdi( $ementash_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="em-frame">
				<div class="em-frame__img">
					<?php
					if ( $ementash_image ) {
						echo wp_get_attachment_image(
							$ementash_image,
							'large',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 900px) 100vw, 560px',
							)
						);
					} else {
						echo '<span class="em-ph" aria-hidden="true"></span>';
					}
					?>
				</div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/stats' ); ?>
	</div>
</section>
