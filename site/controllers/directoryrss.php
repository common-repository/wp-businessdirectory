<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerDirectoryRSS extends JControllerLegacy {
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	public function getCompaniesRss() {
		$model = $this->getModel('DirectoryRSS');
		$companies = $model->getCompaniesRss();
		exit();
	}

	public function getEventsRss() {
		$model = $this->getModel('DirectoryRSS');
		$events = $model->getEventsRss();
		exit();
	}

	public function getOffersRss() {
		$model = $this->getModel('DirectoryRSS');
		$offers = $model->getOffersRss();
		exit();
	}
}
