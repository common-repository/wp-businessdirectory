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
require_once(BD_HELPERS_PATH . '/category_lib.php');
JTable::addIncludePath(DS . 'components' . 'com_jbusinessdirectory' . DS . 'tables');

/**
 * List Model.
 *
 * @package     JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelManageCompanies extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'bc.id',
				'registrationCode', 'bc.registrationCode',
				'address', 'bc.address',
				'type', 'ct.name',
				'viewCount', 'bc.viewCount',
				'contactCount', 'bc.contactCount',
				'state', 'bc.state',
				'approved', 'bc.approved'
			);
		}

		$this->appSettings = JBusinessUtil::getApplicationSettings();

		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'ManageCompany', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems() {

		//update statistics
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models', 'statistics');
		$modelStatistics = JModelLegacy::getInstance('Statistics', 'JBusinessDirectoryModel');
		$modelStatistics->getArchiveStatistics();

		// Load the list items.
		$items = parent::getItems();

		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		} else {
			foreach ($items as $company) {
				$company->active = true;
				$company->features = explode(",", (string)$company->features);

				$company->checklist = JBusinessUtil::getCompletionProgress($company, 1);
				$company->progress  = 0;

				if (count($company->checklist) > 0) {
					// calculate percentage of completion
					$count     = 0;
					$completed = 0;
					foreach ($company->checklist as $key => $val) {
						if ($val->status) {
							$completed++;
						}
						$count++;
					}
					$company->progress = (float) ($completed / $count);
				}

				$company->progress = round($company->progress, 4);
			}
		}

		if ($this->appSettings->enable_packages) {
			$items = JBusinessUtil::processPackages($items);
			foreach($items as $item){
				if(!empty($item->packgeInfo)){
					$item->currentPackage = end($item->packgeInfo);
					if(count($item->packgeInfo)>1){
						$item->lastPaidPackage = prev($item->packgeInfo);
					}
				}
			}

			$items = SubscriptionService::processSubscriptions($items);			
		}

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($items);
		}

		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		JBusinessUtil::setGroupConcatLenght();

		$query = "SELECT bc.*, clm.id AS claimId, count(bk.id) as nr_bookmarks,
                    GROUP_CONCAT(DISTINCT inv.start_date,'|',IFNULL(inv.start_trial_date, ''),'|',inv.state,'|',inv.package_id,'|',inv.id,'|',inv.company_id,'|',inv.end_date,'|',inv.amount,'|',IFNULL(inv.trial_amount, ''),'|',inv.only_trial  SEPARATOR '#|') AS orders,
                    GROUP_CONCAT(DISTINCT pf.feature) AS features,
					websiteCnt.websiteCounts";

		if ($this->appSettings->enable_packages) {
			$query .= " ,sb.id as sub_id, sb.status as sub_status, sb.subscription_id as sub_subscription_id, sb.time_unit as sub_time_unit, sb.time_amount as sub_time_amount, sb.processor_type as sub_processor_type, sb.created as sub_created,
						ord.payment_date as sub_payment_date, ord.payment_status as sub_payment_status, ord.order_id as sub_order_id";
		}			

		$query .= "	FROM #__jbusinessdirectory_companies AS bc
   					LEFT JOIN #__jbusinessdirectory_company_category cc ON bc.id=cc.companyId
					LEFT JOIN #__jbusinessdirectory_company_claim AS clm ON bc.id=clm.companyId
					LEFT JOIN #__jbusinessdirectory_packages pk ON bc.package_id=pk.id
                    LEFT JOIN #__jbusinessdirectory_package_fields pf ON pk.id=pf.package_id
                    LEFT JOIN #__jbusinessdirectory_orders inv ON inv.company_id = bc.id
					LEFT JOIN #__users u ON bc.userId=u.id
					LEFT JOIN #__jbusinessdirectory_bookmarks bk on bk.item_id = bc.id and bk.item_type = ".BOOKMARK_TYPE_BUSINESS."
					LEFT JOIN `#__jbusinessdirectory_company_editors` as ce on ce.company_id = bc.id

					left join 
					( select sum(starchWebsite.item_count) as websiteCounts, item_id
						from `#__jbusinessdirectory_statistics_archive` starchWebsite where starchWebsite.item_type = '".STATISTIC_ITEM_BUSINESS."' and starchWebsite.type='".STATISTIC_TYPE_WEBSITE_CLICK."'   group by item_id
					) as websiteCnt on websiteCnt.item_id = bc.id 
					
                    ";

		if ($this->appSettings->enable_packages) {
	
			$query .= " LEFT JOIN (
				SELECT sb.*
					FROM #__jbusinessdirectory_subscriptions AS sb
					WHERE sb.id = (
						SELECT MAX(id)
						FROM #__jbusinessdirectory_subscriptions
						WHERE company_id = sb.company_id
					)
				) AS sb ON sb.company_id = bc.id ";

			$query .= " LEFT JOIN (
							select inv.*, p.payment_date, p.payment_status
							from #__jbusinessdirectory_orders as inv
							LEFT JOIN (
								select p.*
								from #__jbusinessdirectory_payments as p
								where p.payment_status = ".PAYMENT_STATUS_PAID."
								order by p.payment_id desc
							) as p on p.order_id = inv.id
							order by inv.id desc
						) as ord on ord.subscription_id = sb.id ";
		}

		$where = " where 1 ";

		$user  = JBusinessUtil::getUser();
		$where .= ' and bc.userId =' . $user->ID . ' or ce.editor_id = '.$user->ID ;

		$groupBy = " group by bc.id ";

		// Add the list ordering clause.
		$orderBy = " order by " . $db->escape($this->getState('list.ordering', 'bc.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC'));

		$query = $query . $where;
		$query = $query . $groupBy;
		$query = $query . $orderBy;

		//dump($query);
		//exit;

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication('administrator');

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState('bc.id', 'desc');
	}

	public function getCompanyTypes() {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyTypes();
	}

	/**
	 * Retrieves the active package for a company
	 *
	 * @param $companyId int ID of the company
	 *
	 * @return mixed
	 *
	 * @since 5.2.0
	 */
	public function getActivePackage($companyId) {
		$table   = $this->getTable("Package");
		$package = $table->getCompanyPackage($companyId);

		return $package;
	}

	/**
	 * Returns a list of packages and prepares a details text for each of them based on their
	 * configuration.
	 *
	 * @return mixed
	 *
	 * @since 5.2.0
	 */
	public function getPackages($showAdmin = true) {
		$table    = $this->getTable("Package");
		$packages = $table->getPackages($showAdmin,false, PACKAGE_TYPE_BUSINESS);

		foreach ($packages as &$package) {
			JBusinessUtil::preparePackageText($package);
		}

		return $packages;
	}

	public function getTotal() {
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$user           = JBusinessUtil::getUser();
			$companiesTable = $this->getTable("Company");
			$this->_total   = $companiesTable->getTotalListings($user->ID);
		}
		return $this->_total;
	}

	public function getStates() {
		$states       = array();
		$state        = new stdClass();
		$state->value = 0;
		$state->text  = JTEXT::_("LNG_INACTIVE");
		$states[]     = $state;
		$state        = new stdClass();
		$state->value = 1;
		$state->text  = JTEXT::_("LNG_ACTIVE");
		$states[]     = $state;

		return $states;
	}

	public function getStatuses() {
		$statuses      = array();
		$status        = new stdClass();
		$status->value = COMPANY_STATUS_CLAIMED;
		$status->text  = JTEXT::_("LNG_NEEDS_CLAIM_APROVAL");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = COMPANY_STATUS_CREATED;
		$status->text  = JTEXT::_("LNG_NEEDS_CREATION_APPROVAL");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = COMPANY_STATUS_DISAPPROVED;
		$status->text  = JTEXT::_("LNG_DISAPPROVED");
		$statuses[]    = $status;
		$status        = new stdClass();
		$status->value = COMPANY_STATUS_APPROVED;
		$status->text  = JTEXT::_("LNG_APPROVED");
		$statuses[]    = $status;

		return $statuses;
	}
}
