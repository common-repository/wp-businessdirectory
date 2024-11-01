<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

/**
 * Company Types List Model.
 *
 * @package     JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelCompanyTypes extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'ct.id',
				'title', 'ct.name',
				'ordering', 'ct.ordering'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = "ct.id", $direction = "desc") {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
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

	public function getCompanyViews() {
		$items = array();
		$items[1] = 'LNG_STYLE_1';
		$items[2] = 'LNG_STYLE_2';
		$items[3] = 'LNG_STYLE_3';
		$items[4] = 'LNG_STYLE_4';
		$items[5] = 'LNG_STYLE_5';
		$items[6] = 'LNG_STYLE_6';
		$items[7] = 'LNG_STYLE_7';
		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'ct.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_company_types') . ' AS ct');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("ct.name LIKE '%" . trim($db->escape($search)) . "%'");
		}

		$query->group('ct.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'ct.ordering');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		// Add the list ordering clause.
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param    int        The ID of the primary key to move.
	 * @param    integer    Increment, usually +1 or -1
	 *
	 * @return    boolean    False on failure or error, true otherwise.
	 */
	public function reorder($pk, $direction = 0) {
		// Sanitize the id and adjustment.
		$pk   = (!empty($pk)) ? $pk : (int) $this->getState('companytype.id');
		// Get an instance of the record's table.
		$table = JTable::getInstance('CompanyType');

		// Load the row.
		if (!$table->load($pk)) {
			$this->setError($table->getError());
			return false;
		}

		// Access checks.
		$allow = true; //$user->authorise('core.edit.state', 'com_users');

		if (!$allow) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			return false;
		}

		// Move the row.
		// TODO: Where clause to restrict category.
		$table->move($pk);

		return true;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param    array    An array of primary key ids.
	 * @param    int        +/-1
	 *
	 * @return bool
	 */
	public function saveorder($pks, $order) {
		// Initialise variables.
		$table      = JTable::getInstance('CompanyType');
		$conditions = array();

		if (empty($pks)) {
			return JFactory::getApplication()->enqueueMessage(JText::_('COM_USERS_ERROR_LEVELS_NOLEVELS_SELECTED'), 'warning');
		}

		// update ordering values
		foreach ($pks as $i => $pk) {
			$table->load((int) $pk);

			// Access checks.
			$allow = true;//$user->authorise('core.edit.state', 'com_users');

			if (!$allow) {
				// Prune items that you can't change.
				unset($pks[$i]);
				JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'warning');
			} elseif ($table->ordering != $order[$i]) {
				$table->ordering = $order[$i];
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond) {
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		return true;
	}
}
