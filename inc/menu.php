<?php
/**
 * منوی اصلی: مگامنوی محصولات (خودکار از دسته‌ها و محصولات ووکامرس) و زیرمنوی کشویی.
 *
 * - آیتم منویی که به برگه فروشگاه لینک دارد (یا کلاس CSS «em-mega» دارد) مگامنو می‌گیرد:
 *   دسکتاپ: ستون دسته‌های اصلی؛ با هاور/فوکوس هر دسته، زیردسته‌ها و محصولاتش کنار آن.
 *   موبایل: آکاردئون دسته ← محصولات.
 * - آیتم‌های دارای زیرمنو (مثل «صنایع») در دسکتاپ کشویی‌اند و در موبایل دکمه باز/بسته دارند.
 * - داده مگامنو کش می‌شود و با تغییر محصول یا دسته پاک می‌شود. رفتار در assets/js/main.js.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * داده مگامنو: دسته‌های اصلی (به ترتیب menu_order)، هر کدام با گروه‌ها (زیردسته‌ها) و محصولات.
 *
 * @return array
 */
function ementash_mega_data() {
	$cached = get_transient( 'ementash_mega' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$data = array();
	if ( ! taxonomy_exists( 'product_cat' ) || ! function_exists( 'wc_get_products' ) ) {
		return $data;
	}
	$args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'orderby'    => 'menu_order',
		'exclude'    => array( absint( get_option( 'default_product_cat' ) ) ),
	);
	$tops = get_terms( $args + array( 'parent' => 0 ) );
	if ( is_wp_error( $tops ) ) {
		return $data;
	}
	$products_of = static function ( $term_id, $direct_only ) {
		$items = wc_get_products(
			array(
				'status'   => 'publish',
				'limit'    => -1,
				'orderby'  => 'menu_order',
				'order'    => 'ASC',
				'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- فهرست کوتاه، کش‌شده.
					array(
						'taxonomy'         => 'product_cat',
						'terms'            => array( $term_id ),
						'include_children' => ! $direct_only,
					),
				),
			)
		);
		$out = array();
		foreach ( $items as $p ) {
			$out[] = array(
				'id'    => $p->get_id(),
				'name'  => $p->get_name(),
				'url'   => $p->get_permalink(),
				'sku'   => $p->get_sku(),
				'image' => (int) $p->get_image_id(),
			);
		}
		return $out;
	};
	foreach ( $tops as $top ) {
		$children = get_terms( $args + array( 'parent' => $top->term_id ) );
		$groups   = array();
		$total    = 0;
		if ( ! is_wp_error( $children ) && $children ) {
			// محصولی که هم در دسته اصلی و هم در زیردسته است فقط زیر زیردسته می‌آید (دوبار شمرده نشود).
			$seen = array();
			foreach ( $children as $child ) {
				$list = array_values( array_filter( $products_of( $child->term_id, false ), static fn( $p ) => ! isset( $seen[ $p['id'] ] ) ) );
				if ( ! $list ) {
					continue;
				}
				foreach ( $list as $p ) {
					$seen[ $p['id'] ] = true;
				}
				$groups[] = array(
					'name'     => $child->name,
					'url'      => get_term_link( $child ),
					'products' => $list,
				);
				$total   += count( $list );
			}
			$direct = array_values( array_filter( $products_of( $top->term_id, true ), static fn( $p ) => ! isset( $seen[ $p['id'] ] ) ) );
			if ( $direct ) {
				array_unshift(
					$groups,
					array(
						'name'     => '',
						'url'      => '',
						'products' => $direct,
					)
				);
				$total += count( $direct );
			}
		} else {
			$list     = $products_of( $top->term_id, false );
			$groups[] = array(
				'name'     => '',
				'url'      => '',
				'products' => $list,
			);
			$total    = count( $list );
		}
		if ( ! $total ) {
			continue;
		}
		$data[] = array(
			'id'     => $top->term_id,
			'name'   => $top->name,
			'url'    => get_term_link( $top ),
			'count'  => $total,
			'icon'   => function_exists( 'ementash_cat_icon' ) ? ementash_cat_icon( $top->term_id ) : '',
			'groups' => $groups,
		);
	}
	set_transient( 'ementash_mega', $data, DAY_IN_SECONDS );
	return $data;
}

/**
 * پاک کردن کش مگامنو با هر تغییر محصول یا دسته.
 */
function ementash_mega_flush() {
	delete_transient( 'ementash_mega' );
}
add_action( 'save_post_product', 'ementash_mega_flush' );
add_action( 'deleted_post', 'ementash_mega_flush' );
add_action( 'trashed_post', 'ementash_mega_flush' );
add_action( 'created_product_cat', 'ementash_mega_flush' );
add_action( 'edited_product_cat', 'ementash_mega_flush' );
add_action( 'delete_product_cat', 'ementash_mega_flush' );
add_action( 'woocommerce_after_product_ordering', 'ementash_mega_flush' );
add_action( 'switch_theme', 'ementash_mega_flush' );

