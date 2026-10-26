<?php
/**
 * نتایج جستجو، گروه‌بندی‌شده: اول محصولات (بدون قیمت و سبد)، بعد مقاله‌ها و برگه‌ها با صفحه‌بندی.
 * کوئری اصلی فقط نوشته و برگه است (inc/blog.php)؛ محصولات با کوئری جدا و فقط در صفحه اول.
 * ‎?post_type=product جستجوی کامل محصولات است و قالب فروشگاه ووکامرس را می‌گیرد.
 * location «archive» المنتور اولویت دارد.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ementash_elementor_location( 'archive' ) ) {
	global $wp_query;

	$ementash_term     = get_search_query( false );
	$ementash_has_term = '' !== trim( $ementash_term );
	$ementash_paged    = max( 1, (int) get_query_var( 'paged' ) );
	$ementash_posts_n  = $ementash_has_term ? (int) $wp_query->found_posts : 0;
	$ementash_products = ( $ementash_has_term && post_type_exists( 'product' ) ) ? ementash_search_products( $ementash_term ) : null;
	$ementash_prod_n   = $ementash_products ? (int) $ementash_products->found_posts : 0;

	if ( $ementash_has_term ) {
		/* translators: %s: عبارت جستجو */
		$ementash_title = sprintf( __( 'نتایج جستجو برای «%s»', 'ementash' ), $ementash_term );
		$ementash_desc  = sprintf(
			/* translators: 1: تعداد محصولات، 2: تعداد مقاله‌ها و برگه‌ها */
			__( '%1$s محصول و %2$s مقاله یا برگه پیدا شد.', 'ementash' ),
			ementash_fa_num( number_format_i18n( $ementash_prod_n ) ),
			ementash_fa_num( number_format_i18n( $ementash_posts_n ) )
		);
	} else {
		$ementash_title = __( 'جستجو', 'ementash' );
		$ementash_desc  = __( 'نام محصول، کد محصول یا موضوع مقاله را بنویسید.', 'ementash' );
	}

	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title'  => $ementash_title,
			'desc'   => $ementash_desc,
			'crumbs' => array(
				array(
					'label' => __( 'صفحه اصلی', 'ementash' ),
					'url'   => home_url( '/' ),
				),
				array(
					'label' => __( 'جستجو', 'ementash' ),
					'url'   => '',
				),
			),
		)
	);
	?>
	<div class="em-section em-sresults">
		<div class="em-container">
			<div class="em-sresults__form"><?php get_search_form(); ?></div>

			<?php if ( $ementash_prod_n && 1 === $ementash_paged ) : ?>
				<section class="em-sgroup" aria-labelledby="em-sgroup-products">
					<div class="em-sgroup__head">
						<h2 class="em-sgroup__title" id="em-sgroup-products">
							<?php esc_html_e( 'محصولات', 'ementash' ); ?>
							<span class="em-sgroup__count"><?php echo esc_html( ementash_fa_num( number_format_i18n( $ementash_prod_n ) ) ); ?></span>
						</h2>
						<?php if ( $ementash_prod_n > (int) $ementash_products->post_count ) : ?>
							<a class="em-link" href="<?php echo esc_url( ementash_product_search_url( $ementash_term ) ); ?>">
								<?php
								/* translators: %s: تعداد محصولات */
								echo esc_html( sprintf( __( 'همه %s محصول', 'ementash' ), ementash_fa_num( number_format_i18n( $ementash_prod_n ) ) ) );
								?>
							</a>
						<?php endif; ?>
					</div>
					<ul class="em-pgrid">
						<?php
						while ( $ementash_products->have_posts() ) {
							$ementash_products->the_post();
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
						?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $ementash_posts_n ) : ?>
				<section class="em-sgroup" aria-labelledby="em-sgroup-posts">
					<div class="em-sgroup__head">
						<h2 class="em-sgroup__title" id="em-sgroup-posts">
							<?php esc_html_e( 'مقاله‌ها و برگه‌ها', 'ementash' ); ?>
							<span class="em-sgroup__count"><?php echo esc_html( ementash_fa_num( number_format_i18n( $ementash_posts_n ) ) ); ?></span>
						</h2>
					</div>
					<ul class="em-posts em-posts--line">
						<?php
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/blog/card', null, array( 'heading' => 'h3' ) );
						}
						?>
					</ul>
					<?php get_template_part( 'template-parts/pagination', null, array( 'label' => __( 'صفحه‌بندی نتایج جستجو', 'ementash' ) ) ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $ementash_has_term && ! $ementash_prod_n && ! $ementash_posts_n ) : ?>
				<div class="em-empty em-empty--box">
					<p class="em-empty__title"><?php esc_html_e( 'نتیجه‌ای پیدا نشد.', 'ementash' ); ?></p>
					<p><?php esc_html_e( 'عبارت کوتاه‌تر یا کد محصول را امتحان کنید، یا از فهرست محصولات کمک بگیرید.', 'ementash' ); ?></p>
					<div class="em-empty__actions">
						<a class="em-btn em-btn--brand" href="<?php echo esc_url( ementash_shop_url() ); ?>"><?php esc_html_e( 'فهرست محصولات', 'ementash' ); ?></a>
						<a class="em-btn em-btn--outline" href="<?php echo esc_url( ementash_contact_url() ); ?>"><?php esc_html_e( 'تماس با ما', 'ementash' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

get_footer();
