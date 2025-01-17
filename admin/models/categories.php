<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_categories
 *
 * @copyright  Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html;
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
/**
 * Categories Component Categories Model
 *
 * @package     JBD.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class JBusinessDirectoryModelCategories extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'type', 'a.type',
				"a.ordering",
				'published', 'a.published');
		}

		parent::__construct($config);
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
	protected function populateState($ordering = 'a.lft', $direction = 'asc') {
		$app = JFactory::getApplication();
		$context = $this->context;

		$search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
		$this->setState('filter.search', $search);

		$level = $this->getUserStateFromRequest($context . '.filter.level', 'filter_level');
		$this->setState('filter.level', $level);

		$published = $this->getUserStateFromRequest($context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryType = $app->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', CATEGORY_TYPE_BUSINESS);
		$this->setState('filter.type', $categoryType);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
		
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '') {
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.extension');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * @return  string
	 *
	 * @since   1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.published, a.icon, a.type, a.imageLocation, a.markerLocation, a.color' .
				',  a.parent_id, a.level, a.lft, a.rgt'
			)
		);
		$query->from('#__jbusinessdirectory_categories AS a');

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('a.level <= ' . (int) $level);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		} elseif ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by type
		if (empty($this->getState('filter.type'))) {
			$this->setState('filter.type', CATEGORY_TYPE_BUSINESS);
		}
		$jinput = JFactory::getApplication()->input;
		if (is_numeric($jinput->get('type'))) {
			$type = $jinput->get('type');
			$query->where('(a.type IN (0,' . (int) $type.'))');
		} elseif (is_numeric($this->getState('filter.type'))) {
			$type = $this->getState('filter.type');
			$query->where('(a.type IN (0,' . (int) $type.'))');
		} else {
			$query->where('(a.type IN (0, '.CATEGORY_TYPE_BUSINESS.'))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else {
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		$query->where('a.id >1');
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'a.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}

	public function getCategoryTypes() {
		$types = array();
		$companyType = new stdClass();
		$companyType->value = CATEGORY_TYPE_BUSINESS;
		$companyType->text = JText::_('LNG_COMPANY');
		array_push($types, $companyType);

		$offerType = new stdClass();
		$offerType->value = CATEGORY_TYPE_OFFER;
		$offerType->text = JText::_('LNG_OFFER');
		array_push($types, $offerType);

		$eventType = new stdClass();
		$eventType->value = CATEGORY_TYPE_EVENT;
		$eventType->text = JText::_('LNG_EVENT');
		array_push($types, $eventType);

		$videoType = new stdClass();
		$videoType->value = CATEGORY_TYPE_VIDEO;
		$videoType->text = JText::_('LNG_VIDEO');
		array_push($types, $videoType);

		$conferenceType = new stdClass();
		$conferenceType->value = CATEGORY_TYPE_CONFERENCE;
		$conferenceType->text = JText::_('LNG_CONFERENCE');
		array_push($types, $conferenceType);

		return $types;
	}

	public function getCategoryTypeFromURL() {
		$type = JFactory::getApplication()->input->get('type');

		if (!isset($type)) {
			return false;
		}

		$type = intval($type);

		return $type;
	}
}
