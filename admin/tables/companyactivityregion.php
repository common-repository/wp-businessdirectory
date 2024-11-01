<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class JTableCompanyActivityRegion
 */
class JTableCompanyActivityRegion extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_activity_region', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function deleteNotContainedRegions($companyId, $regions) {
		if(empty($regions)){
			$regions = array(-1);
		}

		$db      = JFactory::getDbo();
		$regions = implode(",", $regions);
		$sql     = "delete from #__jbusinessdirectory_company_activity_region where company_id= $companyId and region_id not in ($regions)";

		$db->setQuery($sql);
		return $db->execute();
	}

	public function getActivityRegions($companyId) {
		$db    = JFactory::getDbo();
		$query = "select ac.*, c.name, c.id from #__jbusinessdirectory_company_activity_region ac
					left join #__jbusinessdirectory_regions c on ac.region_id=c.id
					where company_id= $companyId";

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getActivityRegion($companyId, $regionId) {
		$db    = JFactory::getDbo();
		$query = "select * from #__jbusinessdirectory_company_activity_region where company_id= $companyId and region_id = $regionId";
		$db->setQuery($query);

		return $db->loadObject();
	}
}
