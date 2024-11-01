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

JTable::addIncludePath(DS . 'components' . 'com_jbusinessdirectory' . DS . 'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'paymentprocessors.php');

class JBusinessDirectoryModelManagePaymentProcessors extends JBusinessDirectoryModelPaymentProcessors {
	public function __construct() {
		parent::__construct();

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit      = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'PaymentProcessor', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getPaymentProcessors() {
		$table = $this->getTable();
		$user  = JBusinessUtil::getUser();

		$companyId = $this->getState('filter.company_id');
		$result    = $table->getUserPaymentProcessors($user->ID, JBusinessUtil::getCompaniesByUserId($user->ID,true), $this->getState('limitstart'), $this->getState('limit'), $companyId);

		return $result;
	}

	public function getPagination() {
		// Load the content if it doesn't already exist
		$table = $this->getTable();
		if (empty($this->_pagination)) {
			$user   = JBusinessUtil::getUser();
			$companyId = $this->getState('filter.company_id');
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($table->getTotalUserPaymentProcessors($user->ID, JBusinessUtil::getCompaniesByUserId($user->ID,true)), $this->getState('limitstart'), $this->getState('limit'), $companyId);
		}
		return $this->_pagination;
	}

	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);

		// Get a group row instance.
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
}
