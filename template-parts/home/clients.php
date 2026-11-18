<?php
/**
 * صفحه اصلی: نوار چرخان لوگوی مشتریان (پست‌تایپ em_client) روی زمینه سرمه‌ای.
 *
 * - ۸ لوگو یا بیشتر: دو ردیف که خلاف هم حرکت می‌کنند؛ کمتر: یک ردیف.
 * - لوگوها روی کارت تیره سفید یک‌رنگ نمایش داده می‌شوند (فیلتر CSS؛ فایل اصلی دست نمی‌خورد).
 * - نام شرکت فقط برای صفحه‌خوان است (نام روی خود لوگوها نوشته شده).
 * - هر ردیف تا حداقل تعداد تکرار می‌شود و یک نسخه دوم کامل (aria-hidden) پشت آن می‌آید تا حرکت بی‌درز باشد.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_items( 'em_client' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_get( 'clients_title' );
$ementash_rows  = count( $ementash_items ) >= 8 ? array_chunk( $ementash_items, (int) ceil( count( $ementash_items ) / 2 ) ) : array( $ementash_items );

/**
 * چاپ یک نیمه نوار. نیمه دوم و تکرارها از صفحه‌خوان پنهان‌اند.
 */
$ementash_print_half = static function ( $loop, $unique_count, $hidden ) {
	printf( '<ul class="em-clients__list"%s>', $hidden ? ' aria-hidden="true"' : '' );
	foreach ( $loop as $i => $client ) {
		$copy = $hidden || $i >= $unique_count;
		printf( '<li class="em-client"%s>', $copy ? ' aria-hidden="true" data-em-copy' : '' );
		echo '<span class="em-client__logo">';
		if ( has_post_thumbnail( $client ) ) {
			echo get_the_post_thumbnail(
				$client,
				'medium',
				array(
					'alt'     => '',
					'loading' => 'lazy',
					'sizes'   => '120px',
				)
			);
		}
		echo '</span>';
		echo '<span class="screen-reader-text">' . ementash_bdi( get_the_title( $client ) ) . '</span>';
		echo '</li>';
	}
	echo '</ul>';
};
?>
<section class="em-section em-clients"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-clients-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title ) : ?>
			<h2 class="em-clients__title" id="em-clients-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php endif; ?>

		<div class="em-clients__rows">
			<?php
			foreach ( $ementash_rows as $ementash_r => $ementash_row ) :
				// حداقل ۱۰ لوگو در هر نیمه تا روی صفحه‌های عریض جای خالی دیده نشود.
				$ementash_loop = $ementash_row;
				while ( count( $ementash_loop ) < 10 ) {
					$ementash_loop = array_merge( $ementash_loop, $ementash_row );
				}
				?>
				<div class="em-clients__wrap">
					<div class="em-clients__track<?php echo $ementash_r % 2 ? ' em-clients__track--rev' : ''; ?>" style="--em-clients-n:<?php echo esc_attr( count( $ementash_loop ) ); ?>">
						<?php
						$ementash_print_half( $ementash_loop, count( $ementash_row ), false );
						$ementash_print_half( $ementash_loop, count( $ementash_row ), true );
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
