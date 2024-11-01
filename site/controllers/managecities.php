<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerManageCities extends JControllerLegacy {

    /**
	 * Method to retrieve cities by region (ajax)
	 */
	public function getCitiesByRegionsAjax() {
		$regionParam = JFactory::getApplication()->input->get('regionParam', '', 'RAW');
		$model  = $this->getModel('ManageCities');
		$result = $model->getCities($regionParam);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

}