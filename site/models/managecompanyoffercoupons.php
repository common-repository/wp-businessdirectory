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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'offercoupons.php');
require_once(JPATH_COMPONENT.DS.'libraries'.DS.'phpqrcode'.DS.'qrlib.php');
require_once(JPATH_COMPONENT.DS.'libraries'.DS.'tfpdf'.DS.'tfpdf.php');

class JBusinessDirectoryModelManageCompanyOfferCoupons extends JBusinessDirectoryModelOfferCoupons {
	public function __construct() {
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'OfferCoupons', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	* Method that retrieves offer coupons that belong to the user
	*
	* @return object with data
	*/
	public function getOfferCoupons() {
		// Load the data
		$user = JBusinessUtil::getUser();
		$offercouponsTable = $this->getTable("OfferCoupon");
		if (empty($this->_data)) {
			$this->_data = $offercouponsTable->getCouponsByUserId($user->ID, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	public function getTotal() {
		$user = JBusinessUtil::getUser();
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$offersTable = $this->getTable("OfferCoupon");
			$this->_total = $offersTable->getTotalUserOfferCoupons($user->ID);
		}
		return $this->_total;
	}
}
