<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');
require_once(BD_HELPERS_PATH.'/category_lib.php');

class JBusinessDirectoryModelListingsMap extends JModelList {
	
	public function __construct() {
		parent::__construct();

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$jinput = JFactory::getApplication()->input;

	}

	/**
	 * Get listings map items
	 *
	 * @return void
	 */	
	public function getItems() {
		$companyTable = JTable::getInstance("Company", "JTable");
	
		$items = array();
		if ($this->appSettings->enable_cache) {
			$cacheIdentifier = 'jbd-listings-map';
			try {
				$cache = JCache::getInstance();
				$items = $cache->get($cacheIdentifier);
				if (empty($items)) {
					$items = $companyTable->getListingMap();
					$cache->store($items, $cacheIdentifier);
				}
			} catch (RuntimeException $e) {
				$this->setError($e->getMessage());
				return null;
			}
		}else{
			$items = $companyTable->getListingMap();
		}

		$result = array();
		if(!empty($items)){
			foreach($items as &$item){

				if(!empty($item->countryId)){

					if(!isset($result[$item->countryId])){
						$result[$item->countryId] = array();
					}

					if(!empty($item->county)){

						if(!isset($result[$item->countryId][$item->county])){
							$result[$item->countryId][$item->county] = array();
						}

						if(!empty($item->city)){

							if(!isset($result[$item->countryId][$item->county][$item->city])){
								$result[$item->countryId][$item->county][$item->city] = array();
							}

							if(!empty($item->categories)){
								$item->categories = explode("#",$item->categories);
								foreach($item->categories as &$cat){
									$cat = explode("|", $cat);
									$cat = array_filter($cat);
								}
							}

							$result[$item->countryId][$item->county][$item->city] = $item;
						}
					}
				}
			}
		}


		return $result;
	}
}