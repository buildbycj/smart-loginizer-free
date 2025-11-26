<?php
/**
 * Page Restriction Admin functionality.
 *
 * @package SmartLoginizer\Admin
 */

namespace SmartLoginizer\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page Restriction Admin class.
 */
class Page_Restriction {

	/**
	 * Initialize page restriction admin.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_restriction_settings' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add meta boxes for page restriction.
	 *
	 * @return void
	 */
	public function add_meta_boxes(): void {
		$post_types = get_post_types( array( 'public' => true ), 'names' );
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'smart_loginizer_page_restriction',
				__( 'Page Restriction', 'smart-loginizer' ),
				array( $this, 'render_meta_box' ),
				$post_type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Render meta box content.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'smart_loginizer_save_restriction', 'smart_loginizer_restriction_nonce' );

		$restriction_enabled = get_post_meta( $post->ID, '_smart_loginizer_restriction_enabled', true );
		$restriction_type    = get_post_meta( $post->ID, '_smart_loginizer_restriction_type', true );
		$restriction_roles   = get_post_meta( $post->ID, '_smart_loginizer_restriction_roles', true );
		$restriction_message = get_post_meta( $post->ID, '_smart_loginizer_restriction_message', true );
		$restriction_action  = get_post_meta( $post->ID, '_smart_loginizer_restriction_action', true );
		$restriction_redirect = get_post_meta( $post->ID, '_smart_loginizer_restriction_redirect', true );
		$restriction_redirect_url = get_post_meta( $post->ID, '_smart_loginizer_restriction_redirect_url', true );

		// Default values.
		$restriction_enabled = '' === $restriction_enabled ? 'no' : $restriction_enabled;
		$restriction_type    = '' === $restriction_type ? 'logged_in' : $restriction_type;
		$restriction_roles   = is_array( $restriction_roles ) ? $restriction_roles : array();
		$restriction_message = '' === $restriction_message ? __( 'You do not have permission to access this page.', 'smart-loginizer' ) : $restriction_message;
		$restriction_action  = '' === $restriction_action ? 'message' : $restriction_action;
		$restriction_redirect = '' === $restriction_redirect ? 0 : absint( $restriction_redirect );
		$restriction_redirect_url = '' === $restriction_redirect_url ? '' : $restriction_redirect_url;

		// Get user roles.
		$user_roles = wp_roles()->get_names();

		?>
		<div class="smart-loginizer-restriction-meta-box">
			<p>
				<label>
					<input type="checkbox" name="smart_loginizer_restriction_enabled" value="yes" <?php checked( $restriction_enabled, 'yes' ); ?> />
					<?php esc_html_e( 'Enable Page Restriction', 'smart-loginizer' ); ?>
				</label>
			</p>

			<div class="smart-loginizer-restriction-options" style="<?php echo 'yes' !== $restriction_enabled ? 'display: none;' : ''; ?>">
				<p>
					<label for="smart_loginizer_restriction_type">
						<strong><?php esc_html_e( 'Restrict Access To:', 'smart-loginizer' ); ?></strong>
					</label>
					<select name="smart_loginizer_restriction_type" id="smart_loginizer_restriction_type" class="widefat">
						<option value="logged_out" <?php selected( $restriction_type, 'logged_out' ); ?>>
							<?php esc_html_e( 'Logged Out Users', 'smart-loginizer' ); ?>
						</option>
						<option value="logged_in" <?php selected( $restriction_type, 'logged_in' ); ?>>
							<?php esc_html_e( 'Logged In Users', 'smart-loginizer' ); ?>
						</option>
						<option value="specific_roles" <?php selected( $restriction_type, 'specific_roles' ); ?>>
							<?php esc_html_e( 'Specific User Roles', 'smart-loginizer' ); ?>
						</option>
					</select>
				</p>

				<div id="smart_loginizer_roles_container" style="<?php echo 'specific_roles' !== $restriction_type ? 'display: none;' : ''; ?>">
					<p>
						<label for="smart_loginizer_restriction_roles">
							<strong><?php esc_html_e( 'Select Roles:', 'smart-loginizer' ); ?></strong>
						</label>
						<?php foreach ( $user_roles as $role_key => $role_name ) : ?>
							<label style="display: block; margin: 5px 0;">
								<input type="checkbox" name="smart_loginizer_restriction_roles[]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $restriction_roles, true ) ); ?> />
								<?php echo esc_html( $role_name ); ?>
							</label>
						<?php endforeach; ?>
					</p>
				</div>

