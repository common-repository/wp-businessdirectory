<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'company.php');

class JBusinessDirectoryModelManageCompany extends JBusinessDirectoryModelCompany {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object    A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object    A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object    A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEdit($record) {
		return true;
	}

	protected function populateState() {
		$jinput    = JFactory::getApplication()->input;
		$companyId = $jinput->getInt("id", 0);
		$this->setState('company.id', $companyId);

		$packageId = $jinput->getInt('filter_package');
		if (isset($packageId)) {
			$this->setState('company.packageId', $packageId);
		}
	}

	public function updateCompanyOwner($companyId, $userId) {
		// Get a row instance.
		$table = $this->getTable("Company");
		$table->load($companyId);
		$table->userId = $userId;

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
	}

	public function getTotal() {
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$user = JBusinessUtil::getUser();
			if ($user->ID == 0) {
				return 0;
			}
			$companiesTable = $this->getTable("Company");
			$this->_total   = $companiesTable->getTotalListings($user->ID);
		}
		return $this->_total;
	}

	/**
	 * Checks if a certain company belongs to the current active user
	 *
	 * @param $companyId int ID of the company
	 *
	 * @return bool
	 *
	 * @since 5.2.0
	 */
	public function checkCompanyBelongsToUser($companyId) {
		$table = $this->getTable("Company");
		$table->load($companyId);

		$user = JBusinessUtil::getUser();
		if ($table->userId != $user->ID) {
			return false;
		}

		return true;
	}

	/**
	 * Changes the package for a company by creating an order. Returns an error/success message.
	 *
	 * @param $companyId int ID of the company
	 * @param $packageId int ID of the package
	 * @param $type      int type of the package update
	 *
	 * @return mixed
	 *
	 * @since 5.2.0
	 */
	public function changePackage($companyId, $packageId, $type) {
		if (!$this->checkCompanyBelongsToUser($companyId)) {
			return JText::_('LNG_INVALID_COMPANY');
		}

		if (!$this->createOrder($companyId, $packageId, $type, false)) {
			return JText::_('LNG_ERROR_UPGRADING_PACKAGE');
		}
		
		$companyTable = $this->getTable('Company', "JTable");
		$companyTable->setPackageId($companyId, $packageId);

		return JText::_('LNG_PACKAGE_UPGRADED');
	}

	public function getEditors() {
		$companyId = $this->getState('company.id');
		$editorTable = $this->getTable('CompanyEditor', "Table");
		$editors = $editorTable->getCompanyEditors($companyId);
		$result = array();
		foreach ($editors as $editor) {
			$result[] = $editor->value;
		}
		return $result;
	}

	/**
	 * Add company editor
	 *
	 * @param [int] $companyId
	 * @param [int] $editorId
	 * @return void
	 */
	public function addCompanyEditor($companyId, $editorId){
		$table = $this->getTable("CompanyEditor", "Table");

		$editor = $table->getCompanyEditor($companyId, $editorId);
		dump($editor);
		if(!empty($editor)){
			return $editor->editor_id;
		}

		$table->company_id = $companyId;
		$table->editor_id = $editorId;

		if (!$table->store()) {
			return false;
		}

		return true;
	}

	/**
	 * Start the profile trial period
	 *
	 * @return void
	 */
	public function startTrial($companyId){

		$table= $this->getTable("Order");
		$lastUnpaidOrder = $table->getLastUnpaidOrder($companyId, null);
		
		$packageTable = $this->getTable("Package");
		$package = $packageTable->getPackage($lastUnpaidOrder->package_id);

		$startDate = date('Y-m-d');
		$timeUnit = JBusinessUtil::getTimeUnit($package->time_unit);
		$endDate = date('Y-m-d', strtotime($startDate. " + $package->time_amount $timeUnit"));

		$lastUnpaidOrder->start_date = $startDate;
		$lastUnpaidOrder->end_date = $endDate;
		$lastUnpaidOrder->state = 1;

		$table->bind($lastUnpaidOrder);
		$table->store();

		return true;
	}
}
