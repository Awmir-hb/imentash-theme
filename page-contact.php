<?php
/**
 * Template Name: تماس با ما
 *
 * صفحه تماس (Contact.dc.html): دفاتر، فرم درخواست، نقشه و ساعات کاری. با هر نامکی کار می‌کند:
 * قالب را در ویرایشگر برگه انتخاب کنید یا برگه را در Customizer ← برگه‌ها تعیین کنید.
 * هندلر فرم در inc/contact-form.php. location «single» المنتور اولویت دارد.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'single' ) ) {
	while ( have_posts() ) {
		the_post();

		if ( ementash_is_elementor() ) {
			the_content();
			continue;
		}

		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'  => get_the_title(),
				'desc'   => ementash_page_subtitle(),
				'crumbs' => ementash_page_crumbs(),
			)
		);

		get_template_part( 'template-parts/contact/offices' );
		get_template_part( 'template-parts/contact/form' );

		// محتوای اختیاری خود برگه، زیر فرم.
		if ( '' !== trim( (string) get_the_content() ) ) {
			echo '<div class="em-section em-contact-extra"><div class="em-container">';
			get_template_part( 'template-parts/entry-content' );
			echo '</div></div>';
		}
	}
}

get_footer();
