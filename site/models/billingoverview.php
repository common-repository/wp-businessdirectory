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

jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');


class JBusinessDirectoryModelBillingOverview extends JModelItem {
	public function __construct() {
		$this->log = Logger::getInstance();
		parent::__construct();

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getItem($pk = NULL) {		
	}
	
	public function getOrders() {
		$user = JBusinessUtil::getUser();
		$orderTable = JTable::getInstance("Order", "JTable", array());
		$orders = $orderTable->getOrders($user->ID);

		$results = array();
		foreach($orders as $order){
			if(!isset($results[$order->business_id])){
				$obj =  new stdClass;
				$obj->business_id = $order->business_id;
				$obj->business_name = $order->business_name;
				$obj->package_name = $order->name;
				$obj->package_price = $order->price;
				$obj->package_period = $order->time_unit;
				$obj->package_period_amount = $order->time_amount;
				$obj->package_info = JBusinessUtil::getPackageDuration($order, true);
				$obj->next_payment_date = $order->end_date;
				$obj->orders = array($order);
				$results[$order->business_id] = $obj;

			}else{
				$results[$order->business_id]->orders[] = $order;
			}
		}

		return $results;
	}
	
	public function getPagination() {
		$user = JBusinessUtil::getUser();
		$orderTable = JTable::getInstance("Order", "JTable", array());
		if (empty($pagination)) {
			jimport('joomla.html.pagination');
			$pagination = new JPagination($orderTable->getTotalOrdersByUserId($user->ID), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $pagination;
	}
}
?>

