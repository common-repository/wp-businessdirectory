<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTablePaymentProcessor extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_payment_processors', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function getPaymentProcessor($id) {
		$db    = JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_payment_processors WHERE id=" . $id;
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}

	public function getCompanyPaymentProcessors($companyId = -1) {
		$companyId = (int) $companyId;

		$db    = JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_payment_processors WHERE company_id=" . $companyId;
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getPaymentProcessorsByType($type, $companyId = -1) {
		$companyId = (int) $companyId;
		
		if (empty($companyId)) {
			$companyId = -1;
		}
		
		$db    = JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_payment_processors WHERE type='$type' and company_id=" . $companyId;
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	
	public function getPaymentProcessorByName($name) {
		$db    = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_payment_processors where type='$name'";
		$db->setQuery($query);
		$processor = $db->loadObject();

		if (isset($processor)) {
			$fields = $this->getPaymentProcessorFields($processor->id);
			foreach ($fields as $field) {
				$processor->{$field->column_name} = $field->column_value;
			}
		}
		return $processor;
	}

	public function getPaymentProcessorFields($processorId) {
		$query = " SELECT * FROM #__jbusinessdirectory_payment_processor_fields where processor_id=$processorId";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getPaymentProcessors() {
		$db    = JFactory::getDBO();
		$query = "SELECT * FROM #__jbusinessdirectory_payment_processors WHERE status=1 ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	public function changeState($processorId) {
		$db    = JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_payment_processors SET status = IF(status, 0, 1) WHERE id = " . $processorId;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function changeFrontState($processorId) {
		$db    = JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_payment_processors SET displayFront = IF(displayFront, 0, 1) WHERE id = " . $processorId;
		$db->setQuery($query);

		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getUserPaymentProcessorsSql($userId, $companyIds, $companyId = null) {
		$userId = (int) $userId;

		$whereCompany = '';
		if (!empty($companyId)) {
			$companyId    = (int) $companyId;
			$whereCompany = " and pp.company_id = $companyId";
		}

		$query = "select pp.*, cp.name as companyName
				  from #__jbusinessdirectory_payment_processors as pp
				  left join #__jbusinessdirectory_companies as cp on cp.id = pp.company_id
				  where 1 and (cp.id in ($companyIds)) and cp.userId = $userId $whereCompany";

		return $query;
	}

	public function getUserPaymentProcessors($userId, $companyIds, $limitstart = 0, $limit = 0, $companyId = null) {
		if (empty($companyIds) || empty($userId)) {
			return null;
		}

		$companyIds = implode(",", $companyIds);

		$query = $this->getUserPaymentProcessorsSql($userId, $companyIds, $companyId);

		$db = JFactory::getDBO();
		$db->setQuery($query, $limitstart, $limit);

		return $db->loadObjectList();
	}

	public function getTotalUserPaymentProcessors($userId, $companyIds, $companyId = null) {
		if (empty($companyIds) || empty($userId)) {
			return null;
		}

		$companyIds = implode(",", $companyIds);

		$query = $this->getUserPaymentProcessorsSql($userId, $companyIds, $companyId);
		$db    = JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();

		return $db->getNumRows();
	}
}
