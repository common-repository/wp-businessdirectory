<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');


class JBusinessDirectoryModelUserDashboard extends JModelLegacy {
	public function __construct() {
		parent::__construct();
	}

	public function getStatistics() {
		$statistics = new stdClass();
		$statistics->serviceBookings = 0;
		$statistics->offerOrders = 0;
		$statistics->eventReservations = 0;

		$model = JModelLegacy::getInstance('ManageUserServiceReservations', 'JBusinessDirectoryModel', array('ignore_request' => true));
		if ($model) {
			$items = $model->getItems();
			$statistics->serviceBookings = count($items);
		}
		
		$model = JModelLegacy::getInstance('ManageUserOfferOrders', 'JBusinessDirectoryModel', array('ignore_request' => true));
		if ($model) {
			$items = $model->getItems();
			$statistics->offerOrders = count($items);
		}

		$model = JModelLegacy::getInstance('ManageUserEventReservations', 'JBusinessDirectoryModel', array('ignore_request' => true));
		if ($model) {
			$items = $model->getItems();
			$statistics->eventReservations = count($items);
		}

		return $statistics;
	}
}
