<?php
/**
 * Plugin Name:       WP QR Menu
 * Plugin URI:        https://github.com/chaze05/wp-qr-menu
 * Description:       Turn any WordPress site into a QR digital menu for restaurants — menu items with categories and prices, per-table QR codes with printable cards, and one-tap handoff to your ordering system.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Rani M. Rubillos
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-qr-menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QRM_VERSION', '1.0.0' );
define( 'QRM_PATH', plugin_dir_path( __FILE__ ) );
define( 'QRM_URL', plugin_dir_url( __FILE__ ) );

require_once QRM_PATH . 'includes/cpt.php';
require_once QRM_PATH . 'includes/metaboxes.php';
require_once QRM_PATH . 'includes/shortcode.php';
require_once QRM_PATH . 'includes/admin-qr.php';
require_once QRM_PATH . 'includes/settings.php';

/**
 * On activation: register the post type, make sure a menu page exists,
 * and flush rewrite rules once.
 */
function qrm_activate() {
	qrm_register_cpt();

	$page_id = (int) get_option( 'qrm_menu_page_id' );
	if ( ! $page_id || 'page' !== get_post_status( $page_id ) ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Menu', 'wp-qr-menu' ),
				'post_name'    => 'menu',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '[qr_menu]',
			)
		);

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_option( 'qrm_menu_page_id', (int) $page_id );
		}
	}

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'qrm_activate' );

register_deactivation_hook(
	__FILE__,
	function () {
		flush_rewrite_rules();
	}
);
