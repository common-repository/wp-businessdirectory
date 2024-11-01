<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS . 'components' . 'com_jbusinessdirectory' . DS . 'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'listingregistrations.php');

class JBusinessDirectoryModelManageListingRegistrations extends JBusinessDirectoryModelListingRegistrations {
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
	 * @param string $type
	 * @param string $prefix
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 *
	 * @since 5.2.0
	 */
	public function getTable($type = 'RegisteredCompany', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get campaigns filtered by the companies that belong to the current active
	 * user
	 *
	 * @param null $userId int ID of the user
	 *
	 * @return string
	 *
	 * @since 5.2.0
	 */
	public function getListQuery($userId = null) {
		$user = JBusinessUtil::getUser();
		return parent::getListQuery($user->ID);
	}

	public function getPagination() {
		// Load the content if it doesn't already exist
		$user  = JBusinessUtil::getUser();
		$table = $this->getTable();
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($table->getTotalJoinRequestsByUser($user->ID), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}
}
