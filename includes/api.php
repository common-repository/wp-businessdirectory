<?php 

/**
 * WP-Businessdirectory API.
 *
 * WP-Businessdirectory API handler class is responsible for communicating with 
 * remote servers retrieving data.
 *
 * @since 1.1.2
 */
class WPBDApi {

	/**
	 * API info URL.
	 *
	 * Holds the URL of the info API.
	 *
	 * @access public
	 * @static
	 *
	 * @var string API info URL.
	 */
	public static $api_info_url = 'https://updates.cmsjunkie.com/directory/wp-businessdirectory.json';

	/**
	 * Get info data.
	 *
	 * This function notifies the user of upgrade notices, new templates and contributors.
	 *
	 * @since 1.1.2
	 * @access private
	 * @static
	 *
	 * @param bool $force_update Optional. Whether to force the data retrieval or
	 *                                     not. Default is false.
	 *
	 * @return array|false Info data, or false.
	 */
	private static function get_info_data( $force_update = false ) {
	    $cache_key = 'wpbd_remote_info_api_data_' . WP_BUSINESSDIRECTORY_VERSION_NUM;

		$info_data = get_transient( $cache_key );

		if ( $force_update || false === $info_data ) {
			$timeout = ( $force_update ) ? 25 : 8;

			$response = wp_remote_get( self::$api_info_url, [
				'timeout' => $timeout,
			    'headers' => array(
			        'Accept' => 'application/json'
			    ), 
				'body' => [
					// Which API version is used.
				    'api_version' => WP_BUSINESSDIRECTORY_VERSION_NUM,
					// Which language to return.
					'site_lang' => get_bloginfo( 'language' ),
				],
			] );
			
			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				set_transient( $cache_key, [], 2 * HOUR_IN_SECONDS );

				return false;
			}

			$info_data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $info_data ) || ! is_array( $info_data ) ) {
				set_transient( $cache_key, [], 2 * HOUR_IN_SECONDS );

				return false;
			}

			set_transient( $cache_key, $info_data, 12 * HOUR_IN_SECONDS );
		}
		
		return $info_data;
	}

	/**
	 * Get upgrade notice.
	 *
	 * Retrieve the upgrade notice if one exists, or false otherwise.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array|false Upgrade notice, or false none exist.
	 */
	public static function get_upgrade_notice() {
		$data = self::get_info_data();

		if ( empty( $data['upgrade_notice'] ) ) {
			return false;
		}

		return $data['upgrade_notice'];
	}

	
	/**
	 * Get admin notice.
	 *
	 * Retrieve the admin notice if one exists, or false otherwise.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array|false Admin notice, or false none exist.
	 */
	public static function get_admin_notice() {
		$data = self::get_info_data();
		if ( empty( $data['admin_notice'] ) ) {
			return false;
		}
		return $data['admin_notice'];
	}
	
	/**
	 * Get deployment info.
	 *
	 * Retrieve the deployment info if one exists, or false otherwise.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array|false Deployment info, or false none exist.
	 */
	public static function get_deployment_info( $force = false ) {
	    $data = self::get_info_data( $force );
	    
	    if ( empty( $data['deployment'] ) ) {
	        return false;
	    }
	    
	    return $data['deployment'];
	}
}