<?php
/**
 * صفحه ۴۰۴. طراحی کامل در فاز ۶.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! imentash_elementor_location( 'single' ) ) {
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title' => __( 'صفحه پیدا نشد', 'imentash' ),
			'desc'  => __( 'صفحه‌ای که دنبالش بودید وجود ندارد یا جابه‌جا شده است.', 'imentash' ),
		)
	);
	?>
	<div class="em-section">
		<div class="em-container em-entry">
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به صفحه اصلی', 'imentash' ); ?></a></p>
		</div>
	</div>
	<?php
}

get_footer();