				<p>
					<label for="smart_loginizer_restriction_action">
						<strong><?php esc_html_e( 'Action When Restricted:', 'smart-loginizer' ); ?></strong>
					</label>
					<select name="smart_loginizer_restriction_action" id="smart_loginizer_restriction_action" class="widefat">
						<option value="message" <?php selected( $restriction_action, 'message' ); ?>>
							<?php esc_html_e( 'Show Message', 'smart-loginizer' ); ?>
						</option>
						<option value="redirect_login" <?php selected( $restriction_action, 'redirect_login' ); ?>>
							<?php esc_html_e( 'Redirect to Login Page', 'smart-loginizer' ); ?>
						</option>
						<option value="redirect_page" <?php selected( $restriction_action, 'redirect_page' ); ?>>
							<?php esc_html_e( 'Redirect to Page', 'smart-loginizer' ); ?>
						</option>
						<option value="redirect_url" <?php selected( $restriction_action, 'redirect_url' ); ?>>
							<?php esc_html_e( 'Redirect to Custom URL', 'smart-loginizer' ); ?>
						</option>
					</select>
				</p>

				<div id="smart_loginizer_message_container" style="<?php echo 'message' !== $restriction_action ? 'display: none;' : ''; ?>">
					<p>
						<label for="smart_loginizer_restriction_message">
							<strong><?php esc_html_e( 'Restriction Message:', 'smart-loginizer' ); ?></strong>
						</label>
						<textarea name="smart_loginizer_restriction_message" id="smart_loginizer_restriction_message" class="widefat" rows="3"><?php echo esc_textarea( $restriction_message ); ?></textarea>
					</p>
				</div>

				<div id="smart_loginizer_redirect_page_container" style="<?php echo 'redirect_page' !== $restriction_action ? 'display: none;' : ''; ?>">
					<p>
						<label for="smart_loginizer_restriction_redirect">
							<strong><?php esc_html_e( 'Redirect Page:', 'smart-loginizer' ); ?></strong>
						</label>
						<select name="smart_loginizer_restriction_redirect" id="smart_loginizer_restriction_redirect" class="widefat">
							<option value="0"><?php esc_html_e( '-- Select a page or post --', 'smart-loginizer' ); ?></option>
							<?php
							// Get all pages and posts.
							$pages_and_posts = get_posts(
								array(
									'post_type'      => array( 'page', 'post' ),
									'posts_per_page' => -1,
									'post_status'    => 'publish',
									'orderby'        => 'post_type',
									'order'          => 'ASC',
								)
							);

							// Group by post type for better organization.
							$grouped = array();
							foreach ( $pages_and_posts as $item ) {
								$post_type_obj = get_post_type_object( $item->post_type );
								$type_label    = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst( $item->post_type );
								if ( ! isset( $grouped[ $type_label ] ) ) {
									$grouped[ $type_label ] = array();
								}
								$grouped[ $type_label ][] = $item;
							}

							// Output grouped options.
							foreach ( $grouped as $type_label => $items ) {
								printf( '<optgroup label="%s">', esc_attr( $type_label ) );
								foreach ( $items as $item ) {
									printf(
										'<option value="%d" %s>%s</option>',
										esc_attr( $item->ID ),
										selected( $restriction_redirect, $item->ID, false ),
										esc_html( $item->post_title )
									);
								}
								echo '</optgroup>';
							}
							?>
						</select>
					</p>
				</div>

