<?php
/**
 * تماس: فرم درخواست + ستون کناری (تصویر نقشه با لینک مسیریابی، ساعات کاری و ایمیل).
 *
 * فرم بدون JS به همین صفحه POST می‌شود؛ assets/js/contact.js همان را با AJAX می‌فرستد.
 * ?product=ID (فقط محصول منتشرشده) نوع «استعلام قیمت» و نام محصول را از پیش پر می‌کند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_result = isset( $GLOBALS['ementash_inquiry_result'] ) ? $GLOBALS['ementash_inquiry_result'] : null;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- فقط نمایش پیام بعد از ریدایرکت.
$ementash_sent   = isset( $_GET['em_sent'] ) && '1' === $_GET['em_sent'];
$ementash_values = $ementash_result ? $ementash_result['values'] : array();
$ementash_errors = $ementash_result ? $ementash_result['errors'] : array();
$ementash_types  = ementash_inquiry_types();

// پیش‌پر کردن از لینک «استعلام قیمت این محصول».
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- فقط خواندن شناسه محصول.
$ementash_product = isset( $_GET['product'] ) ? ementash_inquiry_product( absint( $_GET['product'] ) ) : null;
if ( ! $ementash_values && $ementash_product ) {
	$ementash_values = array(
		'type'    => 'price',
		'product' => $ementash_product->ID,
		/* translators: %s: نام محصول */
		'message' => sprintf( __( 'محصول: %s', 'ementash' ), get_the_title( $ementash_product ) ) . "\n" . __( 'مقدار مورد نیاز: ', 'ementash' ),
	);
} elseif ( ! empty( $ementash_values['product'] ) ) {
	$ementash_product = ementash_inquiry_product( $ementash_values['product'] );
}

$ementash_val = static function ( $key ) use ( $ementash_values ) {
	return isset( $ementash_values[ $key ] ) ? (string) $ementash_values[ $key ] : '';
};

/**
 * یک فیلد متنی با برچسب، خطا و ویژگی‌های دسترس‌پذیری.
 */
