<?php
/**
 * هدر سایت. اگر Theme Builder المنتور location هدر را گرفته باشد، هدر قالب چاپ نمی‌شود.
 *
 * نوع هدر قالب با get_header( null, array( 'variant' => ... ) ) تعیین می‌شود:
 * overlay (شفاف روی هیرو صفحه اصلی)، grid (پیش‌فرض، سرمه‌ای شطرنجی) یا plain (سرمه‌ای ساده).
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

$imentash_variant = isset( $args['variant'] ) && in_array( $args['variant'], array( 'overlay', 'grid', 'plain' ), true ) ? $args['variant'] : 'grid';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="em-skip" href="#em-main"><?php esc_html_e( 'رفتن به محتوای اصلی', 'imentash' ); ?></a>

<?php
if ( ! imentash_elementor_location( 'header' ) ) {
	imentash_own_header( true );
	get_template_part( 'template-parts/site-header', null, array( 'variant' => $imentash_variant ) );
}
?>

<main id="em-main" class="em-main" tabindex="-1">
