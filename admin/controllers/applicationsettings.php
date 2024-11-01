<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class JBusinessDirectoryControllerApplicationSettings extends JControllerForm {
	public function display($cachable = false, $urlparams = false) {
	}

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		parent::__construct();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$jinput = JFactory::getApplication()->input;
		$model  = $this->getModel('applicationsettings');
		$data   = $jinput->post->getArray();

		$config                = JBusinessUtil::getSiteConfig();
		$data['sendmail_from'] = $config->mailfrom;
		$data['sendmail_name'] = $config->fromname;
		
		$data['terms_conditions']         = isset($_REQUEST['terms_conditions'])?$_REQUEST['terms_conditions']:"";
		$data['reviews_terms_conditions'] = isset($_REQUEST['reviews_terms_conditions'])?$_REQUEST['reviews_terms_conditions']:"";
		$data['contact_terms_conditions'] = isset($_REQUEST['contact_terms_conditions'])?$_REQUEST['contact_terms_conditions']:"";
		$data['content_responsible'] = 		isset($_REQUEST['content_responsible'])?$_REQUEST['content_responsible']:"";
		$data['privacy_policy']           = isset($_REQUEST['privacy_policy'])?$_REQUEST['privacy_policy']:"";
		$data['custom_address']           = isset($_REQUEST['custom_address'])?$_REQUEST['custom_address']:"";
		
		$languages = JBusinessUtil::getLanguages();
		$appSettings = JBusinessUtil::getApplicationSettings();
		if ($appSettings->enable_multilingual) {
			foreach ($languages as $lng) {
				$data['terms_conditions_'. $lng]         = isset($_REQUEST['terms_conditions_'. ($lng)])?$_REQUEST['terms_conditions_'. ($lng)]:"";
				$data['reviews_terms_conditions_'. ($lng)] = isset($_REQUEST['reviews_terms_conditions_'. ($lng)])?$_REQUEST['reviews_terms_conditions_'. ($lng)]:"";
				$data['contact_terms_conditions_'. ($lng)] = isset($_REQUEST['contact_terms_conditions_'. ($lng)])?$_REQUEST['contact_terms_conditions_'. ($lng)]:"";
				$data['content_responsible_'. ($lng)] 	 = isset($_REQUEST['content_responsible_'. ($lng)])?$_REQUEST['content_responsible_'. ($lng)]:"";
				$data['privacy_policy_'. ($lng)]           = isset($_REQUEST['privacy_policy_'. ($lng)])?$_REQUEST['privacy_policy_'. ($lng)]:"";
				$data['custom_address_'. ($lng)]           = isset($_REQUEST['custom_address_'. ($lng)])?$_REQUEST['custom_address_'. ($lng)]:"";
				$data['privacy_policy_article_id_'. ($lng)]             = isset($_REQUEST['privacy_policy_article_id_'. ($lng)])?$_REQUEST['privacy_policy_article_id_'. ($lng)]:"";
				$data['terms_conditions_article_id_'. ($lng)]           = isset($_REQUEST['terms_conditions_article_id_'. ($lng)])?$_REQUEST['terms_conditions_article_id_'. ($lng)]:"";
				$data['reviews_terms_conditions_article_id_'. ($lng)]   = isset($_REQUEST['reviews_terms_conditions_article_id_'. ($lng)])?$_REQUEST['reviews_terms_conditions_article_id_'. ($lng)]:"";
				$data['contact_terms_conditions_article_id_'. ($lng)]   = isset($_REQUEST['contact_terms_conditions_article_id_'. ($lng)])?$_REQUEST['contact_terms_conditions_article_id_'. ($lng)]:"";
			}
		}

		if (isset($data['linkedin']) && strlen($data['linkedin']) > 1) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['linkedin'])) {
				$data['linkedin'] = "http://" . $data['linkedin'];
			}
		}
		if (isset($data['facebook']) && strlen($data['facebook']) > 1) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['facebook'])) {
				$data['facebook'] = "http://" . $data['facebook'];
			}
		}
		if (isset($data['twitter']) && strlen($data['twitter']) > 1) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['twitter'])) {
				$data['twitter'] = "http://" . $data['twitter'];
			}
		}
		if (isset($data['youtube']) && strlen($data['youtube']) > 1) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['youtube'])) {
				$data['youtube'] = "http://" . $data['youtube'];
			}
		}
		
		if (!empty($data['redirect_contact_url'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['redirect_contact_url'])) {
				$data['redirect_contact_url'] = "http://" . $data['redirect_contact_url'];
			}
		}

		if (!empty($data['search_redirect_url'])) {
			if (!preg_match("~^(?:f|ht)tps?://~i", $data['search_redirect_url'])) {
				$data['search_redirect_url'] = "http://" . $data['search_redirect_url'];
			}
		}
		
		if ($model->store($data)) {
			$msg = JText::_('LNG_SETTINGS_APPLICATION_SAVED');
		} else {
			$msg = JText::_('LNG_ERROR_SAVING_SETTINGS_APPLICATION');
		}

		$task = $this->getTask();

		switch ($task) {
			case 'apply':
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item, false), $msg);
				break;
			default:
				// Check the table in so it can be edited.... we are done with it anyway
				$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory'), $msg);
				break;
		}
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel($key = null) {
		$msg = JText::_('LNG_OPERATION_CANCELLED');
		$this->setRedirect('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory', $msg);
	}

	/**
	 * Send an email to all customers that have not activated the subscriptions
	 * boolean result as JSON.
	 *
	 * @since 5.3.0
	 */
	public function sendPayamentEmailNotificationsAjax() {
		$model = $this->getModel('applicationsettings');
		$result = $model->sendPayamentEmailNotificationsAjax();

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function sendTestEmail(){

		$result = EmailService::sendTestEmail();

		if ($result == true){
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_TEST_EMAIL_SENT'));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_TEST_EMAIL_NOT_SENT'), 'error');
		}

		$link = 'index.php?option=com_jbusinessdirectory&view=applicationsettings';
		$this->setRedirect($link);
	}

	
	/**
	 * Clears the local cache of map tile images for the staticmaplite library of OpenStreetMaps. Sends
	 * boolean result as JSON.
	 *
	 * @since 5.1.5
	 */
	public function clearOSMCacheAjax() {
		$model = $this->getModel('applicationsettings');
		$result = $model->clearOSMCache();
		
		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function clearDemoDataAjax() {
		$model = $this->getModel('applicationsettings');
		$result = $model->clearDemoData();

		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
	
	
	/**
	 * Searches the application settings with the given keyword and returns the results as
	 * JSON.
	 *
	 * @since 5.2.0
	 */
	public function searchSettingAjax() {
		$keyword = JFactory::getApplication()->input->get("keyword", '', 'RAW');
		$model = $this->getModel('applicationsettings');
		
		$result = $model->searchSetting($keyword);
		
		// Send as JSON
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}
