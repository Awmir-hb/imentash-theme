<?php
/**
 * دکمه بازگشت به بالای صفحه: دایره کوچک سرمه‌ای با حلقه پیشرفت اسکرول.
 * بعد از حدود یک صفحه اسکرول ظاهر می‌شود؛ رفتار در assets/js/main.js. بدون JS پنهان می‌ماند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;
?>
<button type="button" class="em-totop" data-em-totop aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'ementash' ); ?>">
	<svg class="em-totop__ring" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
		<circle class="em-totop__track" cx="24" cy="24" r="22"/>
		<circle class="em-totop__bar" cx="24" cy="24" r="22" pathLength="100"/>
	</svg>
	<?php echo ementash_icon( 'arrow', 'em-totop__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</button>
