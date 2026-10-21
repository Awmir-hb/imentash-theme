<?php
/**
 * فوتر سایت. اگر Theme Builder المنتور location فوتر را گرفته باشد، فوتر و نوار پایین موبایل قالب چاپ نمی‌شوند.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<?php
if ( ! imentash_elementor_location( 'footer' ) ) {
	get_template_part( 'template-parts/site-footer' );
	get_template_part( 'template-parts/mobile-bar' );
}

// منوی موبایل فقط همراه هدر قالب معنا دارد.
if ( imentash_own_header() ) {
	get_template_part( 'template-parts/mobile-menu' );
}

wp_footer();
?>
</body>
</html>
