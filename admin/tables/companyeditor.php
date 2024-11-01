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

class TableCompanyEditor extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_editors', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function deleteCompanyEditors($companyId) {
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_editors where company_id = '$companyId'";
		$db->setQuery($query);
		$db->execute();
		return true;
	}

	public function getCompanyEditors($companyId) {
		$db =JFactory::getDBO();
		$query = "select ce.editor_id as value, concat(us.user_nicename,'-',us.display_name) AS name
					from #__jbusinessdirectory_company_editors as ce
					left join #__users as us on us.id = ce.editor_id
					where company_id='$companyId'";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCompanyEditor($companyId, $editorId){
		$db =JFactory::getDBO();
		$query = "select ce.*
					from #__jbusinessdirectory_company_editors as ce
					where company_id=$companyId and editor_id=$editorId ";
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

}
