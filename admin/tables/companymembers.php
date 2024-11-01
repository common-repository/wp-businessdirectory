<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableCompanyMembers extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db){

		parent::__construct('#__jbusinessdirectory_company_members', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getCompanyMembers($companyId){
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_members where company_id=$companyId order by id";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function deleteCompanyMembers($companyId, $ids){
		$query = "delete from #__jbusinessdirectory_company_members where company_id in ($companyId)";
		$this->_db->setQuery($query);
		try {
			$this->_db->execute();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
}
