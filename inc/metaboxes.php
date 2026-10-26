<?php
/**
 * متاباکس‌های بومی (بدون ACF).
 *
 * همه متاباکس‌ها فقط در ementash_meta_boxes() تعریف می‌شوند؛ نمایش، ذخیره و ثبت متا از همین آرایه ساخته می‌شود.
 * کلید متا در دیتابیس: _ementash_{نام فیلد}.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * تعریف متاباکس‌ها.
 *
 * نوع فیلدها: text، textarea، url، checkbox، select، file (شناسه پیوست)، editor (wp_editor)،
 * repeater (ردیف‌های تکرارشونده با زیرفیلدهای text/textarea/select).
 */
function ementash_meta_boxes() {
	return array(
		'service'              => array(
			'title'  => __( 'جزئیات خدمت', 'ementash' ),
			'screen' => 'em_service',
			'fields' => array(
				'icon' => array(
					'type'    => 'select',
					'label'   => __( 'آیکون', 'ementash' ),
					'choices' => ementash_icon_choices( 'service' ),
					'default' => 'truck',
				),
				'link' => array(
					'type'        => 'url',
					'label'       => __( 'لینک', 'ementash' ),
					'description' => __( 'برگه‌ای که کارت به آن می‌رود. اگر خالی باشد کارت لینک ندارد.', 'ementash' ),
				),
			),
		),
		'industry'             => array(
			'title'  => __( 'جزئیات صنعت', 'ementash' ),
			'screen' => 'em_industry',
			'fields' => array(
				'link' => array(
					'type'        => 'url',
					'label'       => __( 'لینک', 'ementash' ),
					'description' => __( 'آدرس برگه فعلی این صنعت در سایت.', 'ementash' ),
				),
			),
		),
		'timeline'             => array(
			'title'  => __( 'جزئیات رویداد', 'ementash' ),
			'screen' => 'em_timeline',
			'fields' => array(
				'year' => array(
					'type'  => 'text',
					'label' => __( 'سال', 'ementash' ),
				),
				'text' => array(
					'type'        => 'textarea',
					'label'       => __( 'متن', 'ementash' ),
					'description' => __( 'اگر خالی باشد عنوان رویداد نمایش داده می‌شود.', 'ementash' ),
				),
				'key'  => array(
					'type'  => 'checkbox',
					'label' => __( 'رویداد کلیدی (پررنگ‌تر نمایش داده می‌شود)', 'ementash' ),
				),
			),
		),
		'certificate'          => array(
			'title'  => __( 'جزئیات گواهینامه', 'ementash' ),
			'screen' => 'em_certificate',
			'fields' => array(
				'kind'     => array(
					'type'        => 'select',
					'label'       => __( 'نوع', 'ementash' ),
					'choices'     => ementash_certificate_kinds(),
					'default'     => 'certificate',
					'description' => __( '«افتخار» در نوار افتخارات صفحه اصلی نمایش داده می‌شود؛ «گواهینامه» در صفحه درباره ما.', 'ementash' ),
				),
				'year'     => array(
					'type'  => 'text',
					'label' => __( 'سال', 'ementash' ),
				),
				'subtitle' => array(
					'type'        => 'text',
					'label'       => __( 'توضیح کوتاه', 'ementash' ),
					'description' => __( 'مثلاً «مدیریت کیفیت».', 'ementash' ),
				),
				'pdf'      => array(
					'type'        => 'file',
					'label'       => __( 'فایل PDF (اختیاری)', 'ementash' ),
					'mime'        => 'application/pdf',
					'description' => __( 'اگر انتخاب شود، کارت گواهینامه به این فایل لینک می‌شود.', 'ementash' ),
				),
			),
		),
		'product_applications' => array(
			'title'  => __( 'تب «کاربردها»', 'ementash' ),
			'screen' => 'product',
			'fields' => array(
				'applications' => array( 'type' => 'editor' ),
			),
		),
		'product_packaging'    => array(
			'title'  => __( 'تب «بسته‌بندی»', 'ementash' ),
			'screen' => 'product',
			'fields' => array(
				'packaging' => array( 'type' => 'editor' ),
			),
		),
		'about'                => array(
			'title'     => __( 'بخش‌های صفحه درباره ما', 'ementash' ),
			'screen'    => 'page',
			'condition' => 'ementash_is_about_page',
			'fields'    => array(
				'about_timeline_title' => array(
					'type'        => 'text',
					'label'       => __( 'تیتر خط زمانی', 'ementash' ),
					'description' => __( 'رویدادها از بخش «خط زمانی» در پیشخوان خوانده می‌شوند.', 'ementash' ),
					'default'     => __( 'مسیر ما', 'ementash' ),
				),
				'about_cert_title'     => array(
					'type'        => 'text',
					'label'       => __( 'تیتر گواهینامه‌ها', 'ementash' ),
					'description' => __( 'گواهینامه‌ها از بخش «گواهینامه‌ها» در پیشخوان خوانده می‌شوند.', 'ementash' ),
					'default'     => __( 'گواهینامه‌ها و استانداردها', 'ementash' ),
				),
				'about_cert_text'      => array(
					'type'    => 'textarea',
					'label'   => __( 'متن گواهینامه‌ها', 'ementash' ),
					'default' => __( 'سیستم مدیریت یکپارچه (IMS) شرکت توسط TÜV NORD آلمان تأیید شده است.', 'ementash' ),
				),
				'about_rd_title'       => array(
					'type'    => 'text',
					'label'   => __( 'تیتر تحقیق و توسعه', 'ementash' ),
					'default' => __( 'تحقیق و توسعه', 'ementash' ),
				),
				'about_rd_text'        => array(
					'type'    => 'textarea',
					'label'   => __( 'متن تحقیق و توسعه', 'ementash' ),
					'default' => __( 'واحد تحقیق و توسعه ایمن تاش با همکاری دانشگاه‌ها و مراکز علمی، در این حوزه‌ها فعال است:', 'ementash' ),
				),
				'about_rd_items'       => array(
					'type'    => 'repeater',
					'label'   => __( 'کارت‌های تحقیق و توسعه', 'ementash' ),
					'fields'  => array(
						'title' => array(
							'type'  => 'text',
							'label' => __( 'عنوان', 'ementash' ),
						),
						'icon'  => array(
							'type'    => 'select',
							'label'   => __( 'آیکون', 'ementash' ),
							'choices' => ementash_icon_choices( 'card' ),
						),
					),
					'default' => array(
						array(
							'title' => __( 'فوم آتش‌نشانی', 'ementash' ),
							'icon'  => 'foam',
						),
						array(
							'title' => __( 'پودر آتش‌نشانی', 'ementash' ),
							'icon'  => 'powder',
						),
						array(
							'title' => __( 'مواد ضدکف', 'ementash' ),
							'icon'  => 'antifoam',
						),
						array(
							'title' => __( 'مواد حفاری', 'ementash' ),
							'icon'  => 'drill',
						),
						array(
							'title' => __( 'افزودنی‌های بتن', 'ementash' ),
							'icon'  => 'concrete',
						),
						array(
							'title' => __( 'مواد شیمیایی صنایع نفت', 'ementash' ),
							'icon'  => 'lab',
						),
					),
				),
				'about_values_title'   => array(
					'type'    => 'text',
					'label'   => __( 'تیتر مشتری‌مداری', 'ementash' ),
					'default' => __( 'مشتری‌مداری', 'ementash' ),
				),
				'about_values_items'   => array(
					'type'    => 'repeater',
					'label'   => __( 'کارت‌های مشتری‌مداری', 'ementash' ),
					'fields'  => array(
						'title' => array(
							'type'  => 'text',
							'label' => __( 'عنوان', 'ementash' ),
						),
						'text'  => array(
							'type'  => 'textarea',
							'label' => __( 'متن', 'ementash' ),
						),
						'icon'  => array(
							'type'    => 'select',
							'label'   => __( 'آیکون', 'ementash' ),
							'choices' => ementash_icon_choices( 'card' ),
						),
					),
					'default' => array(
						array(
							'title' => __( 'تعامل و گفتگو', 'ementash' ),
							'text'  => __( 'گفتگوی مستمر با مشتریان برای شناخت دقیق نیازهای آن‌ها.', 'ementash' ),
							'icon'  => 'message',
						),
						array(
							'title' => __( 'رسیدگی شفاف', 'ementash' ),
							'text'  => __( 'فرایند روشن برای رسیدگی به درخواست‌ها و شکایات.', 'ementash' ),
							'icon'  => 'check',
						),
						array(
							'title' => __( 'رفتار منصفانه', 'ementash' ),
							'text'  => __( 'برخورد برابر و منصفانه با همه مشتریان.', 'ementash' ),
							'icon'  => 'scale',
						),
						array(
							'title' => __( 'حفظ اطلاعات مشتری', 'ementash' ),
							'text'  => __( 'محرمانه ماندن اطلاعات مشتری به‌عنوان یک اصل اخلاقی.', 'ementash' ),
							'icon'  => 'lock',
						),
					),
				),
			),
		),
	);
}