$ementash_field = static function ( $name, $label, $type, $autocomplete, $required, $wide = false ) use ( $ementash_val, $ementash_errors ) {
	$key   = substr( $name, 3 );
	$error = isset( $ementash_errors[ $name ] ) ? $ementash_errors[ $name ] : '';
	$id    = 'em-f-' . $key;
	$attrs = sprintf(
		'name="%1$s" id="%2$s"%3$s%4$s aria-describedby="%2$s-err"%5$s',
		esc_attr( $name ),
		esc_attr( $id ),
		$autocomplete ? ' autocomplete="' . esc_attr( $autocomplete ) . '"' : '',
		$required ? ' required aria-required="true"' : '',
		$error ? ' aria-invalid="true"' : ''
	);
	echo '<div class="em-field' . ( $wide ? ' em-field--wide' : '' ) . '">';
	printf( '<label for="%1$s">%2$s</label>', esc_attr( $id ), esc_html( $label ) );
	if ( 'textarea' === $type ) {
		printf( '<textarea %1$s rows="4" maxlength="2000">%2$s</textarea>', $attrs, esc_textarea( $ementash_val( $key ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $attrs بالا escape شده.
	} else {
		printf( '<input type="%1$s" %2$s value="%3$s"%4$s>', esc_attr( $type ), $attrs, esc_attr( $ementash_val( $key ) ), 'tel' === $type ? ' dir="ltr" inputmode="tel"' : ( 'email' === $type ? ' dir="ltr"' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $attrs بالا escape شده.
	}
	printf( '<p class="em-field__error" id="%1$s-err" data-em-error%3$s>%2$s</p>', esc_attr( $id ), esc_html( $error ), $error ? '' : ' hidden' );
	echo '</div>';
};

$ementash_title = (string) ementash_get( 'contact_form_title' );
$ementash_text  = (string) ementash_get( 'contact_form_text' );
$ementash_map   = absint( ementash_get( 'contact_map_image' ) );
$ementash_route = (string) ementash_get( 'contact_map_url' );
$ementash_hours = (string) ementash_get( 'work_hours' );
$ementash_email = (string) ementash_get( 'email' );
$ementash_aside = $ementash_map || '' !== $ementash_hours || '' !== $ementash_email;
$ementash_type  = '' !== $ementash_val( 'type' ) ? $ementash_val( 'type' ) : (string) key( $ementash_types );

if ( $ementash_sent ) {
	$ementash_status = array( 'ok', (string) ementash_get( 'contact_success' ) );
} elseif ( $ementash_result ) {
	$ementash_status = array( 'error', $ementash_result['message'] );
} else {
	$ementash_status = array( '', '' );
}
?>
<section class="em-section em-contact" id="em-form">
	<div class="em-container em-contact__in<?php echo $ementash_aside ? '' : ' em-contact__in--full'; ?>">
		<form class="em-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>#em-form" novalidate data-em-inquiry data-em-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-em-sending="<?php esc_attr_e( 'در حال ارسال…', 'ementash' ); ?>" data-em-failed="<?php esc_attr_e( 'ارتباط برقرار نشد. لطفاً دوباره تلاش کنید.', 'ementash' ); ?>"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-form-title"' : ''; ?>>
			<div class="em-form__head">
				<?php if ( '' !== $ementash_title ) : ?>
					<h2 class="em-form__title" id="em-form-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-form__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>

			<div class="em-form__status<?php echo $ementash_status[0] ? ' em-form__status--' . esc_attr( $ementash_status[0] ) : ''; ?>" role="status" aria-live="polite" tabindex="-1" data-em-status<?php echo '' === $ementash_status[1] ? ' hidden' : ''; ?>><?php echo esc_html( $ementash_status[1] ); ?></div>

			<?php if ( $ementash_product ) : ?>
				<p class="em-form__product">
					<?php esc_html_e( 'درخواست برای محصول:', 'ementash' ); ?>
					<a href="<?php echo esc_url( get_permalink( $ementash_product ) ); ?>"><?php echo ementash_bdi( get_the_title( $ementash_product ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</p>
			<?php endif; ?>

			<fieldset class="em-form__types">
				<legend><?php esc_html_e( 'نوع درخواست', 'ementash' ); ?></legend>
				<?php foreach ( $ementash_types as $ementash_key => $ementash_label ) : ?>
					<label class="em-radio">
						<input type="radio" name="em_type" value="<?php echo esc_attr( $ementash_key ); ?>"<?php checked( $ementash_type, $ementash_key ); ?>>
						<?php echo esc_html( $ementash_label ); ?>
					</label>
				<?php endforeach; ?>
			</fieldset>

			<?php
			$ementash_field( 'em_name', __( 'نام و نام خانوادگی', 'ementash' ), 'text', 'name', true );
			$ementash_field( 'em_company', __( 'نام شرکت', 'ementash' ), 'text', 'organization', true );
			$ementash_field( 'em_phone', __( 'شماره تماس', 'ementash' ), 'tel', 'tel', true );
			$ementash_field( 'em_email', __( 'ایمیل (اختیاری)', 'ementash' ), 'email', 'email', false );
			$ementash_field( 'em_message', __( 'محصول و مقدار مورد نیاز', 'ementash' ), 'textarea', '', true, true );
			?>

			<?php // honeypot: برای انسان پنهان است و نباید پر شود. ?>
			<div class="em-form__hp" aria-hidden="true">
				<label for="em-f-website"><?php esc_html_e( 'وب‌سایت', 'ementash' ); ?></label>
				<input type="text" name="em_website" id="em-f-website" value="" tabindex="-1" autocomplete="off">
			</div>

			<input type="hidden" name="em_product" value="<?php echo esc_attr( $ementash_product ? (string) $ementash_product->ID : '' ); ?>">
			<input type="hidden" name="ementash_inquiry" value="1">
			<input type="hidden" name="action" value="ementash_inquiry">
			<?php wp_nonce_field( 'ementash_inquiry', '_em_nonce', false ); ?>

			<div class="em-form__actions">
				<button type="submit" class="em-btn em-btn--brand em-form__submit" data-em-submit><?php esc_html_e( 'ارسال درخواست', 'ementash' ); ?></button>
			</div>
		</form>

		<?php if ( $ementash_aside ) : ?>
			<aside class="em-contact__side">
				<?php if ( $ementash_map ) : ?>
					<figure class="em-map">
						<?php
						echo wp_get_attachment_image(
							$ementash_map,
							'large',
							false,
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 900px) 100vw, 540px',
							)
						);
						?>
						<?php if ( '' !== $ementash_route && '' !== (string) ementash_get( 'contact_map_label' ) ) : ?>
							<figcaption>
								<a class="em-map__link" href="<?php echo esc_url( $ementash_route ); ?>" target="_blank" rel="noopener"><?php echo ementash_bdi( ementash_get( 'contact_map_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( '(در پنجره جدید)', 'ementash' ); ?></span></a>
							</figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<?php if ( '' !== $ementash_hours || '' !== $ementash_email ) : ?>
					<ul class="em-cinfo">
						<?php if ( '' !== $ementash_hours ) : ?>
							<li><?php echo ementash_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'ساعات کاری:', 'ementash' ); ?> <?php echo ementash_bdi( $ementash_hours, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></li>
						<?php endif; ?>
						<?php if ( '' !== $ementash_email ) : ?>
							<li><?php echo ementash_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'ایمیل:', 'ementash' ); ?> <bdi dir="ltr"><a href="<?php echo esc_url( 'mailto:' . $ementash_email ); ?>"><?php echo esc_html( $ementash_email ); ?></a></bdi></span></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</aside>
		<?php endif; ?>
	</div>
</section>
