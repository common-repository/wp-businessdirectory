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
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
class JBusinessDirectoryModelBookmarks extends JModelList {
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
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage
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
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$type = BOOKMARK_TYPE_BUSINESS;

		$jinput = JFactory::getApplication()->input;

		if (is_numeric($jinput->get('type'))) {
			$type = $jinput->get('type');
		} elseif (is_numeric($this->getState('filter.type_id'))) {
			$type = $this->getState('filter.type_id');
		}
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'ct.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_bookmarks').' AS ct');

		if ($type == BOOKMARK_TYPE_BUSINESS) {
			// Join over the companies
			$query->select('bc.name as name');
			$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies') . ' AS bc ON bc.id=ct.item_id');
		}

		if ($type == BOOKMARK_TYPE_OFFER) {
			// Join over the offer
			$query->select('o.subject as name');
			$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_offers') . ' AS o ON o.id=ct.item_id');
		}

		// Join over the users
		$query->select('us.display_name as userName');
		$query->join('LEFT', $db->quoteName('#__users').' AS us ON us.id=ct.user_id');

		// Filter by type
		$query->where('(ct.item_type IN (0,' . (int) $type.'))');
		
		$query->group('ct.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'ct.id')).' '.$db->escape($this->getState('list.direction', 'ASC')));

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
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication('administrator');

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		$typeId = $app->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
		$this->setState('filter.type_id', $typeId);

		// List state information.
		parent::populateState('ct.id', 'desc');
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param	int		The ID of the primary key to move.
	 * @param	integer	Increment, usually +1 or -1
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function reorder($pk, $direction = 0) {
		// Sanitize the id and adjustment.
		$pk	= (!empty($pk)) ? $pk : (int) $this->getState('bookmark.id');
		// Get an instance of the record's table.
		$table = JTable::getInstance('Bookmark');
	
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
	 * @param	array	An array of primary key ids.
	 * @param	int		+/-1
	 */
	public function saveorder($pks, $order) {
		// Initialise variables.
		$table		= JTable::getInstance('Bookmark');
		$conditions	= array();
	
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

	/**
	 * Retrieves the bookmark types
	 *
	 * @return array
	 *
	 * @since 4.9.0
	 */
	public function getTypes() {
		$types = array();
		$companyType = new stdClass();
		$companyType->value = BOOKMARK_TYPE_BUSINESS;
		$companyType->text = JText::_('LNG_COMPANY');
		array_push($types, $companyType);

		$offerType = new stdClass();
		$offerType->value = BOOKMARK_TYPE_OFFER;
		$offerType->text = JText::_('LNG_OFFER');
		array_push($types, $offerType);

		return $types;
	}
}
