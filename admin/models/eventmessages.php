<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
/**
 * List Model.
 *
 * @package    WBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelEventMessages extends JModelList{

    /**
     * Constructor.
     *
     * @param   array  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()){
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'cm.id',
                'name', 'cm.name',
                'surname', 'cm.surname',
                'email', 'cm.email',
                'message', 'cm.message',
                'eventName', 'bc.name'
            );
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
    protected function populateState($ordering = 'bc.name', $direction = 'asc'){
        $app = JFactory::getApplication('administrator');

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

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


    /**
     * Overrides the getItems method to attach additional metrics to the list.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.6.1
     */
    public function getItems(){
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
    protected function getListQuery(){

        // Create a new query object
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select all fields from the table.
        $query->select($this->getState('list.select', 'cm.*'));
        $query->from($db->quoteName('#__jbusinessdirectory_event_messages').' AS cm');

        $query->select('bc.name as eventName');
        $query->leftJoin($db->quoteName('#__jbusinessdirectory_company_events').' AS bc ON bc.id=cm.event_id');

        // Filter by search in title.
        $search = $this->getState('filter.search');
        $typeId = $this->getState('filter.type_id');


        if (!empty($search)) {
            $val = trim($db->escape($search));
            $query->where("bc.name LIKE '%" . $val . "%' OR cm.name LIKE '%" . $val . "%' OR cm.surname LIKE '%" . $val . "%' OR cm.email LIKE '%" . $val . "%'");
        }

        $query->group('cm.id');

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'cm.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    function getSearchTypes(){
        $states = array();
        $state = new stdClass();
        $state->value = FILTER_EVENT_NAME;
        $state->text = JTEXT::_("LNG_MESSAGE_EVENT_NAME");
        $states[] = $state;
        $state = new stdClass();
        $state->value = FILTER_NAME;
        $state->text = JTEXT::_("LNG_NAME");
        $states[] = $state;
        $state = new stdClass();
        $state->value = FILTER_LAST_NAME;
        $state->text = JTEXT::_("LNG_LAST_NAME");
        $states[] = $state;
        $state = new stdClass();
        $state->value = FILTER_EMAIL;
        $state->text = JTEXT::_("LNG_EMAIL");
        $states[] = $state;

        return $states;
    }
}