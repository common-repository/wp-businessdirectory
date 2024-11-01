<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableBlockedUsers extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_blocked_users', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function blockUser($user_id, $blocked_id) 
    {
        $db = JFactory::getDbo();
        
        // Check if the user is already blocked
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__jbusinessdirectory_blocked_users'))
            ->where($db->quoteName('user_id') . ' = ' . $db->quote($user_id))
            ->where($db->quoteName('blocked_id') . ' = ' . $db->quote($blocked_id));
        
        $db->setQuery($query);
        $existingRecordId = $db->loadResult();
        
        if ($existingRecordId) {
            return true;
        }
        
        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__jbusinessdirectory_blocked_users'))
            ->columns($db->quoteName(array('user_id', 'blocked_id')))
            ->values($db->quote($user_id) . ', ' . $db->quote($blocked_id));
        
        $db->setQuery($query);
        return $db->execute();
    }


    public function unblockUser($user_id, $blocked_id) 
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $conditions = array(
          $db->quoteName('user_id') . ' = ' . $db->quote($user_id),
          $db->quoteName('blocked_id') . ' = ' . $db->quote($blocked_id)
        );
        
        $query
          ->delete($db->quoteName('#__jbusinessdirectory_blocked_users'))
          ->where($conditions);
        
        $db->setQuery($query);
        return $db->execute();
    }
}