/**
 * آیا این برگه، برگه درباره ما است؟
 */
function ementash_is_about_page( $post ) {
	if ( 'page-about.php' === get_page_template_slug( $post ) ) {
		return true;
	}
	$id = ementash_page_id( 'about' );
	return $id && (int) $post->ID === $id;
}

/**
 * تعریف یک فیلد متاباکس با نام آن.
 */
function ementash_meta_field( $name ) {
	foreach ( ementash_meta_boxes() as $box ) {
		if ( isset( $box['fields'][ $name ] ) ) {
			return $box['fields'][ $name ];
		}
	}
	return null;
}

/**
 * خواندن متای قالب. تا وقتی فیلد ذخیره نشده، مقدار پیش‌فرض دمو برگردانده می‌شود.
 */
function ementash_meta( $post_id, $name ) {
	$key = '_ementash_' . $name;
	if ( metadata_exists( 'post', $post_id, $key ) ) {
		return get_post_meta( $post_id, $key, true );
	}
	$field = ementash_meta_field( $name );
	if ( $field && isset( $field['default'] ) ) {
		return $field['default'];
	}
	return ( $field && 'repeater' === $field['type'] ) ? array() : '';
}

/**
 * متای برگه درباره ما.
 */
function ementash_about_meta( $name ) {
	return ementash_meta( ementash_page_id( 'about' ), $name );
}

