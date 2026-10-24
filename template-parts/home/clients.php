<?php
/**
 * صفحه اصلی: نوار چرخان لوگوی مشتریان (پست‌تایپ em_client).
 *
 * فهرست تا رسیدن به حداقل تعداد تکرار می‌شود و یک نسخه دوم کامل (aria-hidden) پشت آن می‌آید
 * تا حرکت بی‌درز باشد. فقط نسخه اول برای صفحه‌خوان‌ها خوانده می‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_items( 'em_client' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_get( 'clients_title' );

// حداقل ۸ لوگو در هر نیمه تا روی صفحه‌های عریض جای خالی دیده نشود.
$ementash_loop = $ementash_items;
while ( count( $ementash_loop ) < 8 ) {
	$ementash_loop = array_merge( $ementash_loop, $ementash_items );
}

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
					'sizes'   => '180px',
				)
			);
		}
		echo '</span>';
		echo '<span class="em-client__name">' . esc_html( get_the_title( $client ) ) . '</span>';
		echo '</li>';
	}
	echo '</ul>';
};
?>
<section class="em-section em-section--soft em-clients"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-clients-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title ) : ?>
			<h2 class="em-clients__title" id="em-clients-title"><?php echo esc_html( $ementash_title ); ?></h2>
		<?php endif; ?>

		<div class="em-clients__wrap">
			<div class="em-clients__track">
				<?php
				$ementash_print_half( $ementash_loop, count( $ementash_items ), false );
				$ementash_print_half( $ementash_loop, count( $ementash_items ), true );
				?>
			</div>
		</div>
	</div>
</section>
