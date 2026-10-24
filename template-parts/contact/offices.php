<?php
/**
 * تماس: کارت دفاتر (اصفهان، تهران، کارخانه) از Customizer ← تماس و دفاتر.
 * دفتری که نه آدرس دارد نه تلفن نمایش داده نمی‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_offices = array(
	'isfahan' => 'home',
	'tehran'  => 'building',
	'factory' => 'factory',
);
$ementash_cards   = array();
foreach ( $ementash_offices as $ementash_key => $ementash_icon ) {
	$ementash_address = (string) ementash_get( 'office_' . $ementash_key . '_address' );
	$ementash_phones  = ementash_phones( $ementash_key );
	if ( '' === $ementash_address && ! $ementash_phones ) {
		continue;
	}
	$ementash_cards[] = array(
		'icon'    => $ementash_icon,
		'title'   => (string) ementash_get( 'office_' . $ementash_key . '_title' ),
		'address' => $ementash_address,
		'phones'  => $ementash_phones,
	);
}
if ( ! $ementash_cards ) {
	return;
}
?>
<section class="em-offices" aria-label="<?php esc_attr_e( 'دفاتر و کارخانه', 'ementash' ); ?>">
	<div class="em-container">
		<ul class="em-offices__list">
			<?php foreach ( $ementash_cards as $ementash_card ) : ?>
				<li class="em-office">
					<span class="em-office__icon"><?php echo ementash_icon( $ementash_card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php if ( '' !== $ementash_card['title'] ) : ?>
						<h2 class="em-office__title"><?php echo ementash_bdi( $ementash_card['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $ementash_card['address'] ) : ?>
						<p class="em-office__address"><?php echo nl2br( ementash_bdi( $ementash_card['address'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
					<?php if ( $ementash_card['phones'] ) : ?>
						<div class="em-office__phones">
							<?php
							foreach ( $ementash_card['phones'] as $ementash_phone ) {
								ementash_phone_link( $ementash_phone );
							}
							?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
