<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTablePaymentProcessorFields extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_payment_processor_fields', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getPaymentProcessorFields($processorId) {
		if (empty($processorId)) {
			$processorId = 0;
		}
		$db =JFactory::getDBO();
		$query = " SELECT * FROM #__jbusinessdirectory_payment_processor_fields where processor_id=$processorId order by column_mode desc,id";

		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function deleteProcessorFields($ids) {
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_payment_processor_fields WHERE processor_id in ($ids)";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
