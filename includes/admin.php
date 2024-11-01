<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Utility class for admin
 * 
 * @author George
 *
 */

class WPBDAdmin{

    
    /**
     * The admin notices key.
     */
    const ADMIN_NOTICES_KEY = 'wpbd_admin_notices';
    
    /**
     * Admin constructor.
     *
     * Initializing WP-BusinessDirectory in WordPress admin.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct() {
        
        add_action( 'admin_notices', [ $this, 'admin_notices' ] );
        add_action( 'admin_notices', [ $this, 'admin_upgrade_notices' ] );
        add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ] );
        
        add_action( 'wp_ajax_wpbd_set_admin_notice_viewed', [ __CLASS__, 'ajax_set_admin_notice_viewed' ] );
        
        $this->wpbdDeployment = new WPBDDeployment();
    }
    
   /**
    * Admin notices.
    * 
    * @since 1.0.0
	* @access public
    */
	public function admin_notices() {
		$admin_notice = WPBDApi::get_admin_notice();
		if ( empty( $admin_notice ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! in_array( get_current_screen()->id, [ 'toplevel_page_jbd_businessdirectory', 'dashboard' ], true ) ) {
			return;
		}
		$notice_id = 'admin_notice_api_' . $admin_notice['notice_id'];
		if ( self::is_user_notice_viewed( $notice_id ) ) {
			return;
		}
		?>
		<div class="notice is-dismissible updated wpbd-message-dismissed wpbd-message-announcement" data-notice_id="<?php echo esc_attr( $notice_id ); ?>">
			<p><?php echo $admin_notice['notice_text']; ?></p>
		</div>
		<?php
	}

	/**
	 * Admin upgrade notices.
	 *
	 * Add WP-BusinessDirectory upgrades notices to WordPress admin screen.
	 *
	 * Fired by `admin_notices` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_upgrade_notices() {
	    $deployInfo = WPBDApi::get_deployment_info();
	    $new_version = $deployInfo["plugin_info"]["new_version"];
	    
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}
		
		if ( ! in_array( get_current_screen()->id, [ 'toplevel_page_jbd_businessdirectory', 'plugins', 'dashboard' ], true ) ) {
			return;
		}

		// Check if have any upgrades.
		$update_plugins = get_site_transient( 'update_plugins' );
    		
		$has_remote_update_package = ! ( empty( $update_plugins ) || empty( $update_plugins->response[ WP_BUSINESSDIRECTORY_PLUGIN_BASE ] ) || empty( $update_plugins->response[ WP_BUSINESSDIRECTORY_PLUGIN_BASE ]->package ) );

		if ( ! $has_remote_update_package ) {
			return;
		}

		$product = $update_plugins->response[ WP_BUSINESSDIRECTORY_PLUGIN_BASE ];

		$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $product->slug . '&section=changelog&TB_iframe=true&width=600&height=800' );
		$upgrade_url = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . WP_BUSINESSDIRECTORY_PLUGIN_BASE ), 'upgrade-plugin_' . WP_BUSINESSDIRECTORY_PLUGIN_BASE );

		// Check if have upgrade notices to show.
		if ( version_compare( WP_BUSINESSDIRECTORY_VERSION_NUM, $new_version, '>=' ) ) {
			return;
		}

		$notice_id = 'upgrade_notice_' . $new_version;
		if ( self::is_user_notice_viewed( $notice_id ) ) {
			//return;
		}

		$appSettigns = JBusinessUtil::getApplicationSettings();
		$orderUpdateUrl= self_admin_url('admin.php?page=jbd_updates');
		
		?>
		<div class="notice updated is-dismissible wpbd-message wpbd-message-dismissed" data-notice_id="<?php echo esc_attr( $notice_id ); ?>">
			<div class="wpbd-message-inner">
				<div class="wpbd-message-icon">
					<div class="wpbd-logo-wrapper"></div>
				</div>
				<div class="wpbd-message-content">
					<strong><?php echo __( 'Update Notification', 'wp-businessdirectory' ); ?></strong>
					<p>
						<?php
							if(empty($appSettigns->order_id) || empty($appSettigns->order_email)){
								printf(
									__( 'There is a new version of WP-BusinessDirectory available. <br/>
										 In order to update to the latest version you will need to enter the order details in the <b><a href="%1$s">Update details</a></b> section. 
										<a href="%2$s" class="thickbox open-plugin-details-modal" aria-label="%3$s">View version %4$s details</a>', 'wp-businessdirectory' ),
									esc_url($orderUpdateUrl),
									esc_url( $details_url ),
									esc_attr( sprintf(
										/* translators: %s: wpbd version */
										__( 'View WP-BusinessDirectory version %s details', 'wp-businessdirectory' ),
										$new_version
									) ),
									$new_version,
									esc_attr( __( 'Update WP-BusinessDirectory Now', 'wp-businessdirectory' ) )
								);
							}else{
								printf(
									__( 'There is a new version of WP-BusinessDirectory available. <a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">View version %3$s details</a> or <a href="%4$s" class="update-link" aria-label="%5$s">update now</a>.', 'wp-businessdirectory' ),
									esc_url( $details_url ),
									esc_attr( sprintf(
										/* translators: %s: wpbd version */
										__( 'View WP-BusinessDirectory version %s details', 'wp-businessdirectory' ),
										$new_version
									) ),
									$new_version,
									esc_url( $upgrade_url ),
									esc_attr( __( 'Update WP-BusinessDirectory Now', 'wp-businessdirectory' ) )
								);
							}
						?>
					</p>
				</div>
				<?php if(!empty($appSettigns->order_id) && !empty($appSettigns->order_email)){ ?>
					<div class="wpbd-message-action">
						<a class="button wpbd-button" href="<?php echo $upgrade_url; ?>">
							<i class="dashicons dashicons-update" aria-hidden="true"></i>
							<?php echo __( 'Update Now', 'wp-businessdirectory' ); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
		
		<?php
	}

	/**
	 * Admin footer text.
	 *
	 * Modifies the "Thank you" text displayed in the admin footer.
	 *
	 * Fired by `admin_footer_text` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $footer_text The content that will be printed.
	 *
	 * @return string The content that will be printed.
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		$is_wp_businessirectory_screen = ( $current_screen && false !== strpos( $current_screen->id, 'businessdirectory' ) );

		if ( $is_wp_businessirectory_screen ) {
			$footer_text = sprintf(
				__( 'Enjoyed %1$s? Please leave us a %2$s rating. We really appreciate your support!', 'wp-businessdirectory' ),
				'<strong>' . __( 'WP-BusinessDirectory', 'wp-businessdirectory' ) . '</strong>',
				'<a href="https://wordpress.org/support/plugin/wp-businessdirectory/reviews/?filter=5/#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}
	
	/**
	 * Is user notice viewed.
	 *
	 * Whether the notice was viewed by the user.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int $notice_id The notice ID.
	 *
	 * @return bool Whether the notice was viewed by the user.
	 */
	public static function is_user_notice_viewed( $notice_id ) {
	    $notices = get_user_meta( get_current_user_id(), self::ADMIN_NOTICES_KEY, true );
	    
	    if ( empty( $notices ) || empty( $notices[ $notice_id ] ) ) {
	        return false;
	    }
	    
	    return true;
	}
	
	
	/**
	 * Set admin notice as viewed.
	 *
	 * Flag the user admin notice as viewed using an authenticated ajax request.
	 *
	 * Fired by `wp_ajax_elementor_set_admin_notice_viewed` action.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function ajax_set_admin_notice_viewed() {
	    if ( empty( $_REQUEST['notice_id'] ) ) {
	        wp_die();
	    }
	    
	    $notices = get_user_meta( get_current_user_id(), self::ADMIN_NOTICES_KEY, true );
	    if ( empty( $notices ) ) {
	        $notices = [];
	    }
	    
	    $notices[ $_REQUEST['notice_id'] ] = 'true';
	    update_user_meta( get_current_user_id(), self::ADMIN_NOTICES_KEY, $notices );
	    
	    if ( ! wp_doing_ajax() ) {
	        wp_safe_redirect( admin_url() );
	        die;
	    }
	    
	    wp_die();
	}

}