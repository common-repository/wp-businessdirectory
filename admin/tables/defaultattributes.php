<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableDefaultAttributes extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_default_attributes', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function getAttributesConfiguration($type) {
		switch ($type) {
			case DEFAULT_ATTRIBUTE_TYPE_LISTING:
				$select = 'id, name, listing_config as config';
				break;
			case DEFAULT_ATTRIBUTE_TYPE_OFFER:
				$select = 'id, name, offer_config as config';
				break;
			case DEFAULT_ATTRIBUTE_TYPE_EVENT:
				$select = 'id, name, event_config as config';
				break;
			default:
				$select = 'id, name, listing_config as config';
				break;
		}
		$query = "SELECT $select FROM #__jbusinessdirectory_default_attributes";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
