<?php
/**
 * صفحه‌بندی دایره‌ای آرشیو محصولات (ترتیب انتخاب‌شده حفظ می‌شود).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

if ( (int) $wp_query->max_num_pages < 2 ) {
	return;
}

$ementash_links = paginate_links(
	array(
		'total'     => (int) $wp_query->max_num_pages,
		'current'   => max( 1, (int) get_query_var( 'paged' ) ),
		'type'      => 'array',
		'mid_size'  => 1,
		'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'صفحه قبلی', 'ementash' ) . '</span>' . ementash_icon( 'arrow', 'em-pager__prev' ),
		'next_text' => '<span class="screen-reader-text">' . esc_html__( 'صفحه بعدی', 'ementash' ) . '</span>' . ementash_icon( 'arrow' ),
	)
);
if ( ! $ementash_links ) {
	return;
}
?>
<nav class="em-pager" aria-label="<?php esc_attr_e( 'صفحه‌بندی محصولات', 'ementash' ); ?>">
	<?php
	foreach ( $ementash_links as $ementash_link ) {
		// اعداد فارسی؛ پیوند صفحه فعلی aria-current دارد (خروجی خود وردپرس).
		echo preg_replace_callback( '/>(\d+)</', static fn( $m ) => '>' . ementash_fa_num( $m[1] ) . '<', $ementash_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</nav>
