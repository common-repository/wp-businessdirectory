<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

//Access Denied
if (!defined('ABSPATH')) die('Restricted access');

//$language = new Settings\language;
//$language->_ini_contents();

/*** Include defines ***/
include WP_BUSINESSDIRECTORY_PATH.'/site/helpers/defines.php';
/*** Include plugin Autoloaders ***/
include WP_BUSINESSDIRECTORY_PATH.'/site/helpers/utils.php';
include WP_BUSINESSDIRECTORY_PATH.'/site/helpers/urltranslator.php';

//Include notices
include WP_BUSINESSDIRECTORY_PATH.'/includes/notices.php';

//Include api
include WP_BUSINESSDIRECTORY_PATH.'/includes/api.php';

//Include form router
include WP_BUSINESSDIRECTORY_PATH.'/includes/router.php';

//Include the Admin pages
include WP_BUSINESSDIRECTORY_PATH.'/includes/admin-menu.php';

//Include the Admin pages
include WP_BUSINESSDIRECTORY_PATH.'/includes/callback.php';
//Include wp-actions
include WP_BUSINESSDIRECTORY_PATH.'/includes/wp-actions.php';

//Include widgets
include WP_BUSINESSDIRECTORY_PATH."/includes/widgets.php";

//Include shortcodes
include WP_BUSINESSDIRECTORY_PATH."/includes/shortcodes.php";

//Include logs
require_once BD_HELPERS_PATH.'/logger.php';
$log = Logger::getInstance(WP_BUSINESSDIRECTORY_PATH."/logs/site-log-".date("d-m-Y").'.log',1);

if ( is_admin() ) {
    //Include update
    include WP_BUSINESSDIRECTORY_PATH."/includes/update.php";
    
    //Include admin
    include WP_BUSINESSDIRECTORY_PATH."/includes/admin.php";
    $wpbdAdmin = new WPBDAdmin();
}

