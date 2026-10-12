<?php
/**
 * Table QR codes: management screen with printable, downloadable codes.
 *
 * @package WP_QR_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qrm_qr_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=qrm_item',
		__( 'Table QR Codes', 'wp-qr-menu' ),
		__( 'Table QR Codes', 'wp-qr-menu' ),
		'manage_options',
		'qrm-codes',
		'qrm_render_qr_page'
	);
}
add_action( 'admin_menu', 'qrm_qr_admin_menu' );

function qrm_handle_tables_save() {
	if ( ! isset( $_POST['qrm_tables_nonce'] ) ||
		! wp_verify_nonce( sanitize_key( $_POST['qrm_tables_nonce'] ), 'qrm_save_tables' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$raw = isset( $_POST['qrm_tables'] ) ? sanitize_textarea_field( wp_unslash( $_POST['qrm_tables'] ) ) : '';
	$lines = preg_split( '/[\r\n,]+/', $raw );
	$tables = array();
	foreach ( (array) $lines as $line ) {
		$name = trim( $line );
		if ( '' !== $name ) {
			$tables[] = $name;
		}
	}
	$tables = array_values( array_unique( $tables ) );

	update_option( 'qrm_tables', $tables );
}
add_action( 'admin_init', 'qrm_handle_tables_save' );

function qrm_tables() {
	$tables = get_option( 'qrm_tables', array() );
	return is_array( $tables ) ? $tables : array();
}

function qrm_menu_url_for_table( $table ) {
	$page_id = (int) get_option( 'qrm_menu_page_id' );
	$base    = $page_id ? get_permalink( $page_id ) : home_url( '/' );
	return add_query_arg( 'table', rawurlencode( $table ), $base );
}

function qrm_render_qr_page() {
	$tables = qrm_tables();

	wp_enqueue_style( 'qrm-admin', QRM_URL . 'assets/css/admin-qr.css', array(), QRM_VERSION );
	wp_enqueue_script( 'qrm-qrcode-lib', QRM_URL . 'assets/js/qrcode.js', array(), '1.4.4', true );
	wp_enqueue_script( 'qrm-admin', QRM_URL . 'assets/js/admin-qr.js', array( 'qrm-qrcode-lib' ), QRM_VERSION, true );
	?>
	<div class="wrap qrm-wrap">
		<h1><?php esc_html_e( 'Table QR Codes', 'wp-qr-menu' ); ?></h1>
		<p class="qrm-lead">
			<?php esc_html_e( 'Each code opens your digital menu with the table already selected — print a card per table, or download the PNGs.', 'wp-qr-menu' ); ?>
		</p>

		<form method="post" class="qrm-tables-form">
			<?php wp_nonce_field( 'qrm_save_tables', 'qrm_tables_nonce' ); ?>
			<label for="qrm_tables"><strong><?php esc_html_e( 'Tables', 'wp-qr-menu' ); ?></strong></label>
			<textarea id="qrm_tables" name="qrm_tables" rows="3" class="large-text"
				placeholder="1, 2, 3, 4, 5"><?php echo esc_textarea( implode( ', ', $tables ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Separate with commas or new lines. Names are fine too — "Bar 1", "Patio A".', 'wp-qr-menu' ); ?></p>
			<p>
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save tables', 'wp-qr-menu' ); ?></button>
				<?php if ( $tables ) : ?>
					<button type="button" class="button" id="qrm-print"><?php esc_html_e( 'Print cards', 'wp-qr-menu' ); ?></button>
				<?php endif; ?>
			</p>
		</form>

		<?php if ( empty( $tables ) ) : ?>
			<div class="qrm-empty-state">
				<p><?php esc_html_e( 'Add your tables above to generate QR codes.', 'wp-qr-menu' ); ?></p>
			</div>
		<?php else : ?>
			<div class="qrm-cards">
				<?php foreach ( $tables as $table ) : ?>
					<div class="qrm-card">
						<div class="qrm-card-code" data-qrm-url="<?php echo esc_url( qrm_menu_url_for_table( $table ) ); ?>"></div>
						<div class="qrm-card-meta">
							<strong><?php printf( esc_html__( 'Table %s', 'wp-qr-menu' ), esc_html( $table ) ); ?></strong>
							<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
							<span class="qrm-card-hint"><?php esc_html_e( 'Scan for the menu', 'wp-qr-menu' ); ?></span>
						</div>
						<button type="button" class="button qrm-download"><?php esc_html_e( 'Download PNG', 'wp-qr-menu' ); ?></button>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
