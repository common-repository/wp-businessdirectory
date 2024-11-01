<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryModelManageRegions extends JModelLegacy {

	/**
	 * Get all regions or by country (based on country ID)
	 *
	 * @param null $countryId int ID of the country
	 *
	 * @return mixed
	 *
	 * @since 4.9.4
	 */
	public function getRegions($countryId = null) {
		$table = $this->getTable('Region', 'JTable');

		if (empty($countryId)) {
			$regions = $table->getRegions();
		} else {
			$regions = $table->getRegionsByCountry($countryId);
		}

		return $regions;
	}
}