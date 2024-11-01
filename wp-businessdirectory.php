<?php
/*
 * Plugin Name: WP-BusinessDirectory
 * Plugin URI: https://www.cmsjunkie.com/wp-businessdirectory
 * Description: Professional Business Directory
 * Author: CMSJunkie
 * Version: 3.1.2
 * Author URI: https://www.cmsjunkie.com
  */
/**
 * @copyright	Copyright (C) 2008-2024 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program.  If not, see <https://www.gnu.org/licenses/agpl-3.0.en.html>.
 */
// DENY DIRECT ACCESS TO THE FILE
if (! defined('ABSPATH'))
    die('Restricted access');

$startTime = microtime(true); // Gets current microtime as one long string

define('WP_BUSINESSDIRECTORY_VERSION_NUM', '3.1.2');
define('WP_BUSINESSDIRECTORY_VERSION_KEY', 'wpbd_plugin_version');
define('WP_BUSINESSDIRECTORY_DB_VERSION_NUM', '3.1.2');
define('WP_BUSINESSDIRECTORY_DB_VERSION_KEY', 'wpbd_db_version');

define('WP_BUSINESSDIRECTORY_PREVIOUS_STABLE_VERSION_NUM', '3.1.1');

define('WP_BUSINESSDIRECTORY_PLUGIN_BASE', plugin_basename(__FILE__));

define('WP_BUSINESSDIRECTORY_PATH', plugin_dir_path(__FILE__));
define('WP_BUSINESSDIRECTORY_URL', plugin_dir_url(__FILE__));
define('WP_BUSINESSDIRECTORY_INCLUDE_PATH', plugin_dir_path(__FILE__) . "includes/jbd/");
define('WP_BUSINESS_DIRECTORY_BASE_URL', get_site_url());
define('WP_BUSINESS_DIRECTORY_ADMIN_URL', admin_url());
define('WP_BUSINESS_DIRECTORY_UPLOAD_DIRECTORY', "wp-businessdirectory");

define('BD_LANGUAGE_FOLDER_PATH', plugin_dir_path(__FILE__) . "admin/language");
define('BD_ASSETS_FOLDER_PATH', plugin_dir_url(__FILE__) . "site/assets/");
define('BD_HELPERS_PATH', plugin_dir_path(__FILE__) . "site/helpers");
define('BD_CLASSES_PATH', plugin_dir_path(__FILE__) . "site/classes");
define('BD_LIBRARIES_PATH', plugin_dir_path(__FILE__) . "site/libraries");

define("WPBD_UPLOAD_DIR", wp_upload_dir()["basedir"] . "/" . WP_BUSINESS_DIRECTORY_UPLOAD_DIRECTORY);
define("WPBD_UPLOAD_URL", wp_upload_dir()["baseurl"] . "/" . WP_BUSINESS_DIRECTORY_UPLOAD_DIRECTORY);

define('WP_BUSINESS_DIRECTORY_TMP_PATH', WPBD_UPLOAD_DIR . "/tmp");

define('BD_PICTURES_PATH', WPBD_UPLOAD_URL . "/pictures");
define('BD_PICTURES_UPLOAD_PATH', WPBD_UPLOAD_DIR . "/pictures");

define('BD_ATTACHMENT_PATH', WPBD_UPLOAD_URL . "/attachments");
define('BD_ATTACHMENT_UPLOAD_PATH', WPBD_UPLOAD_DIR . "/attachments");

define('BD_REVIEW_PICTURES_PATH', '/reviews/');

define('BD_ATTACHMENT_ICON_PATH', WPBD_UPLOAD_URL . "/attachments/icons/");

define('JDEBUG', 0);
define('JVERSION', 3);
define('_JEXEC', 1);

define("BD_COMPONENT_IMAGE_PATH", BD_ASSETS_FOLDER_PATH . "images/");
define('WP_BUSINESS_DIRECTORY_WP_UPLOAD_URL', WPBD_UPLOAD_URL);

define('BD_MOBILE_APP_BUILD_UPLOAD_PATH', WPBD_UPLOAD_DIR . '/mobilebuild');
define('BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH', WPBD_UPLOAD_URL . '/mobilebuild');

if (!is_admin()) {
    define('JPATH_COMPONENT', plugin_dir_path(__FILE__) . "site");
} else {
    define('JPATH_COMPONENT', plugin_dir_path(__FILE__) . "admin");
}

define('JPATH_COMPONENT_ADMINISTRATOR', plugin_dir_path(__FILE__) . "admin");
define('JPATH_COMPONENT_SITE', plugin_dir_path(__FILE__) . "site");

if (defined('WP_DEBUG') && true === WP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
}

if (! version_compare(PHP_VERSION, '5.6', '>=')) {
    add_action('admin_notices', 'wpbd_fail_php_version');
} elseif (! version_compare(get_bloginfo('version'), '5.0', '>=')) {
    add_action('admin_notices', 'wpbd_fail_wp_version');
}

include 'includes/mvc/mvc.php';

// Include Core Functionalities
include 'includes/businessdirectory.php';

// add plugin version option if it does not exists
add_option(WP_BUSINESSDIRECTORY_VERSION_KEY, WP_BUSINESSDIRECTORY_VERSION_NUM);

// Plugin activation
include 'includes/install.php';
register_activation_hook(__FILE__, array(
    'BusinessDirectoryInstall',
    'install'
));

register_uninstall_hook(__FILE__, array(
    'BusinessDirectoryInstall',
    'uninstall'
));

JBusinessUtil::loadClasses();
include 'includes/menu-items.php';

$endTime = microtime(true) - $startTime; // And this at the end of your code
			
//echo PHP_EOL . 'Intialization script took ' . round($endTime, 4) . ' seconds to run. <br/>';

/**
 * WP-BusinessDirectory admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *       
 * @return void
 */
function wpbd_fail_php_version()
{
    /* translators: %s: PHP version */
    $message = sprintf(esc_html__('WP-BusinessDirectory requires PHP version %s+, some errors may appear.', 'wp-businessdirectory'), '5.6');
    $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
    echo wp_kses_post($html_message);
}

/**
 * Wp-BusinessDirectory admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.1.0
 *       
 * @return void
 */
function wpbd_fail_wp_version()
{
    /* translators: %s: WordPress version */
    $message = sprintf(esc_html__('WP-BusinessDirectory requires WordPress version %s+. Because you are using an earlier version, some errors may appear.', 'wp-businessdirectory'), '5.0');
    $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
    echo wp_kses_post($html_message);
}

/**
 * Wp-BusinessDirectory check the current installed version.
 *
 * If a different version is detected than the previous installed version, the installation is run again.
 * The activation is not run during the update process automatically
 *
 * @since 1.1.0
 *       
 * @return void
 */
function wpbd_plugin_check_version()
{
    if (WP_BUSINESSDIRECTORY_DB_VERSION_NUM !== get_option(WP_BUSINESSDIRECTORY_DB_VERSION_KEY)) {
        BusinessDirectoryInstall::install();
    }
}

add_action('plugins_loaded', 'wpbd_plugin_check_version');