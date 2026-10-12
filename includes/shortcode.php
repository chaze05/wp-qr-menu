<?php
/**
 * The [qr_menu] shortcode — the customer-facing digital menu.
 *
 * @package WP_QR_Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qrm_menu_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => get_bloginfo( 'name' ),
		),
		$atts,
		'qr_menu'
	);

	$table = isset( $_GET['table'] ) ? sanitize_text_field( wp_unslash( $_GET['table'] ) ) : '';

	wp_enqueue_style( 'qrm-menu', QRM_URL . 'assets/css/menu.css', array(), QRM_VERSION );

	$accent = get_option( 'qrm_accent', '#2ED47A' );
	$tagline = get_option( 'qrm_tagline', __( 'Scan · Browse · Order', 'wp-qr-menu' ) );

	$categories = get_terms(
		array(
			'taxonomy'   => 'qrm_category',
			'hide_empty' => true,
			'orderby'    => 'name',
		)
	);

	ob_start();
	?>
	<div class="qrm-menu" style="--qrm-accent: <?php echo esc_attr( $accent ); ?>">
		<header class="qrm-header">
			<p class="qrm-tagline"><?php echo esc_html( $tagline ); ?></p>
			<h1 class="qrm-title"><?php echo esc_html( $atts['title'] ); ?></h1>
			<?php if ( $table ) : ?>
				<p class="qrm-table"><?php printf( esc_html__( 'Table %s', 'wp-qr-menu' ), esc_html( $table ) ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( empty( $categories ) || is_wp_error( $categories ) ) : ?>
			<p class="qrm-empty"><?php esc_html_e( 'The menu is being prepared. Please check back soon.', 'wp-qr-menu' ); ?></p>
		<?php else : ?>
			<nav class="qrm-nav">
				<?php foreach ( $categories as $category ) : ?>
					<a href="#qrm-cat-<?php echo esc_attr( $category->slug ); ?>" class="qrm-nav-chip">
						<?php echo esc_html( $category->name ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php
			foreach ( $categories as $category ) :
				$items = new WP_Query(
					array(
						'post_type'      => 'qrm_item',
						'posts_per_page' => 100,
						'orderby'        => array(
							'menu_order' => 'ASC',
							'title'      => 'ASC',
						),
						'tax_query'      => array(
							array(
								'taxonomy' => 'qrm_category',
								'field'    => 'term_id',
								'terms'    => $category->term_id,
							),
						),
					)
				);

				$visible = array();
				foreach ( $items->posts as $item ) {
					if ( '0' === (string) get_post_meta( $item->ID, '_qrm_available', true ) ) {
						continue;
					}
					$visible[] = $item;
				}
				wp_reset_postdata();

				if ( empty( $visible ) ) {
					continue;
				}
				?>
				<section class="qrm-category" id="qrm-cat-<?php echo esc_attr( $category->slug ); ?>">
					<h2 class="qrm-category-title"><?php echo esc_html( $category->name ); ?></h2>
					<div class="qrm-items">
						<?php foreach ( $visible as $item ) : ?>
							<article class="qrm-item">
								<?php if ( has_post_thumbnail( $item ) ) : ?>
									<div class="qrm-item-thumb">
										<?php echo get_the_post_thumbnail( $item, 'medium' ); ?>
									</div>
								<?php endif; ?>
								<div class="qrm-item-body">
									<div class="qrm-item-head">
										<h3 class="qrm-item-name"><?php echo esc_html( get_the_title( $item ) ); ?></h3>
										<span class="qrm-item-price">
											<?php echo esc_html( get_post_meta( $item->ID, '_qrm_price', true ) ); ?>
										</span>
									</div>
									<?php
									$badge = get_post_meta( $item->ID, '_qrm_badge', true );
									if ( $badge ) :
										?>
										<span class="qrm-badge"><?php echo esc_html( $badge ); ?></span>
									<?php endif; ?>
									<?php if ( $item->post_excerpt ) : ?>
										<p class="qrm-item-desc"><?php echo esc_html( $item->post_excerpt ); ?></p>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php
		$order_base = get_option( 'qrm_order_base', '' );
		if ( $table && $order_base ) :
			$order_url = add_query_arg( 'table', rawurlencode( $table ), $order_base );
			?>
			<div class="qrm-order-bar">
				<a class="qrm-order-button" href="<?php echo esc_url( $order_url ); ?>">
					<?php printf( esc_html__( 'Order at table %s', 'wp-qr-menu' ), esc_html( $table ) ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'qr_menu', 'qrm_menu_shortcode' );
