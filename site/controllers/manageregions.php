<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerManageRegions extends JControllerLegacy {

    /**
	 * Method to retrieve counties by country (ajax)
	 */
	public function getRegionsByCountryAjax() {
		$countryId = (int) JFactory::getApplication()->input->get('countryId');
		$model  = $this->getModel('ManageRegions');
		$result = $model->getRegions($countryId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

}