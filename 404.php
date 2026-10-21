<?php
/**
 * صفحه ۴۰۴. طراحی کامل در فاز ۶.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'single' ) ) {
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title' => __( 'صفحه پیدا نشد', 'ementash' ),
			'desc'  => __( 'صفحه‌ای که دنبالش بودید وجود ندارد یا جابه‌جا شده است.', 'ementash' ),
		)
	);
	?>
	<div class="em-section">
		<div class="em-container em-entry">
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به صفحه اصلی', 'ementash' ); ?></a></p>
		</div>
	</div>
	<?php
}

get_footer();
