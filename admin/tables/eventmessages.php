<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

class JTableEventMessages extends JTable {

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db){

        parent::__construct('#__jbusinessdirectory_event_messages', 'id', $db);
    }

    function setKey($k){
        $this->_tbl_key = $k;
    }

    function getUserEventMessages($eventIds, $limitstart=0, $limit=0){
        $db =JFactory::getDBO();
        
        if(empty($eventIds))
            return array();

        $eventIds = implode(",", $eventIds);

        $query = "select cm.*, cp.name as eventName
					from
					#__jbusinessdirectory_event_messages cm
					left join #__jbusinessdirectory_company_events cp on cp.id = cm.event_id
					where cm.event_id in ($eventIds)
					group by cm.id	";

        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getTotalEventMessagesByUser($eventIds, $limitstart=0, $limit=0){
        $db =JFactory::getDBO();

        if(!empty($eventIds))
            $eventIds = implode(",", $eventIds);
        else
            $eventIds = 0;

        $query = "select cm.*, cp.name as eventName
					from
					#__jbusinessdirectory_event_messages cm
					left join #__jbusinessdirectory_company_events cp on cp.id = cm.event_id
					where event_id in ($eventIds)
					group by cm.id	";

        $db->setQuery($query, $limitstart, $limit);
        $db->execute();
        return $db->getNumRows();

    }
}
