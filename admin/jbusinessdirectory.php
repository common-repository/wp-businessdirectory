<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.'/helpers/defines.php';
require_once BD_HELPERS_PATH.'/utils.php';
require_once BD_HELPERS_PATH.'/translations.php';
require_once BD_HELPERS_PATH.'/attachments.php';
require_once BD_HELPERS_PATH.'/tabs.php';
require_once BD_HELPERS_PATH.'/logger.php';

require_once JPATH_COMPONENT_ADMINISTRATOR . '/views/jbdview.php';

JHtml::_('jquery.framework', true, true);

JText::script('LNG_SELECT_OPTION');

JBusinessUtil::includeCSSLibraries();

JBusinessUtil::enqueueStyle('css/jbd-admin.css');

JBusinessUtil::loadJQueryChosen();

JBusinessUtil::enqueueStyle('libraries/modal/jquery.modal.css');
JBusinessUtil::enqueueScript('libraries/modal/jquery.modal.js');

JBusinessUtil::loadBaseScripts();
JBusinessUtil::loadMapScripts();

JBusinessUtil::enqueueScript('libraries/metis-menu/metisMenu.js');

JBusinessUtil::enqueueStyle('css/jbd-template.css');

if (!defined('BD_COMPONENT_IMAGE_PATH')) {
	define("BD_COMPONENT_IMAGE_PATH", BD_ASSETS_FOLDER_PATH."images/");
}

$log = Logger::getInstance(JPATH_COMPONENT . "/logs/admin-log-" . date("d-m-Y"), 1);

JBusinessUtil::loadClasses();
JBusinessUtil::loadSiteLanguage();
$appSettings = JBusinessUtil::getApplicationSettings();

$jsSettings                     = JBusinessUtil::addJSSettings();
$jsSettings->isProfile          = 0;

JBusinessUtil::sanitizeRequest();

if (!defined('JBD_UTILS_LOADED')) {
	$document = JFactory::getDocument();
	$document->addScriptDeclaration('
		window.addEventListener("load",function() {
	        jbdUtils.setProperties(' . json_encode($jsSettings) . ');
			jbdUtils.renderRadioButtons();
		});
	');
	define('JBD_UTILS_LOADED', 1);
}

$view = JFactory::getApplication()->input->get('view', null);

// Execute the task.
$controller = JControllerLegacy::getInstance('JBusinessDirectory');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
