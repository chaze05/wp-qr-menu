<?php
/**
 * Plugin settings: ordering handoff URL, accent colour, tagline.
 *
 * @package WP_QR_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qrm_settings_menu() {
	add_submenu_page(
		'edit.php?post_type=qrm_item',
		__( 'QR Menu Settings', 'wp-qr-menu' ),
		__( 'Settings', 'wp-qr-menu' ),
		'manage_options',
		'qrm-settings',
		'qrm_render_settings'
	);
}
add_action( 'admin_menu', 'qrm_settings_menu' );

function qrm_register_settings() {
	register_setting(
		'qrm_settings',
		'qrm_order_base',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);
	register_setting(
		'qrm_settings',
		'qrm_accent',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_hex_color',
			'default'           => '#2ED47A',
		)
	);
	register_setting(
		'qrm_settings',
		'qrm_tagline',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'Scan · Browse · Order',
		)
	);
}
add_action( 'admin_init', 'qrm_register_settings' );

function qrm_render_settings() {
	$menu_page_id = (int) get_option( 'qrm_menu_page_id' );
	?>
	<div class="wrap qrm-wrap">
		<h1><?php esc_html_e( 'QR Menu Settings', 'wp-qr-menu' ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'qrm_settings' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="qrm_order_base"><?php esc_html_e( 'Ordering app URL', 'wp-qr-menu' ); ?></label>
					</th>
					<td>
						<input type="url" id="qrm_order_base" name="qrm_order_base" class="regular-text"
							value="<?php echo esc_attr( get_option( 'qrm_order_base', '' ) ); ?>"
							placeholder="https://your-ordering-app.example.com/menu" />
						<p class="description">
							<?php esc_html_e( 'When a guest opens the menu with a table QR code, an "Order" button appears linking here with ?table= appended. Leave empty to disable.', 'wp-qr-menu' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="qrm_accent"><?php esc_html_e( 'Accent colour', 'wp-qr-menu' ); ?></label>
					</th>
					<td>
						<input type="color" id="qrm_accent" name="qrm_accent"
							value="<?php echo esc_attr( get_option( 'qrm_accent', '#2ED47A' ) ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="qrm_tagline"><?php esc_html_e( 'Menu tagline', 'wp-qr-menu' ); ?></label>
					</th>
					<td>
						<input type="text" id="qrm_tagline" name="qrm_tagline" class="regular-text"
							value="<?php echo esc_attr( get_option( 'qrm_tagline', 'Scan · Browse · Order' ) ); ?>" />
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>

		<?php if ( $menu_page_id && 'page' === get_post_status( $menu_page_id ) ) : ?>
			<p class="description">
				<?php
				printf(
					/* translators: %s: menu page URL. */
					esc_html__( 'Your menu page lives at %s — edit it like any page.', 'wp-qr-menu' ),
					'<a href="' . esc_url( get_permalink( $menu_page_id ) ) . '">' . esc_html( get_permalink( $menu_page_id ) ) . '</a>'
				);
				?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}
