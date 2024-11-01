<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryModelManageCities extends JModelLegacy {

	/**
	 * Get all cities or by region (based on region ID)
	 *
	 * @param null $regionId int ID of the region
	 *
	 * @return mixed
	 *
	 * @since 4.9.4
	 */
	public function getCities($regionParam = null) {
		$table = $this->getTable('City', 'JTable');
		if(is_numeric($regionParam)){
			if (is_array($regionParam)) {
				$regionParam = implode(",", $regionParam);
			}
			$cities = $table->getCitiesByRegions($regionParam);
		} else {
			$cities = $table->getCitiesByRegionName($regionParam);
		}
		return $cities;
	}
}