/**
 * ثبت متای پست‌تایپ‌ها برای REST (فقط فیلدهای ساده).
 */
function ementash_register_meta() {
	$types = array(
		'checkbox' => 'boolean',
		'file'     => 'integer',
	);
	foreach ( ementash_meta_boxes() as $box ) {
		if ( ! post_type_exists( $box['screen'] ) || 'page' === $box['screen'] ) {
			continue;
		}
		foreach ( $box['fields'] as $name => $field ) {
			if ( 'repeater' === $field['type'] ) {
				continue;
			}
			register_post_meta(
				$box['screen'],
				'_ementash_' . $name,
				array(
					'type'              => isset( $types[ $field['type'] ] ) ? $types[ $field['type'] ] : 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => static function ( $value ) use ( $field ) {
						return ementash_sanitize_meta_value( $value, $field );
					},
					'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
						return current_user_can( 'edit_post', $post_id );
					},
				)
			);
		}
	}
}
add_action( 'init', 'ementash_register_meta', 20 );

function ementash_add_meta_boxes( $post_type, $post ) {
	foreach ( ementash_meta_boxes() as $box_id => $box ) {
		if ( $box['screen'] !== $post_type ) {
			continue;
		}
		if ( isset( $box['condition'] ) && ! call_user_func( $box['condition'], $post ) ) {
			continue;
		}
		add_meta_box(
			'ementash-' . $box_id,
			$box['title'],
			'ementash_render_meta_box',
			$post_type,
			'normal',
			'high',
			array( 'box' => $box_id )
		);
	}

	if ( 'em_inquiry' === $post_type ) {
		add_meta_box( 'ementash-inquiry', __( 'جزئیات درخواست', 'ementash' ), 'ementash_render_inquiry_box', 'em_inquiry', 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'ementash_add_meta_boxes', 10, 2 );

function ementash_render_meta_box( $post, $metabox ) {
	$box_id = $metabox['args']['box'];
	$boxes  = ementash_meta_boxes();
	$box    = $boxes[ $box_id ];

	wp_nonce_field( 'ementash_meta_' . $box_id, 'ementash_nonce_' . $box_id );

	echo '<div class="em-mb">';
	foreach ( $box['fields'] as $name => $field ) {
		ementash_render_meta_field( 'ementash_meta[' . $name . ']', 'em-' . $name, $field, ementash_meta( $post->ID, $name ) );
	}
	echo '</div>';
}

/**
 * چاپ یک فیلد متاباکس (و زیرفیلدهای ردیف تکرارشونده).
 */
function ementash_render_meta_field( $input_name, $input_id, $field, $value ) {
	$label = isset( $field['label'] ) ? $field['label'] : '';
	$desc  = isset( $field['description'] ) ? $field['description'] : '';

	if ( 'editor' === $field['type'] ) {
		wp_editor(
			(string) $value,
			str_replace( '-', '_', $input_id ),
			array(
				'textarea_name' => $input_name,
				'textarea_rows' => 8,
				'media_buttons' => true,
			)
		);
		return;
	}

	echo '<div class="em-mb__field em-mb__field--' . esc_attr( $field['type'] ) . '">';

	switch ( $field['type'] ) {
		case 'checkbox':
			printf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" value="1"%3$s> %4$s</label>',
				esc_attr( $input_name ),
				esc_attr( $input_id ),
				checked( (bool) $value, true, false ),
				esc_html( $label )
			);
			break;

		case 'repeater':
			ementash_render_repeater( $input_name, $input_id, $field, is_array( $value ) ? $value : array() );
			break;

		default:
			if ( $label ) {
				printf( '<label class="em-mb__label" for="%1$s">%2$s</label>', esc_attr( $input_id ), esc_html( $label ) );
			}
			ementash_render_meta_input( $input_name, $input_id, $field, $value );
	}

	if ( $desc ) {
		echo '<p class="description">' . esc_html( $desc ) . '</p>';
	}
	echo '</div>';
}

/**
 * ورودی ساده: text، url، textarea، select، file.
 */
function ementash_render_meta_input( $input_name, $input_id, $field, $value ) {
	switch ( $field['type'] ) {
		case 'textarea':
			printf(
				'<textarea class="widefat" name="%1$s" id="%2$s" rows="3">%3$s</textarea>',
				esc_attr( $input_name ),
				esc_attr( $input_id ),
				esc_textarea( (string) $value )
			);
			break;

		case 'select':
			printf( '<select name="%1$s" id="%2$s">', esc_attr( $input_name ), esc_attr( $input_id ) );
			foreach ( $field['choices'] as $key => $choice ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $key ), selected( (string) $value, (string) $key, false ), esc_html( $choice ) );
			}
			echo '</select>';
			break;

		case 'file':
			$id   = absint( $value );
			$file = $id ? get_attached_file( $id ) : '';
			printf(
				'<div class="em-mb__media" data-em-media data-type="%1$s" data-title="%2$s" data-button="%3$s">',
				esc_attr( isset( $field['mime'] ) ? $field['mime'] : '' ),
				esc_attr__( 'انتخاب فایل', 'ementash' ),
				esc_attr__( 'استفاده از این فایل', 'ementash' )
			);
			printf( '<input type="hidden" name="%1$s" id="%2$s" value="%3$s" data-em-media-input>', esc_attr( $input_name ), esc_attr( $input_id ), esc_attr( $id ? (string) $id : '' ) );
			printf( '<span class="em-mb__filename" data-em-media-name>%s</span> ', esc_html( $file ? wp_basename( $file ) : __( 'فایلی انتخاب نشده است.', 'ementash' ) ) );
			printf( '<button type="button" class="button" data-em-media-pick>%s</button> ', esc_html__( 'انتخاب فایل', 'ementash' ) );
			printf(
				'<button type="button" class="button-link button-link-delete" data-em-media-clear data-empty="%2$s"%1$s>%3$s</button>',
				$id ? '' : ' hidden',
				esc_attr__( 'فایلی انتخاب نشده است.', 'ementash' ),
				esc_html__( 'حذف', 'ementash' )
			);
			echo '</div>';
			break;

		default:
			printf(
				'<input type="%1$s" class="widefat" name="%2$s" id="%3$s" value="%4$s"%5$s>',
				'url' === $field['type'] ? 'url' : 'text',
				esc_attr( $input_name ),
				esc_attr( $input_id ),
				'url' === $field['type'] ? esc_url( (string) $value ) : esc_attr( (string) $value ),
				'url' === $field['type'] ? ' dir="ltr"' : ''
			);
	}
}

