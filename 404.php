<?php
/**
 * صفحه ۴۰۴: سربرگ، جستجو، لینک‌های اصلی و دسته‌های سطح اول محصولات.
 * location «single» المنتور اولویت دارد.
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

	$ementash_cats = taxonomy_exists( 'product_cat' ) ? get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'exclude'    => array( absint( get_option( 'default_product_cat' ) ) ),
			'orderby'    => 'menu_order',
		)
	) : array();
	?>
	<div class="em-section em-notfound">
		<div class="em-container em-notfound__in">
			<p class="em-notfound__code" aria-hidden="true"><?php echo esc_html( ementash_fa_num( '404' ) ); ?></p>

			<div class="em-notfound__body">
				<h2 class="em-notfound__title"><?php esc_html_e( 'در سایت جستجو کنید', 'ementash' ); ?></h2>
				<?php get_search_form(); ?>

				<div class="em-notfound__actions">
					<a class="em-btn em-btn--brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحه اصلی', 'ementash' ); ?></a>
					<a class="em-btn em-btn--outline" href="<?php echo esc_url( ementash_shop_url() ); ?>"><?php esc_html_e( 'فهرست محصولات', 'ementash' ); ?></a>
					<a class="em-btn em-btn--outline" href="<?php echo esc_url( ementash_contact_url() ); ?>"><?php esc_html_e( 'تماس با ما', 'ementash' ); ?></a>
				</div>

				<?php if ( $ementash_cats && ! is_wp_error( $ementash_cats ) ) : ?>
					<h2 class="em-notfound__title"><?php esc_html_e( 'دسته‌های محصولات', 'ementash' ); ?></h2>
					<ul class="em-tags">
						<?php foreach ( $ementash_cats as $ementash_cat ) : ?>
							<li><a class="em-tag" href="<?php echo esc_url( get_term_link( $ementash_cat ) ); ?>"><?php echo ementash_bdi( $ementash_cat->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

get_footer();
