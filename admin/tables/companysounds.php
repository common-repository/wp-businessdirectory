<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanySounds extends JTable {
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_sounds', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	
	public function deleteAllForCompany($companyId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_company_sounds where company_id=".$companyId;
		$db->setQuery($sql);
		return $db->execute();
	}
	
	public function getCompanySounds($companyId) {
		$db =JFactory::getDBO();
		$sql = "select * from #__jbusinessdirectory_company_sounds where company_id= $companyId order by id asc";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
}
