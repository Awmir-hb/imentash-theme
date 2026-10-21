<?php
/**
 * بارگذاری ماژول‌های قالب ایمن تاش.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

define( 'IMENTASH_DIR', get_template_directory() );
define( 'IMENTASH_URI', get_template_directory_uri() );

require IMENTASH_DIR . '/inc/template-tags.php';
require IMENTASH_DIR . '/inc/setup.php';
require IMENTASH_DIR . '/inc/elementor.php';
require IMENTASH_DIR . '/inc/enqueue.php';
