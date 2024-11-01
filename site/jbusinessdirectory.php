<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.'/helpers/defines.php';
require_once BD_HELPERS_PATH.'/utils.php';
require_once BD_HELPERS_PATH.'/category_lib.php';
require_once BD_HELPERS_PATH.'/helper.php';
require_once BD_HELPERS_PATH.'/translations.php';
require_once BD_HELPERS_PATH.'/attachments.php';
require_once BD_HELPERS_PATH.'/tabs.php';
require_once BD_HELPERS_PATH.'/logger.php';

JBusinessUtil::includeCSSLibraries();
$appSettings = JBusinessUtil::getApplicationSettings();

JText::script('LNG_SELECT_OPTION');

JHtml::_('jquery.framework', true, true);
define('J_JQUERY_LOADED', 1);

JBusinessUtil::loadBaseScripts();
//JBusinessUtil::loadMapScripts();

JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');

if (!defined('BD_COMPONENT_IMAGE_PATH')) {
	define("BD_COMPONENT_IMAGE_PATH", BD_ASSETS_FOLDER_PATH."images/");
}

JBusinessUtil::setMenuItemId();
JBusinessUtil::loadClasses();
JBusinessUtil::loadSiteLanguage();
JBusinessUtil::applyRobotsMeta();

$jsSettings = JBusinessUtil::addJSSettings();
$jsSettings->isProfile = 1;

if (!defined('JBD_UTILS_LOADED')) {
	$document  =JFactory::getDocument();
	$document->addScriptDeclaration('
		window.addEventListener("load",function() {
	        jbdUtils.setProperties(' . json_encode($jsSettings) . ');
			jbdUtils.renderRadioButtons();
		});
	');
	define('JBD_UTILS_LOADED', 1);
}

if ($appSettings->enable_map_gdpr && $appSettings->map_type == MAP_TYPE_GOOGLE) {
	if (!isset($_COOKIE['jbd_map_gdpr'])) {
		$_COOKIE['jbd_map_gdpr'] = false;
	}

	$document = JFactory::getDocument();
	$gpdr_val = $_COOKIE["jbd_map_gdpr"] ? "true" : "false";
	$document->addScriptDeclaration('
		if (typeof jbd_map_gdpr === "undefined") {
			var jbd_map_gdpr = '.$gpdr_val.';		
		}
	');
}

JBusinessUtil::sanitizeRequest();

$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log', 1);

// Execute the task.
$input = new Input();
$controller	= JControllerLegacy::getInstance('JBusinessDirectory');
$controller->execute($input->get("task"));
$controller->redirect();

//the below function is used to declare global variables in javascript
//to do so we need to assign the function to wp_head
add_action ( 'wp_head', 'header_js_variables' );
function header_js_variables(){ 
       $scripts = JBusinessUtil::generateScriptDeclarations();
       echo $scripts;
}
