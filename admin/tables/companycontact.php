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

class TableCompanyContact extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_contact', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCompanyContact($companyId) {
		$key = array("companyId"=>$companyId);
		$this->load($key, true);
		$properties = $this->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');
		return $value;
	}

	public function getAllCompanyContacts($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_contact where companyId=$companyId order by contact_department";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getAllCompanyContactsDepartment($companyId) {
		$db =JFactory::getDBO();
		$query = "select DISTINCT contact_department from #__jbusinessdirectory_company_contact where companyId=$companyId order by contact_department";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function deleteCompanyContacts($companyId, $ids) {
		if (!empty($ids)) {
			$query = "delete from #__jbusinessdirectory_company_contact where companyId in ($companyId) and id not in ($ids)";
		} else {
			$query = "delete from #__jbusinessdirectory_company_contact where companyId in ($companyId)";
		}
		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	public function getMaxContactNumberOnCompany() {
		$db =JFactory::getDBO();
		$query = "select count(*) as maxContacts FROM #__jbusinessdirectory_company_contact GROUP BY companyId ORDER BY count(*) DESC Limit 1";
		$db->setQuery($query);
		$object = $db->loadObject();
		return $object->maxContacts;
	}
}
