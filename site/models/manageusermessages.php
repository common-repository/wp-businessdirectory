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

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'messages.php');

class JBusinessDirectoryModelManageUserMessages extends JBusinessDirectoryModelMessages {
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
	public function getTable($type = 'Messages', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItems() {
		// Load the list items.
		$result = parent::getItems();
		return $result;
	}

	protected function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JBusinessUtil::getUser();

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'cm.*, cp.name as companyName, 
        cc.contact_name as contactName, cc.contact_email as contactEmail, co.subject as offerName, ev.name as eventName'));
		$query->from($db->quoteName('#__jbusinessdirectory_messages').' AS cm');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies')." AS cp on cp.id = cm.item_id and cm.type='".MESSAGE_TYPE_BUSINESS."'");
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_contact').' AS cc on cm.contact_id = cc.id');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_offers')." AS co ON co.id=cm.item_id and cm.type='".MESSAGE_TYPE_OFFER."'");
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_events')." AS ev ON ev.id=cm.item_id and cm.type='".MESSAGE_TYPE_EVENT."'");
		$query->where("(cm.user_id = '$user->ID')");

		$typeId = $this->getState('filter.type');
		if (!empty($typeId)) {
			$query->where('cm.type='. $typeId);
		}

		$query->group("cm.id");

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'cm.date');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	public function getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$db = $this->getDbo();
			$query = $this->getListQuery();
			$db->setQuery($query);
			$result = $db->loadObjectList();
			require_once(BD_HELPERS_PATH.'/dirpagination.php');
			$this->_pagination = new JBusinessDirectoryPagination(count($result), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}

	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
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

	public function getMessageTypes() {
		$types       = array();

		$type        = new stdClass();
		$type->id    = MESSAGE_TYPE_BUSINESS;
		$type->name  = JTEXT::_("LNG_COMPANY_MESSAGES");
		$types[]     = $type;

		$type        = new stdClass();
		$type->id    = MESSAGE_TYPE_OFFER;
		$type->name  = JTEXT::_("LNG_OFFER_MESSAGES");
		$types[]     = $type;

		$type        = new stdClass();
		$type->id    = MESSAGE_TYPE_EVENT;
		$type->name  = JTEXT::_("LNG_EVENT_MESSAGES");
		$types[]     = $type;

		return $types;
	}
}
