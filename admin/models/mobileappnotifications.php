<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

class JBusinessDirectoryModelMobileAppNotifications extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'MobileAppNotifications', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	

	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery($userId = null) {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);


		// Select all fields from the table.
		$query->select($this->getState('list.select', 'n.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_mobile_app_notifications') . ' AS n');

		// Add the where clause to exclude certain notification types.
		$excludedTypes = [
			NOTIFICATION_TYPE_NEW_BOOKMARK_ITEM,
			NOTIFICATION_TYPE_MESSAGE,
			NOTIFICATION_TYPE_BOOKMARK,
			NOTIFICATION_TYPE_REVIEW
		];

		$whereClause = '';
		foreach ($excludedTypes as $type) {
			$whereClause .= "n.type <> " . $db->quote($type) . " AND ";
		}

		$whereClause = rtrim($whereClause, " AND ");

		if (!empty($whereClause)) {
			$query->where($whereClause);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'n.id');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'n.id', $direction = 'desc') {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$statusId = $app->getUserStateFromRequest($this->context.'.filter.id', 'filter_id');
		$this->setState('filter.id', $statusId);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}


	public function save($data) {

		$temp['id'] = 0;
		$temp['title'] = $data['notification_title'];
		$temp['body']  = $data['notification_body'];
		$temp['type']  = $data['notification_type'];
		$temp['nr_contacts']  = $data['nr_contacts'];
		$temp['itemId']  = $data['itemId'];
		$temp['itemType']  = $data['itemType'];

		$table = $this->getTable();	

		// Bind the data.
		if (!$table->bind($temp)) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			dump($table->getError());
			return false;
		}

		return true;
	}

}