/**
 * آیا این آیتم منو مگامنوی محصولات است؟ (لینک به برگه فروشگاه یا کلاس em-mega)
 */
function ementash_is_mega_item( $item ) {
	if ( in_array( 'em-mega', (array) $item->classes, true ) ) {
		return true;
	}
	$shop = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
	return $shop > 0 && 'post_type' === $item->type && (int) $item->object_id === $shop;
}

/**
 * پنل مگامنوی دسکتاپ.
 */
function ementash_mega_panel( $item ) {
	$cats = ementash_mega_data();
	if ( ! $cats ) {
		return '';
	}
	$catalog = absint( ementash_get( 'shop_catalog_file' ) );
	$file    = $catalog ? wp_get_attachment_url( $catalog ) : '';
	$id      = 'em-mega-' . absint( $item->ID );

	ob_start();
	?>
	<div class="em-mega" id="<?php echo esc_attr( $id ); ?>" data-em-mega>
		<div class="em-mega__in">
			<ul class="em-mega__cats">
				<?php foreach ( $cats as $i => $cat ) : ?>
					<li class="em-mega__cat<?php echo 0 === $i ? ' is-active' : ''; ?>" data-em-mega-cat="<?php echo esc_attr( $i ); ?>">
						<a href="<?php echo esc_url( $cat['url'] ); ?>">
							<?php if ( '' !== $cat['icon'] ) : ?>
								<span class="em-mega__ico"><?php echo ementash_icon( $cat['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
							<span class="em-mega__cname"><?php echo ementash_bdi( $cat['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="em-mega__count"><?php echo esc_html( ementash_fa_num( $cat['count'] ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="em-mega__panes">
				<?php foreach ( $cats as $i => $cat ) : ?>
					<div class="em-mega__pane<?php echo 0 === $i ? ' is-active' : ''; ?>" data-em-mega-pane="<?php echo esc_attr( $i ); ?>">
						<div class="em-mega__head">
							<span class="em-mega__title"><?php echo ementash_bdi( $cat['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<a class="em-mega__all" href="<?php echo esc_url( $cat['url'] ); ?>">
								<?php
								/* translators: %s: تعداد محصولات دسته */
								printf( esc_html__( 'همه %s محصول', 'ementash' ), esc_html( ementash_fa_num( $cat['count'] ) ) );
								?>
								<?php echo ementash_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						</div>
						<?php foreach ( $cat['groups'] as $group ) : ?>
							<div class="em-mega__group">
								<?php if ( '' !== $group['name'] ) : ?>
									<a class="em-mega__sub" href="<?php echo esc_url( $group['url'] ); ?>"><?php echo ementash_bdi( $group['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								<?php endif; ?>
								<ul class="em-mega__products">
									<?php foreach ( $group['products'] as $p ) : ?>
										<li>
											<a class="em-mega__product" href="<?php echo esc_url( $p['url'] ); ?>">
												<?php
												// عکس با data-src: فقط وقتی منو اولین بار باز شد بارگذاری می‌شود (main.js)؛
												// در بارگذاری صفحه هیچ عکسی از منو دانلود نمی‌شود. بدون عکس: جای خالی CSS (نه SVG تکراری).
												$ementash_thumb = $p['image'] ? wp_get_attachment_image_url( $p['image'], 'thumbnail' ) : '';
												?>
												<?php if ( $ementash_thumb ) : ?>
													<span class="em-mega__thumb"><img data-src="<?php echo esc_url( $ementash_thumb ); ?>" alt="" width="44" height="44" decoding="async"></span>
												<?php else : ?>
													<span class="em-mega__thumb em-mega__thumb--ph" aria-hidden="true"></span>
												<?php endif; ?>
												<span class="em-mega__pname"><?php echo ementash_bdi( $p['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="em-mega__promo">
				<span class="em-mega__ptitle"><?php echo ementash_bdi( ementash_get( 'shop_catalog_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<p class="em-mega__ptext"><?php echo ementash_bdi( ementash_get( 'shop_catalog_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php if ( $file ) : ?>
					<a class="em-btn em-btn--light em-mega__pbtn" href="<?php echo esc_url( $file ); ?>" download><?php echo ementash_bdi( ementash_get( 'shop_catalog_btn' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endif; ?>
				<a class="em-mega__quote" href="<?php echo esc_url( ementash_contact_url() ); ?>"><?php echo ementash_bdi( ementash_get( 'quote_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * آکاردئون مگامنو در منوی موبایل: دسته ← (زیردسته) ← محصولات.
 */
function ementash_mega_mobile( $item ) {
	$cats = ementash_mega_data();
	if ( ! $cats ) {
		return '';
	}
	$base = 'em-mm-' . absint( $item->ID );
	ob_start();
	?>
	<ul class="em-mmenu__sub" id="<?php echo esc_attr( $base ); ?>" hidden>
		<?php foreach ( $cats as $i => $cat ) : ?>
			<?php $pid = $base . '-' . $i; ?>
			<li class="em-mmenu__cat">
				<div class="em-mmenu__row">
					<a href="<?php echo esc_url( $cat['url'] ); ?>"><?php echo ementash_bdi( $cat['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <span class="em-mmenu__count"><?php echo esc_html( ementash_fa_num( $cat['count'] ) ); ?></span></a>
					<button type="button" class="em-mmenu__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $pid ); ?>" data-em-acc>
						<span class="screen-reader-text">
							<?php
							/* translators: %s: نام دسته */
							printf( esc_html__( 'محصولات %s', 'ementash' ), esc_html( $cat['name'] ) );
							?>
						</span>
						<?php echo ementash_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
				<ul class="em-mmenu__products" id="<?php echo esc_attr( $pid ); ?>" hidden>
					<?php foreach ( $cat['groups'] as $group ) : ?>
						<?php if ( '' !== $group['name'] ) : ?>
							<li class="em-mmenu__group"><a href="<?php echo esc_url( $group['url'] ); ?>"><?php echo ementash_bdi( $group['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
						<?php endif; ?>
						<?php foreach ( $group['products'] as $p ) : ?>
							<li><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo ementash_bdi( $p['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	return (string) ob_get_clean();
}

/**
 * دکمه باز/بسته زیرمنو در موبایل.
 */
function ementash_menu_toggle( $target, $label ) {
	return sprintf(
		'<button type="button" class="em-mmenu__toggle" aria-expanded="false" aria-controls="%1$s" data-em-acc><span class="screen-reader-text">%2$s</span>%3$s</button>',
		esc_attr( $target ),
		/* translators: %s: عنوان آیتم منو */
		esc_html( sprintf( __( 'زیرمنوی %s', 'ementash' ), wp_strip_all_tags( $label ) ) ),
		ementash_icon( 'chevron' )
	);
}

/**
 * افزودن مگامنو (دسکتاپ و موبایل) و دکمه زیرمنو به خروجی آیتم منو.
 */
function ementash_menu_item_output( $output, $item, $depth, $args ) {
	if ( 0 !== $depth || empty( $args->theme_location ) ) {
		return $output;
	}
	$mega = ementash_is_mega_item( $item );
	// منوی موبایل ممکن است از location «primary» استفاده کند؛ تشخیص با کلاس فهرست.
	if ( 'em-mmenu__list' === ( $args->menu_class ?? '' ) ) {
		if ( $mega ) {
			$panel = ementash_mega_mobile( $item );
			if ( '' !== $panel ) {
				return '<div class="em-mmenu__row">' . $output . ementash_menu_toggle( 'em-mm-' . absint( $item->ID ), $item->title ) . '</div>' . $panel;
			}
		} elseif ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			return '<div class="em-mmenu__row">' . $output . ementash_menu_toggle( 'em-sub-' . absint( $item->ID ), $item->title ) . '</div>';
		}
		return $output;
	}
	if ( 'em-nav__list' === ( $args->menu_class ?? '' ) && $mega ) {
		return $output . ementash_mega_panel( $item );
	}
	return $output;
}
add_filter( 'walker_nav_menu_start_el', 'ementash_menu_item_output', 10, 4 );

/**
 * کلاس و شناسه زیرمنو (برای aria-controls دکمه موبایل) و کلاس آیتم مگامنو.
 */
function ementash_menu_item_classes( $classes, $item, $args, $depth ) {
	if ( 0 === $depth && ementash_is_mega_item( $item ) ) {
		$classes[] = 'em-has-mega';
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'ementash_menu_item_classes', 10, 4 );

/**
 * زیرمنوی موبایل: شناسه برای aria-controls و پنهان در ابتدا.
 */
function ementash_submenu_attrs( $atts, $args, $depth ) {
	if ( 'em-mmenu__list' === ( $args->menu_class ?? '' ) ) {
		$atts['hidden'] = 'hidden';
	}
	return $atts;
}
add_filter( 'nav_menu_submenu_attributes', 'ementash_submenu_attrs', 10, 3 );

/**
 * لینک‌های سطح اول دارای زیرمنو یا مگامنو در دسکتاپ: aria-haspopup.
 */
function ementash_menu_link_attrs( $atts, $item, $args, $depth ) {
	if ( 0 === $depth && 'em-nav__list' === ( $args->menu_class ?? '' ) && ( ementash_is_mega_item( $item ) || in_array( 'menu-item-has-children', (array) $item->classes, true ) ) ) {
		$atts['aria-haspopup'] = 'true';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'ementash_menu_link_attrs', 10, 4 );