/**
 * ردیف‌های تکرارشونده. افزودن، حذف و جابه‌جایی در assets/js/admin.js.
 */
function ementash_render_repeater( $input_name, $input_id, $field, $rows ) {
	echo '<fieldset class="em-rep" data-em-rep>';
	echo '<legend class="em-mb__label">' . esc_html( $field['label'] ) . '</legend>';
	echo '<ol class="em-rep__list" data-em-rep-list>';
	foreach ( array_values( $rows ) as $i => $row ) {
		ementash_render_repeater_row( $input_name, $input_id, $field, $i, is_array( $row ) ? $row : array() );
	}
	echo '</ol>';

	// الگوی ردیف جدید؛ __i__ در JS با شماره یکتا جایگزین می‌شود.
	echo '<template data-em-rep-tpl>';
	ementash_render_repeater_row( $input_name, $input_id, $field, '__i__', array() );
	echo '</template>';

	printf( '<button type="button" class="button" data-em-rep-add>%s</button>', esc_html__( 'افزودن ردیف', 'ementash' ) );
	echo '</fieldset>';
}

function ementash_render_repeater_row( $input_name, $input_id, $field, $index, $row ) {
	echo '<li class="em-rep__row">';
	echo '<div class="em-rep__fields">';
	foreach ( $field['fields'] as $sub => $sub_field ) {
		$name = $input_name . '[' . $index . '][' . $sub . ']';
		$id   = $input_id . '-' . $index . '-' . $sub;
		echo '<div class="em-rep__field">';
		printf( '<label class="em-mb__label" for="%1$s">%2$s</label>', esc_attr( $id ), esc_html( $sub_field['label'] ) );
		ementash_render_meta_input( $name, $id, $sub_field, isset( $row[ $sub ] ) ? $row[ $sub ] : '' );
		echo '</div>';
	}
	echo '</div>';
	echo '<div class="em-rep__actions">';
	printf( '<button type="button" class="button-link" data-em-rep-up aria-label="%1$s">%2$s</button>', esc_attr__( 'انتقال به بالا', 'ementash' ), esc_html__( 'بالا', 'ementash' ) );
	printf( '<button type="button" class="button-link" data-em-rep-down aria-label="%1$s">%2$s</button>', esc_attr__( 'انتقال به پایین', 'ementash' ), esc_html__( 'پایین', 'ementash' ) );
	printf( '<button type="button" class="button-link button-link-delete" data-em-rep-remove>%s</button>', esc_html__( 'حذف', 'ementash' ) );
	echo '</div>';
	echo '</li>';
}

