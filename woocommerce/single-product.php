<?php
/**
 * صفحه محصول. location «single» برای Woo Builder المنتور پرو؛ طراحی کامل در فاز ۴.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

get_header( null, array( 'variant' => 'plain' ) );

if ( ! imentash_elementor_location( 'single' ) ) {
	while ( have_posts() ) {
		the_post();

		if ( imentash_is_elementor() ) {
			the_content();
			continue;
		}
		?>
		<div class="em-section">
			<div class="em-container">
				<?php wc_get_template_part( 'content', 'single-product' ); ?>
			</div>
		</div>
		<?php
	}
}

get_footer();
