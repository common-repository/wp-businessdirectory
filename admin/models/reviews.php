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
class JBusinessDirectoryModelReviews extends JModelList {
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
				'id', 'cr.id',
				'name', 'cr.name',
				'subject', 'cr.subject',
				'rating', 'cr.rating',
				'display_name', 'u.display_name',
				'likeCount', 'cr.likeCount',
				'dislikeCount', 'cr.dislikeCount',
				'companyName', 'bc.name',
				'creationDate', 'cr.creationDate',
				'state', 'cr.state',
				'approved', 'cr.approved',
				'offerName','off.subject'
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
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'cr.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_company_reviews').' AS cr');
		
		// Join over the company types
		$query->select('bc.name as companyName, bc.id as company_id');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies').' AS bc ON bc.id=cr.itemId');

		// Join over the offer
		$query->select('off.subject as offerName, off.id as offer_id, off.alias as offer_alias');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_offers').' AS off ON off.id=cr.itemId');

		// Join over the company types
		$query->select('u.display_name');
		$query->join('LEFT', $db->quoteName('#__users').' AS u ON u.id=cr.userId');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("cr.name LIKE '%" . trim($db->escape($search)) . "%' or 
							cr.subject LIKE '%" . trim($db->escape($search)) . "%'");
		}
		
		$stateId = $this->getState('filter.state_id');
		if (is_numeric($stateId)) {
			$query->where('cr.state ='.(int) $stateId);
		}
		
		$listingId = $this->getState('filter.listing_id');
		if (is_numeric($listingId)) {
			$query->where('cr.itemId ='.(int) $listingId);
		}

		$statusId = $this->getState('filter.status_id');
		if (is_numeric($statusId)) {
			$query->where('cr.approved ='.(int) $statusId);
		}

		// Filter by type
		$input = JFactory::getApplication()->input;
		if (is_numeric($input->get('type'))) {
			$type = $input->get('type');
			$query->where('(cr.review_type IN (0,' . (int) $type.'))');
		} elseif (is_numeric($this->getState('filter.type_id'))) {
			$type = $this->getState('filter.type_id');
			$query->where('(cr.review_type IN (0,' . (int) $type.'))');
		} else {
			$query->where('(cr.review_type IN (0, '.REVIEW_TYPE_BUSINESS.'))');
		}

		$query->group('cr.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'cr.id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

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
	protected function populateState($ordering = "cr.id", $direction = "desc") {
		$app = JFactory::getApplication('administrator');

		$listingId = $this->getUserStateFromRequest($this->context.'.filter.listing_id', 'listing_id');
		$this->setState('filter.listing_id', $listingId);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$stateId = $app->getUserStateFromRequest($this->context.'.filter.state_id', 'filter_state_id');
		$this->setState('filter.state_id', $stateId);

		$statusId = $app->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
		$this->setState('filter.status_id', $statusId);

		$typeId = $app->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
		$this->setState('filter.type_id', $typeId);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	
	public function getStates() {
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}

	public function getStatuses() {
		$statuses = array();
		$status = new stdClass();
		$status->value = REVIEW_STATUS_CREATED;
		$status->text = JTEXT::_("LNG_NEEDS_CREATION_APPROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = REVIEW_STATUS_DISAPPROVED;
		$status->text = JTEXT::_("LNG_DISAPPROVED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = REVIEW_STATUS_APPROVED;
		$status->text = JTEXT::_("LNG_APPROVED");
		$statuses[] = $status;

		return $statuses;
	}

	public function getReviewTypes() {
		$types = array();
		$companyType = new stdClass();
		$companyType->value = REVIEW_TYPE_BUSINESS;
		$companyType->text = JText::_('LNG_COMPANY');
		array_push($types, $companyType);

		$offerType = new stdClass();
		$offerType->value = REVIEW_TYPE_OFFER;
		$offerType->text = JText::_('LNG_OFFER');
		array_push($types, $offerType);

		return $types;
	}
}
