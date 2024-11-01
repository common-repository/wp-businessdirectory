<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *BusinessDirectory_Admin_Notices Class.
 */
class BusinessDirectory_Admin_Notices {

	/**
	 * Stores notices.
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$notices = get_option( 'businessdirectory_admin_notices', array() );
		add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );
		
		add_action( 'admin_print_styles', array( __CLASS__, 'display_notices' ) );
	}

	/**
	 * Store notices to WP
	 */
	public static function store_notices() {
		update_option( 'businessdirectory_admin_notices', self::get_notices() );
	}

	/**
	 * Get notices
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Remove all notices.
	 */
	public static function remove_all_notices() {
		self::$notices = array();
	}

	/**
	 * Add a notice.
	 * @param string $notice
	 */
	public static function add_notice( $text, $type ) {
	    self::$notices[] = array($text, $type);
	}

	/**
	 * Display notices  if needed.
	 */
	public static function display_notices() {
		$notices = self::get_notices();

		if ( ! empty( $notices ) ) {
			add_action( 'admin_notices', array( __CLASS__, "render_notices" ) );
		}
	}
	
	/**
	 * Render notices.
	 * @param string $notice
	 */
	public static function render_notices() {
		$notices = self::get_notices();
            
		if ( ! empty( $notices ) ) {
		    $content = "";
			foreach ( $notices as $notice ) {
			    $content .= "<div class=\"notice-$notice[1] notice\">";
			    $content .= "<p>".$notice[0]."</p>";
			    $content .= "</div>";
			}
		}
		
		self::remove_all_notices();
		
		echo $content;
		
		//var_dump( $content);
	}
	
}

BusinessDirectory_Admin_Notices::init();
