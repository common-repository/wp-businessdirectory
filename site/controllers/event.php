<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerEvent extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Gets all the data about the tickets that are being booked
	 * On success, it redirects to the event guest details page for the user to input further details for the booking.
	 */
	public function reserveTickets() {
		
		$result = EventBookingService::reserveTickets();
		$eventId = JFactory::getApplication()->input->get('eventId');
		
		if ($result) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&task=eventguestdetails.checkBillingDetails'.'&'.JSession::getFormToken().'=1', false));
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=event&eventId='.$eventId, false));
		}
	}

	public function checkListingPackageAjax() {
		$companyId = JFactory::getApplication()->input->get('companyId');

		$model = $this->getModel('Event');
		$result = $model->getPackage($companyId);
		$appSettings = JBusinessUtil::getApplicationSettings();

		if ((isset($result->features) && in_array(EVENT_RECURRING, $result->features)) || !$appSettings->enable_packages) {
			$result = true;
		} else {
			$result = false;
		}

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function checkEventsAboutToExpire() {
		$model = $this->getModel('event');
		$model->checkEventsAboutToExpire();
	}

	public function deleteExpiredEvents() {
		$model = $this->getModel('event');
		$model->deleteExpiredEvents();
	}

	public function contactCompany() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$appSettings = JBusinessUtil::getApplicationSettings();
		$eventmodel = $this->getModel('event');
		$jinput = JFactory::getApplication()->input;
		$data = $jinput->post->getArray();
		$eventId = $jinput->getInt('event_id');
		$contactId = $jinput->getInt('contactId');

		$event = $eventmodel->getPlainEvent($eventId);

		if ($appSettings->captcha) {
			$captchaAnswer = !empty($data['recaptcha_response_field'])?$data['recaptcha_response_field']:$data['g-recaptcha-response'];
			try {
				$namespace="jbusinessdirectory.contact";
				$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
				if (!$captcha->checkAnswer($captchaAnswer)) {
					$error = $captcha->getError();
					$this->setMessage("Captcha error!", 'warning');
					$this->setRedirect(JBusinessUtil::getEventLink($eventId, $event->alias));
					return;
				}
			} catch (Exception $e) {
				$error = $captcha->getError();
				$this->setMessage("Captcha error!", 'warning');
				$this->setRedirect(JBusinessUtil::getEventLink($eventId, $event->alias));
				return;
			}
		}

		$data['description'] = $jinput->get("description", '', 'RAW');
		$data['contact_id'] = $contactId;
		$data['event_name'] = $event->name;
		$data['event_id'] = $eventId;
		$data['event_alias'] = $event->alias;


		$result = $eventmodel->contactEventCompany($data);

		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_EVENT_CONTACTED'));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESS_DIRECTORY_COMPANY_NOT_CONTACTED'));
		}

		$eventmodel->saveEventMessages($data);
		if (!empty($appSettings->redirect_contact_url)) {
			$this->setRedirect(htmlspecialchars($appSettings->redirect_contact_url, ENT_QUOTES));
		} else {
			$this->setRedirect(JBusinessUtil::getEventLink($eventId, $event->alias));
		}
	}

	public function associateCompaniesAjax() {
		$user = JBusinessUtil::getUser();
		
		$jinput = JFactory::getApplication()->input;
		$companyIds = $jinput->getString('companyIds');
		$eventId = $jinput->getInt('eventId');

		$model = $this->getModel('Event');
		$result = $model->associateCompaniesAjax($eventId, $companyIds);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function exportEventsDetailsICS() {
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('event');
		$model->exportEventsDetailsICS();
		exit;
	}
}
