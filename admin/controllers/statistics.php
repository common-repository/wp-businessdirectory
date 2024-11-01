<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerStatistics extends JControllerLegacy {
	public function __construct() {
		parent::__construct();
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory');
	}

	public function getStatistics() {
		$model  = $this->getModel('Statistics');
		$result = $model->getStatistics();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function getItemByStringAjax() {
		$objectType = JFactory::getApplication()->input->get('objectType', STATISTIC_ITEM_BUSINESS);
		$str        = JFactory::getApplication()->input->getString('term', null);
		$isProfile  = JFactory::getApplication()->input->getInt('isProfile', false);

		$userId = null;
		if ($isProfile) {
			$user = JBusinessUtil::getUser();
			$userId = $user->ID;
		}

		header("Content-Type: application/json", true);
		switch ($objectType) {
			case STATISTIC_ITEM_OFFER:
				echo json_encode(JBusinessUtil::getOffersByString($str, $userId));
				break;
			case STATISTIC_ITEM_EVENT:
				echo json_encode(JBusinessUtil::getEventsByString($str, $userId));
				break;
			case STATISTIC_ITEM_SESSION_LOCATION:
				echo json_encode(JBusinessUtil::getSessionLocationByString($str, $userId));
				break;
	
			default:
				echo json_encode(JBusinessUtil::getCompaniesByString($str, $userId));
				break;
		}
		exit;
	}

	public function showExportCsv() {
		JFactory::getApplication()->input->set("layout", "export");
		parent::display();
	}

	public function exportStatisticsCSV() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('statistics');
		$model->exportStatisticsCSV();
		exit;
	}

	public function deleteByDate() {
		JSession::checkToken() or jexit(JText::_('J+INVALID_TOKEN'));
		$model = $this->getModel('statistics');

		if($model->deleteByDate()) {
			$this->setMessage(JText::_('LNG_STATISTICS_DELETED'));
		} else {
			$this->setMessage(JText::_('LNG_STATISTICS_DELETED_ERROR'), 'error');

		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=statistics');
	}
}