/**
 * پاک‌سازی مقدار هر فیلد بر اساس نوع آن.
 */
function ementash_sanitize_meta_value( $value, $field ) {
	switch ( $field['type'] ) {
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		case 'url':
			return esc_url_raw( (string) $value );
		case 'checkbox':
			return ! empty( $value );
		case 'select':
			$value = sanitize_key( (string) $value );
			return isset( $field['choices'][ $value ] ) ? $value : (string) key( $field['choices'] );
		case 'file':
			return absint( $value );
		case 'editor':
			return wp_kses_post( (string) $value );
		case 'repeater':
			$rows = array();
			foreach ( is_array( $value ) ? $value : array() as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$clean = array();
				foreach ( $field['fields'] as $sub => $sub_field ) {
					$clean[ $sub ] = ementash_sanitize_meta_value( isset( $row[ $sub ] ) ? $row[ $sub ] : '', $sub_field );
				}
				// ردیف بدون عنوان ذخیره نمی‌شود.
				if ( isset( $clean['title'] ) && '' === $clean['title'] ) {
					continue;
				}
				$rows[] = $clean;
			}
			return $rows;
		default:
			return sanitize_text_field( (string) $value );
	}
}

function ementash_save_meta_boxes( $post_id, $post ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce هر باکس پایین‌تر بررسی می‌شود.
	$posted = isset( $_POST['ementash_meta'] ) && is_array( $_POST['ementash_meta'] ) ? wp_unslash( $_POST['ementash_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- هر فیلد جدا پاک‌سازی می‌شود.

	foreach ( ementash_meta_boxes() as $box_id => $box ) {
		if ( $box['screen'] !== $post->post_type ) {
			continue;
		}
		$nonce = isset( $_POST[ 'ementash_nonce_' . $box_id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'ementash_nonce_' . $box_id ] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ementash_meta_' . $box_id ) ) {
			continue;
		}
		if ( isset( $box['condition'] ) && ! call_user_func( $box['condition'], $post ) ) {
			continue;
		}
		foreach ( $box['fields'] as $name => $field ) {
			// چک‌باکس خالی و ردیف‌های حذف‌شده در POST نمی‌آیند؛ مقدار خالی ذخیره می‌شود.
			$value = isset( $posted[ $name ] ) ? $posted[ $name ] : '';
			update_post_meta( $post_id, '_ementash_' . $name, ementash_sanitize_meta_value( $value, $field ) );
		}
	}
}
add_action( 'save_post', 'ementash_save_meta_boxes', 10, 2 );

/**
 * نمایش فقط‌خواندنی جزئیات درخواست فرم تماس.
 */
function ementash_render_inquiry_box( $post ) {
	$types = ementash_inquiry_types();
	echo '<table class="widefat striped em-mb__table"><tbody>';
	foreach ( ementash_inquiry_fields() as $field => $label ) {
		$value = ementash_inquiry_meta( $post->ID, $field );
		if ( '' === $value ) {
			continue;
		}
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
		if ( 'type' === $field ) {
			echo esc_html( isset( $types[ $value ] ) ? $types[ $value ] : $value );
		} elseif ( 'product' === $field && absint( $value ) && get_post( absint( $value ) ) ) {
			printf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( absint( $value ) ) ), esc_html( get_the_title( absint( $value ) ) ) );
		} elseif ( 'mail' === $field ) {
			echo esc_html( 'sent' === $value ? __( 'ارسال شد', 'ementash' ) : __( 'ارسال نشد (درخواست ذخیره شده است)', 'ementash' ) );
		} elseif ( 'phone' === $field ) {
			printf( '<a href="%1$s" dir="ltr">%2$s</a>', esc_url( ementash_tel_href( $value ), array( 'tel' ) ), esc_html( $value ) );
		} elseif ( 'email' === $field ) {
			printf( '<a href="%1$s" dir="ltr">%2$s</a>', esc_url( 'mailto:' . $value ), esc_html( $value ) );
		} else {
			echo nl2br( esc_html( $value ) );
		}
		echo '</td></tr>';
	}
	echo '<tr><th scope="row">' . esc_html__( 'تاریخ ارسال', 'ementash' ) . '</th><td>' . esc_html( get_the_date( '', $post ) . ' ' . get_the_time( '', $post ) ) . '</td></tr>';
	echo '</tbody></table>';
}

/**
 * CSS و JS متاباکس‌ها فقط در صفحه ویرایش پست‌تایپ‌هایی که متاباکس دارند.
 */
function ementash_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array_merge( wp_list_pluck( ementash_meta_boxes(), 'screen' ), array( 'em_inquiry' ) ), true ) ) {
		return;
	}

	if ( 'em_certificate' === $screen->post_type ) {
		wp_enqueue_media();
	}
	wp_enqueue_style( 'ementash-admin', EMENTASH_URI . '/assets/css/admin.css', array(), ementash_asset_ver( 'assets/css/admin.css' ) );
	wp_enqueue_script(
		'ementash-admin',
		EMENTASH_URI . '/assets/js/admin.js',
		array(),
		ementash_asset_ver( 'assets/js/admin.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ementash_admin_assets' );
