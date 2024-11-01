<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerExport extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}
	
	// public function exportFiles() {
	// 	$path = JFactory::getApplication()->input->get("path");
	
	// 	if (empty($path)) {
	// 		return;
	// 	}
		
	// 	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/companies.php');
	// 	$companiesModel = new JBusinessDirectoryModelCompanies();
	// 	$companiesModel->exportCompaniesCSVtoFile($path."/business_listings.csv");
	
	// 	exit;
		
	// 	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/offers.php');
	// 	$offersModel = new JBusinessDirectoryModelOffers();
	// 	$offersModel->exportOffersCSVtoFile($path."/offers.csv");
	
	// 	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/events.php');
	// 	$eventsModel = new JBusinessDirectoryModelEvents();
	// 	$eventsModel->exportEventsCSVtoFile($path."/events.csv");

	// 	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/conferences.php');
	// 	$conferencesModel = new JBusinessDirectoryModelConferences();
	// 	$conferencesModel->exportConferencesCSVtoFile($path."/conferences.csv");

	// 	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/sessionlocations.php');
	// 	$sessionLocationsModel = new JBusinessDirectoryModelSessionlocations();
	// 	$sessionLocationsModel->exportSessionLocationsModelCSVtoFile($path."/sessionlocations.csv");
		
	// 	exit;
	// }
}
