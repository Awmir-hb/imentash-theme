<?php
/**
 * نوار آمار (۴ آیتم از Customizer). در صفحه اصلی و صفحه درباره ما استفاده می‌شود.
 * آیتمی که عدد ندارد نمایش داده نمی‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_stats = array();
for ( $ementash_n = 1; $ementash_n <= 4; $ementash_n++ ) {
	$ementash_number = (string) ementash_get( 'stat_' . $ementash_n . '_number' );
	if ( '' !== $ementash_number ) {
		$ementash_stats[] = array(
			'number' => $ementash_number,
			'label'  => (string) ementash_get( 'stat_' . $ementash_n . '_label' ),
			'icon'   => (string) ementash_get( 'stat_' . $ementash_n . '_icon' ),
		);
	}
}
if ( ! $ementash_stats ) {
	return;
}
?>
<ul class="em-stats">
	<?php foreach ( $ementash_stats as $ementash_stat ) : ?>
		<li class="em-stat">
			<?php echo ementash_icon( $ementash_stat['icon'], 'em-stat__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div>
				<div class="em-stat__num" dir="auto"><?php echo esc_html( ementash_fa_num( $ementash_stat['number'] ) ); ?></div>
				<?php if ( '' !== $ementash_stat['label'] ) : ?>
					<div class="em-stat__label"><?php echo esc_html( $ementash_stat['label'] ); ?></div>
				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
