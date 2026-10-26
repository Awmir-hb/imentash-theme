<?php
/**
 * فرم جستجو (get_search_form). نوشته‌ها، برگه‌ها و محصولات را با هم جستجو می‌کند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_id = wp_unique_id( 'em-search-' );
?>
<form role="search" method="get" class="em-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $ementash_id ); ?>"><?php esc_html_e( 'جستجو در سایت', 'ementash' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $ementash_id ); ?>" class="em-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'نام محصول، کد یا موضوع مقاله…', 'ementash' ); ?>" autocomplete="off" enterkeyhint="search">
	<button type="submit" class="em-search__btn">
		<?php echo ementash_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span><?php esc_html_e( 'جستجو', 'ementash' ); ?></span>
	</button>
</form>
