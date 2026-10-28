<?php
/**
 * فوتر خود قالب: نوار دعوت (نیمی بیرون از فوتر)، معرفی و برچسب گواهی‌ها، دسته‌های محصولات،
 * دسترسی سریع، دفاتر (تلفن، فکس)، ساعات کاری و پیام‌رسان‌ها، و نوار پایین.
 * همه متن‌ها از Customizer ← فوتر و تماس و دفاتر؛ دسته‌ها از ووکامرس؛ لینک‌ها از منوی فوتر.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_about   = (string) ementash_get( 'footer_text' );
$ementash_copy    = (string) ementash_get( 'footer_copyright' );
$ementash_badges  = ementash_get_lines( 'footer_badges' );
$ementash_cta     = (string) ementash_get( 'footer_cta_title' );
$ementash_cta_txt = (string) ementash_get( 'footer_cta_text' );
$ementash_cta_btn = (string) ementash_get( 'footer_cta_label' );
$ementash_phone   = ementash_main_phone();
$ementash_hours   = (string) ementash_get( 'work_hours' );
$ementash_map     = (string) ementash_get( 'contact_map_url' );
$ementash_map_lbl = (string) ementash_get( 'footer_map_label' );

// دسته‌های سطح اول محصولات (بدون دسته پیش‌فرض ووکامرس).
$ementash_cats = taxonomy_exists( 'product_cat' ) ? get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => true,
		'exclude'    => array( absint( get_option( 'default_product_cat' ) ) ),
		'orderby'    => 'menu_order',
	)
) : array();
$ementash_cats = is_wp_error( $ementash_cats ) ? array() : $ementash_cats;
?>
<footer class="em-footer<?php echo '' !== $ementash_cta ? ' em-footer--cta' : ''; ?>">
	<?php if ( '' !== $ementash_cta ) : ?>
		<div class="em-fcta">
			<div class="em-fcta__text">
				<h2 class="em-fcta__title"><?php echo ementash_bdi( $ementash_cta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php if ( '' !== $ementash_cta_txt ) : ?>
					<p class="em-fcta__desc"><?php echo ementash_bdi( $ementash_cta_txt ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
			<div class="em-fcta__actions">
				<?php if ( '' !== $ementash_cta_btn ) : ?>
					<a class="em-btn em-btn--light em-fcta__btn" href="<?php echo esc_url( ementash_contact_url() ); ?>"><?php echo ementash_bdi( $ementash_cta_btn ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endif; ?>
				<?php if ( $ementash_phone ) : ?>
					<a class="em-btn em-btn--ghost em-fcta__phone" href="<?php echo esc_url( ementash_tel_href( $ementash_phone ), array( 'tel' ) ); ?>" dir="ltr">
						<?php echo ementash_icon( 'phone', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo esc_html( ementash_fa_num( $ementash_phone ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>


	<div class="em-footer__in">
		<div class="em-footer__brand">
			<?php ementash_logo( 'em-logo--footer' ); ?>
			<?php if ( '' !== $ementash_about ) : ?>
				<p class="em-footer__about"><?php echo ementash_bdi( $ementash_about ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( $ementash_badges ) : ?>
				<ul class="em-footer__badges" aria-label="<?php esc_attr_e( 'گواهینامه‌ها', 'ementash' ); ?>">
					<?php foreach ( $ementash_badges as $ementash_badge ) : ?>
						<li><?php echo ementash_bdi( $ementash_badge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $ementash_cats ) : ?>
			<nav class="em-footer__col em-footer__col--products" aria-labelledby="em-footer-products-title">
				<h2 class="em-footer__title" id="em-footer-products-title"><?php esc_html_e( 'محصولات', 'ementash' ); ?></h2>
				<ul class="em-footer__menu">
					<?php foreach ( $ementash_cats as $ementash_cat ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $ementash_cat ) ); ?>"><?php echo ementash_bdi( $ementash_cat->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="em-footer__col em-footer__col--links" aria-labelledby="em-footer-links-title">
				<h2 class="em-footer__title" id="em-footer-links-title"><?php esc_html_e( 'دسترسی سریع', 'ementash' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'em-footer__menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="em-footer__contact">
			<h2 class="em-footer__title"><?php esc_html_e( 'تماس', 'ementash' ); ?></h2>
			<?php
			foreach ( array( 'isfahan', 'tehran', 'factory' ) as $ementash_key ) :
				$ementash_label   = (string) ementash_get( 'office_' . $ementash_key . '_label' );
				$ementash_address = (string) ementash_get( 'office_' . $ementash_key . '_address' );
				$ementash_phones  = ementash_phones( $ementash_key );
				$ementash_fax     = (string) ementash_get( 'office_' . $ementash_key . '_fax' );
				if ( '' === $ementash_address && ! $ementash_phones && '' === $ementash_fax ) {
					continue;
				}
				?>
				<?php // دفتری که فقط آدرس دارد در موبایل کامل پنهان می‌شود (آدرس‌ها در موبایل نمی‌آیند). ?>
				<div class="em-footer__office<?php echo ( ! $ementash_phones && '' === $ementash_fax ) ? ' em-footer__office--addr' : ''; ?>">
					<?php if ( '' !== $ementash_label ) : ?>
						<span class="em-footer__label"><?php echo ementash_bdi( $ementash_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<?php if ( '' !== $ementash_address ) : ?>
						<span class="em-footer__addr"><?php echo ementash_bdi( $ementash_address ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<?php if ( $ementash_phones || '' !== $ementash_fax ) : ?>
						<span class="em-footer__nums">
							<?php
							if ( $ementash_phones ) {
								ementash_phone_link( $ementash_phones[0] );
							}
							if ( '' !== $ementash_fax ) {
								printf(
									'<span class="em-footer__fax">%1$s <span dir="ltr">%2$s</span></span>',
									esc_html__( 'فکس', 'ementash' ),
									esc_html( ementash_fa_num( $ementash_fax ) )
								);
							}
							?>
						</span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<?php if ( '' !== $ementash_hours ) : ?>
				<p class="em-footer__hours">
					<?php echo ementash_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo ementash_bdi( $ementash_hours, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
			<?php endif; ?>

			<?php ementash_social_links( 'em-footer__soc' ); ?>
		</div>
	</div>

	<?php if ( '' !== $ementash_copy || ( '' !== $ementash_map && '' !== $ementash_map_lbl ) ) : ?>
		<div class="em-footer__bottom">
			<span><?php echo ementash_bdi( $ementash_copy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php if ( '' !== $ementash_map && '' !== $ementash_map_lbl ) : ?>
				<a class="em-footer__map" href="<?php echo esc_url( $ementash_map ); ?>" target="_blank" rel="noopener">
					<?php echo ementash_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo ementash_bdi( $ementash_map_lbl ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</footer>
