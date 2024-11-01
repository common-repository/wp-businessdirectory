<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'eventmessages.php');

class JBusinessDirectoryModelManageEventMessages extends JBusinessDirectoryModelEventMessages{

    function __construct()
    {
        parent::__construct();

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JFactory::getApplication()->input->get('limitstart', 0);

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
    public function getTable($type = 'EventMessages', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     *
     * @return object with data
     */
    function getEventMessages()
    {
        // Load the data
        $eventMessagesTable = $this->getTable("EventMessages");
        if (empty( $this->_data ))
        {
            $this->_data = $eventMessagesTable->getUserEventMessages($this->getEventsByUserId(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    function getEventsByUserId(){
        $user = JBusinessUtil::getUser();
        $companiesTable = $this->getTable("Event");
        $events =  $companiesTable->getUserEvents($user->ID,array());
        $result = array();
        foreach($events as $event){
            $result[] = $event->id;
        }
        return $result;
    }

    function getPagination()
    {
        // Load the content if it doesn't already exist
        $offerMessagesTable = $this->getTable("EventMessages");
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($offerMessagesTable->getTotalEventMessagesByUser($this->getEventsByUserId()),$this->getState('limitstart'), $this->getState('limit'));
        }
        return $this->_pagination;
    }
}