				<div id="smart_loginizer_redirect_url_container" style="<?php echo 'redirect_url' !== $restriction_action ? 'display: none;' : ''; ?>">
					<p>
						<label for="smart_loginizer_restriction_redirect_url">
							<strong><?php esc_html_e( 'Redirect URL:', 'smart-loginizer' ); ?></strong>
						</label>
						<input type="url" name="smart_loginizer_restriction_redirect_url" id="smart_loginizer_restriction_redirect_url" class="widefat" value="<?php echo esc_attr( $restriction_redirect_url ); ?>" placeholder="<?php echo esc_attr( 'https://example.com' ); ?>" />
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save restriction settings.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public function save_restriction_settings( int $post_id, \WP_Post $post ): void {
		// Verify nonce.
		if ( ! isset( $_POST['smart_loginizer_restriction_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['smart_loginizer_restriction_nonce'] ) ), 'smart_loginizer_save_restriction' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save restriction enabled.
		$restriction_enabled = isset( $_POST['smart_loginizer_restriction_enabled'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['smart_loginizer_restriction_enabled'] ) ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_smart_loginizer_restriction_enabled', $restriction_enabled );

		if ( 'yes' === $restriction_enabled ) {
			// Save restriction type.
			if ( isset( $_POST['smart_loginizer_restriction_type'] ) ) {
				$restriction_type = sanitize_text_field( wp_unslash( $_POST['smart_loginizer_restriction_type'] ) );
				$allowed_types    = array( 'logged_out', 'logged_in', 'specific_roles' );
				if ( in_array( $restriction_type, $allowed_types, true ) ) {
					update_post_meta( $post_id, '_smart_loginizer_restriction_type', $restriction_type );
				}
			}

			// Save restriction roles.
			if ( isset( $_POST['smart_loginizer_restriction_roles'] ) && is_array( $_POST['smart_loginizer_restriction_roles'] ) ) {
				$restriction_roles = array_map( 'sanitize_key', wp_unslash( $_POST['smart_loginizer_restriction_roles'] ) );
				update_post_meta( $post_id, '_smart_loginizer_restriction_roles', $restriction_roles );
			} else {
				delete_post_meta( $post_id, '_smart_loginizer_restriction_roles' );
			}

			// Save restriction message.
			if ( isset( $_POST['smart_loginizer_restriction_message'] ) ) {
				$restriction_message = sanitize_textarea_field( wp_unslash( $_POST['smart_loginizer_restriction_message'] ) );
				update_post_meta( $post_id, '_smart_loginizer_restriction_message', $restriction_message );
			}

			// Save restriction action.
			if ( isset( $_POST['smart_loginizer_restriction_action'] ) ) {
				$restriction_action = sanitize_text_field( wp_unslash( $_POST['smart_loginizer_restriction_action'] ) );
				$allowed_actions    = array( 'message', 'redirect_login', 'redirect_page', 'redirect_url' );
				if ( in_array( $restriction_action, $allowed_actions, true ) ) {
					update_post_meta( $post_id, '_smart_loginizer_restriction_action', $restriction_action );
				}
			}

			// Save redirect page.
			if ( isset( $_POST['smart_loginizer_restriction_redirect'] ) ) {
				$restriction_redirect = absint( $_POST['smart_loginizer_restriction_redirect'] );
				update_post_meta( $post_id, '_smart_loginizer_restriction_redirect', $restriction_redirect );
			}

			// Save redirect URL.
			if ( isset( $_POST['smart_loginizer_restriction_redirect_url'] ) ) {
				$restriction_redirect_url = esc_url_raw( wp_unslash( $_POST['smart_loginizer_restriction_redirect_url'] ) );
				update_post_meta( $post_id, '_smart_loginizer_restriction_redirect_url', $restriction_redirect_url );
			}
		} else {
			// Clean up meta if restriction is disabled.
			delete_post_meta( $post_id, '_smart_loginizer_restriction_type' );
			delete_post_meta( $post_id, '_smart_loginizer_restriction_roles' );
			delete_post_meta( $post_id, '_smart_loginizer_restriction_message' );
			delete_post_meta( $post_id, '_smart_loginizer_restriction_action' );
			delete_post_meta( $post_id, '_smart_loginizer_restriction_redirect' );
			delete_post_meta( $post_id, '_smart_loginizer_restriction_redirect_url' );
		}
	}

	/**
	 * Enqueue admin scripts for meta box.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_scripts( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		wp_add_inline_script(
			'jquery',
			"
			jQuery(document).ready(function($) {
				// Toggle restriction options.
				$('#smart_loginizer_restriction_enabled').on('change', function() {
					if ($(this).is(':checked')) {
						$('.smart-loginizer-restriction-options').show();
					} else {
						$('.smart-loginizer-restriction-options').hide();
					}
				});

				// Toggle roles container.
				$('#smart_loginizer_restriction_type').on('change', function() {
					if ($(this).val() === 'specific_roles') {
						$('#smart_loginizer_roles_container').show();
					} else {
						$('#smart_loginizer_roles_container').hide();
					}
				});

				// Toggle action containers.
				$('#smart_loginizer_restriction_action').on('change', function() {
					var action = $(this).val();
					$('#smart_loginizer_message_container').toggle(action === 'message');
					$('#smart_loginizer_redirect_page_container').toggle(action === 'redirect_page');
					$('#smart_loginizer_redirect_url_container').toggle(action === 'redirect_url');
				});
			});
			"
		);
	}
}

