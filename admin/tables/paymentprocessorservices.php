<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class JTablePaymentProcessorServices
 */
class JTablePaymentProcessorServices extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_payment_processor_services', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getPaymentProcessorServices($processorId) {
		if (empty($processorId)) {
			$processorId = 0;
		}
		$db    = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_payment_processor_services where processor_id = $processorId";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function deleteProcessorServices($ids) {
		$db    = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_payment_processor_services WHERE processor_id in ($ids)";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
}
