<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerUserOptions extends JControllerLegacy {
	public function __construct() {
		parent::__construct();
	}

	public function newCompaniesAjax() {
		$model = $this->getModel('UserOptions');
		$result = $model->getNewCompanies();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function newOffersAjax() {
		$model = $this->getModel('UserOptions');
		$result = $model->getNewOffers();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function newEventsAjax() {
		$model = $this->getModel('UserOptions');
		$result = $model->getNewEvents();

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}
