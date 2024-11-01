<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableMessages extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_messages', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function readMessage($id) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_messages SET `read`='1' WHERE id = '".$id."'" ;

		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getTotalUnreadMessages() {
		$db =JFactory::getDBO();
		$query = "select cm.*
                    from #__jbusinessdirectory_messages cm
                    where cm.read = 0 
                    group by cm.id
                     ";

		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getMessages($userId, $companyIds, $type, $notRead = false, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		if (empty($companyIds) || empty($userId)) {
			return null;
		}

		$whereType = '';
		if (!empty($type)) {
			$whereType = ' and cm.type="'.$type.'"';
		}

		$companiesFilter = "";
		if (!empty($companyIds)) {
			$companyIds = implode(",", $companyIds);
			$companiesFilter =" and (cp.id in ($companyIds) or ev.company_id in ($companyIds) or co.companyId in ($companyIds) or cp.userId = $userId or ev.user_id= $userId or co.user_id = $userId) ";
		}

		$notReadFilter = "";
		if (!empty($notRead)) {
			$notReadFilter = "and cm.read = 0";
		}

		$query = "select cm.*, cp.name as companyName, cc.contact_name as contactName, cc.contact_email as contactEmail,
                    co.subject as offerName, ev.name as eventName
					from
					#__jbusinessdirectory_messages cm 
					left join #__jbusinessdirectory_companies cp on cp.id = cm.item_id and cm.type=".MESSAGE_TYPE_BUSINESS."
					left join #__jbusinessdirectory_company_contact cc on cm.contact_id = cc.id
					left join #__jbusinessdirectory_company_offers AS co ON co.id=cm.item_id and cm.type=".MESSAGE_TYPE_OFFER."
					left join #__jbusinessdirectory_company_events AS ev ON ev.id=cm.item_id and cm.type=".MESSAGE_TYPE_EVENT."
					where 1 $notReadFilter $companiesFilter $whereType
					group by cm.id
					order by cm.date DESC";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function getTotalMessages($userId, $companyIds, $type, $notRead = false) {
		$db =JFactory::getDBO();

		if (empty($companyIds) || empty($userId)) {
			return null;
		}

		$whereType = '';
		if (!empty($type)) {
			$whereType = ' and cm.type="'.$type.'"';
		}

		$notReadFilter = "";
		if (!empty($notRead)) {
			$notReadFilter = "and cm.read = 0";
		}

		$companiesFilter = "";
		if (!empty($companyIds)) {
			$companyIds = implode(",", $companyIds);
			$companiesFilter =" and (cp.id in ($companyIds) or ev.company_id in ($companyIds) or co.companyId in ($companyIds) or cp.userId = $userId or ev.user_id= $userId or co.user_id = $userId) ";
		}

		$query = "select cm.*, cp.name as companyName, cc.contact_name as contactName, cc.contact_email as contactEmail,
                    co.subject as offerName, ev.name as eventName
					from
					#__jbusinessdirectory_messages cm 
					left join #__jbusinessdirectory_companies cp on cp.id = cm.item_id and cm.type='".MESSAGE_TYPE_BUSINESS."'
					left join #__jbusinessdirectory_company_contact cc on cm.contact_id = cc.id
					left join #__jbusinessdirectory_company_offers AS co ON co.id=cm.item_id and cm.type='".MESSAGE_TYPE_OFFER."'
					left join #__jbusinessdirectory_company_events AS ev ON ev.id=cm.item_id and cm.type='".MESSAGE_TYPE_EVENT."'
					where 1 $notReadFilter $companiesFilter $whereType
					group by cm.id
					order by cm.date DESC";

		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function changeStatus($id, $value) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_messages SET `read` = '$value' WHERE id = ".$id ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
