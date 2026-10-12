<?php
/**
 * Item price, badge and availability fields.
 *
 * @package WP_QR_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qrm_add_metaboxes() {
	add_meta_box(
		'qrm_item_details',
		__( 'Menu Item Details', 'wp-qr-menu' ),
		'qrm_render_item_details',
		'qrm_item',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'qrm_add_metaboxes' );

function qrm_render_item_details( $post ) {
	wp_nonce_field( 'qrm_item_details', 'qrm_item_details_nonce' );

	$price     = get_post_meta( $post->ID, '_qrm_price', true );
	$badge     = get_post_meta( $post->ID, '_qrm_badge', true );
	$available = get_post_meta( $post->ID, '_qrm_available', true );
	$available = '' === $available ? '1' : $available;
	?>
	<p>
		<label for="qrm_price"><strong><?php esc_html_e( 'Price', 'wp-qr-menu' ); ?></strong></label>
		<input type="text" id="qrm_price" name="qrm_price" class="widefat" style="margin-top:4px"
			value="<?php echo esc_attr( $price ); ?>" placeholder="8.50" />
	</p>
	<p>
		<label for="qrm_badge"><strong><?php esc_html_e( 'Badge (optional)', 'wp-qr-menu' ); ?></strong></label>
		<input type="text" id="qrm_badge" name="qrm_badge" class="widefat" style="margin-top:4px"
			value="<?php echo esc_attr( $badge ); ?>" placeholder="<?php esc_attr_e( 'Chef\'s pick', 'wp-qr-menu' ); ?>" />
	</p>
	<p>
		<label>
			<input type="checkbox" name="qrm_available" value="1" <?php checked( $available, '1' ); ?> />
			<?php esc_html_e( 'Available today', 'wp-qr-menu' ); ?>
		</label>
	</p>
	<?php
}

function qrm_save_item_details( $post_id ) {
	if ( ! isset( $_POST['qrm_item_details_nonce'] ) ||
		! wp_verify_nonce( sanitize_key( $_POST['qrm_item_details_nonce'] ), 'qrm_item_details' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$price = isset( $_POST['qrm_price'] ) ? sanitize_text_field( wp_unslash( $_POST['qrm_price'] ) ) : '';
	$badge = isset( $_POST['qrm_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['qrm_badge'] ) ) : '';
	$avail = isset( $_POST['qrm_available'] ) ? '1' : '0';

	update_post_meta( $post_id, '_qrm_price', $price );
	update_post_meta( $post_id, '_qrm_badge', $badge );
	update_post_meta( $post_id, '_qrm_available', $avail );
}
add_action( 'save_post_qrm_item', 'qrm_save_item_details' );

/**
 * Price is the field restaurants care about — surface it in the items list.
 */
function qrm_items_columns( $columns ) {
	$columns['qrm_price']       = __( 'Price', 'wp-qr-menu' );
	$columns['qrm_available']   = __( 'Available', 'wp-qr-menu' );
	return $columns;
}
add_filter( 'manage_qrm_item_posts_columns', 'qrm_items_columns' );

function qrm_items_column_content( $column, $post_id ) {
	if ( 'qrm_price' === $column ) {
		$price = get_post_meta( $post_id, '_qrm_price', true );
		echo $price ? esc_html( $price ) : '&mdash;';
	}
	if ( 'qrm_available' === $column ) {
		$available = get_post_meta( $post_id, '_qrm_available', true );
		echo '1' === (string) $available || '' === $available
			? '<span style="color:#16a34a">&#10003;</span>'
			: '<span style="color:#dc2626">&#10007;</span>';
	}
}
add_action( 'manage_qrm_item_posts_custom_column', 'qrm_items_column_content', 10, 2 );
