<?php
/**
 * محتوای ویرایشگر داخل کانتینر قالب (برگه‌ها و نوشته‌هایی که با المنتور ساخته نشده‌اند).
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="em-section">
	<div class="em-container em-entry">
		<?php
		the_content();
		wp_link_pages();
		?>
	</div>
</div>
