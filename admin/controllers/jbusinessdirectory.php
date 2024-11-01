<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerJBusinessDirectory extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	public function newCompaniesAjax()
	{
		$model  = $this->getModel('JBusinessDirectory');
		$result = $model->getNewCompanies();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function newOffersAjax()
	{
		$model  = $this->getModel('JBusinessDirectory');
		$result = $model->getNewOffers();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function newEventsAjax()
	{
		$model  = $this->getModel('JBusinessDirectory');
		$result = $model->getNewEvents();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function incomeAjax()
	{
		$model  = $this->getModel('JBusinessDirectory');
		$result = $model->getNewIncome();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function getLatestServerNewsAjax()
	{
		$model = $this->getModel('JBusinessDirectory');
		$model->getLatestServerNews();
		exit;
	}

	public function installApp() {

		$model = $this->getModel();
		$result = $model->bulkInstallApps();

		if (ob_get_length() > 0 ) {
			ob_end_clean();
		}

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=jbusinessdirectory&res='.$result);
	}

	public function archiveStatisticsAjax(){
		$model = $this->getModel('Statistics', 'JBusinessDirectoryModel');
		$model->getArchiveStatistics();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode(true);
		exit;
	}

	public function archiveSearchLogsAjax(){
		$model = $this->getModel('SearchLogs', 'JBusinessDirectoryModel');
		$model->getArchiveSearchLogs();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode(true);
		exit;
	}
}
