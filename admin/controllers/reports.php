<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;

class JBusinessDirectoryControllerReports extends JControllerLegacy {

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		parent::__construct();
	}

	public function generateReport() {
		parent::display();
	}

	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_REPORT_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel("Report");

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);
	
			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} elseif (!empty($model->getErrors())) {
				$implodeErrors = implode('<br />', $model->getErrors());
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_DELETED_WARNING', $implodeErrors), 'Warning');
			} else {
				$this->setMessage(JText::plural('COM_JBUSINESSDIRECTORY_N_REPORTS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=reports');
	}

	public function exportReportToCSV() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('Reports');
		$data = $model->getReportData();
		$model->exportReportToCSV($data);

		exit;
	}
}
