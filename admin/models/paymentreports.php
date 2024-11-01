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
/**
 * Reports model
 *
 * @package     JBusinessDirectory
 * @since       1.0.0
 */
class JBusinessDirectoryModelPaymentReports extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.0.0
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'type', 'type',
				'payment_status', 'payment_status',
				'start_date', 'start_date',
				'end_date', 'end_date',
				'created', 'pm.created',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string $type   The table name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Report', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
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
	 * @since   1.0.0
	 * @throws Exception
	 */
	protected function populateState($ordering = "pm.created", $direction = "desc") {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$startDate = $app->getUserStateFromRequest($this->context . '.filter.start_date', 'filter_start_date');
		$this->setState('filter.start_date', $startDate);

		$endDate = $app->getUserStateFromRequest($this->context . '.filter.end_date', 'filter_end_date');
		$this->setState('filter.end_date', $endDate);

		$itemType = $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
		$this->setState('filter.type', $itemType);

		$status = $app->getUserStateFromRequest($this->context . '.filter.payment_status', 'filter_payment_status');
		$this->setState('filter.payment_status', $status);

		$currency = $app->getUserStateFromRequest($this->context . '.filter.currency', 'filter_currency');
		$this->setState('filter.currency', $currency);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Retrieves items based on the report type
	 *
	 * @return mixed|null
	 *
	 * @since 1.0.0
	 */
	public function getItems() {
		$items = $this->getPaymentReport();

		return $items;
	}

	/**
	 * Overrides parent method. Get's the total number of items retrieved and caches it.
	 * Needed for pagination.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function getTotal() {
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		try {
			// Load the total and add the total to the internal cache.
			$this->cache[$store] = (int) $this->getTotalItems();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * Get's the total number of items based on the report type.
	 *
	 * @return null|int
	 *
	 * @since 1.0.0
	 */
	private function getTotalItems() {
		$table = $this->getTable();
		$total = $table->getTotalPaymentReport($this->getSearchDetails());

		return $total;
	}

	/**
	 * Create an array with search details for each type of report based on the search criteria submitted by
	 * the user.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	private function getSearchDetails() {
		$order =  $this->getState('list.ordering');
		$orderDir = $this->getState('list.direction');

		$searchDetails = array();
		$searchDetails['keywordSearch']    = $this->getState('filter.search');
		$searchDetails['payment_status']    = $this->getState('filter.payment_status');
		$searchDetails['currency']    = $this->getState('filter.currency');
		$searchDetails['start_date'] = !JBusinessUtil::emptyDate($this->getState('filter.start_date')) ? JBusinessUtil::convertToMysqlFormat($this->getState('filter.start_date')) : null;
		$searchDetails['end_date']   = !JBusinessUtil::emptyDate($this->getState('filter.end_date')) ? JBusinessUtil::convertToMysqlFormat($this->getState('filter.end_date')) : null;
		$searchDetails['item_type']    = $this->getState('filter.type');
		$searchDetails['user']    = $this->getState('filter.user_id');
		$searchDetails['order'] = $order;
		$searchDetails['dir'] = $orderDir;
		return $searchDetails;
	}


	/**
	 * Retrieves members based on the epc credits criteria from the database and returns them. Limits the number of items
	 * returned if list.limit is set.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	public function getPaymentReport() {
		$searchDetails      = $this->getSearchDetails();
		$paymentStatuses    = $this->getPaymentStatuses();
		$paymentTypes       = $this->getPaymentTypes();
		$commissionRates    = $this->getCommissionRates();
		$appSettings        = JBusinessUtil::getApplicationSettings();
		$defaultCurrency    = JBusinessUtil::getCurrency($appSettings->currency_id);
		$table              = $this->getTable();
		$items              = $table->getPaymentReport($searchDetails, $this->getStart(), $this->getState('list.limit'));
		
		$totalEarnings = array();
		$totalCommissions = array();
		foreach ($items as $key => $item) {	
			if ($item->payment_status == PAYMENT_STATUS_PAID) {
				$item->commission = $commissionRates[$item->type]*$item->amount;
				if (!empty($item->currency)) {
					if (!isset($totalEarnings[$item->currency])) {
						$totalEarnings [$item->currency] = $item->amount;
						$totalCommissions [$item->currency] = $item->commission;
					} else {
						$totalEarnings [$item->currency] += $item->amount;
						$totalCommissions [$item->currency] += $item->commission;
					}
				} else {
					if (!isset($totalEarnings [$defaultCurrency->currency_name])) {
						$totalEarnings [$defaultCurrency->currency_name] = $item->amount;
						$totalCommissions [$defaultCurrency->currency_name] = $item->commission;
					} else {
						$totalEarnings [$defaultCurrency->currency_name] += $item->amount;
						$totalCommissions [$defaultCurrency->currency_name] += $item->commission;
					}
				}
			} else {
				$item->commission = 0;
			}

			$item->payment_status_string = $paymentStatuses[$item->payment_status];
			switch($item->type){
				case PAYMENT_TYPE_SERVICE:
					$item->name = $item->sname;
					break;
				case PAYMENT_TYPE_CAMPAIGN:
					$item->name = $item->cname;
					break;
				case PAYMENT_TYPE_OFFER:
					$item->name = $item->oname;
					break;
				case PAYMENT_TYPE_EVENT:
					$item->name = $item->ename;
					break;
			}
			
			$item->type = $paymentTypes[$item->type];
			$item->created = JBusinessUtil::getDateGeneralFormatWithTime($item->created);
			$item->commission_string = $item->commission . " (" .$item->currency.")";
		} 

		foreach ($totalEarnings as $key => &$earning) {
			$earning = $earning." (".$key.") ";
		}
		foreach ($totalCommissions as $key => &$commission) {
			$commission = $commission." (".$key.") ";
		}

		if (!empty($items)) {
			if (empty($totalEarnings)) {
				$totalEarnings[$defaultCurrency->currency_name] = "0 (".$defaultCurrency->currency_name.") ";
				$totalCommissions[$defaultCurrency->currency_name] = "0 (".$defaultCurrency->currency_name.") ";
			}
			$items[0]->totalAmount = implode(', ', $totalEarnings);
			$items[0]->totalCommission = implode(', ', $totalCommissions);
		}
		
		return $items;
	}


	/**
	 * Returns an array of report headers based on the report type.
	 *
	 * @return array of report headers (objects)
	 *
	 * @since 1.0.0
	 */
	public function getReportHeaders() {
		$headers       = array();

		$header        = new stdClass();
		$header->field = 'name';
		$header->text  = JText::_('LNG_NAME');
		$header->sort  = false;
		$headers[]     = $header;

		$header        = new stdClass();
		$header->field = 'type';
		$header->text  = JText::_('LNG_TYPE');
		$header->sort  = false;
		$headers[]     = $header;

		$header        = new stdClass();
		$header->field = 'processor_type';
		$header->text  = JText::_('LNG_PAYMENT_METHOD');
		$header->sort  = false;
		$headers[]     = $header;

		$header        = new stdClass();
		$header->field = 'order_amount';
		$header->text  = JText::_('LNG_AMOUNT');
		$header->sort  = false;
		$headers[]     = $header;

		$header        = new stdClass();
		$header->field = 'created';
		$header->text  = JText::_('LNG_CREATED');
		$header->sort  = false;
		$headers[]     = $header;

		$header        = new stdClass();
		$header->field = 'payment_status_string';
		$header->text  = JText::_('LNG_PAYMENT_STATUS');
		$header->sort  = false;
		$headers[]     = $header;

		$header        = new stdClass();
		$header->field = 'commission_string';
		$header->text  = JText::_('LNG_COMMISSION');
		$header->sort  = false;
		$headers[]     = $header;

		return $headers;
	}

	/**
	 * Retrieves the data and formats them into a csv string based on the report type
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getReportCSV() {
		$delimiter = ",";
		$items     = $this->getItems();

		$csv_output = '';
		$headers    = $this->getReportHeaders();
		$count      = 0;
		foreach ($headers as $header) {
			$csv_output .= $header->text;
			
			if (count($headers) > $count) {
				$csv_output .= $delimiter;
			}

			$count++;
		}
		$csv_output .= "\n";

		foreach ($items as $item) {
			$count = 0;
			foreach ($headers as $header) {
				$value="";
				if (isset($item->{$header->field})) {
					$value = $item->{$header->field};
				}

				if (!empty($value)) {
					$value = preg_replace('~[\r\n]+~', '', $value);
				}
				
				$csv_output .= "\"" . $value . "\"";

				if (count($headers) > $count) {
					$csv_output .= $delimiter;
				}

				$count++;
			}
			$csv_output .= "\n";
		}

		return $csv_output;
	}

	/**
	 * Method that exports the epc courses in a csv file
	 *
	 * @param $report_type int type of the report
	 *
	 * @since 1.0.0
	 */
	public function exportReportToCsv() {
		$this->populateState();

		// generate the csv only if there's a report type selected
		$csv_output = $this->getReportCSV();

		$fileName = "payment_report";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=" . $fileName . ".csv");
		print $csv_output;
	}

	public function getPaymentStatuses() {
		$paymentStatuses = array();
		$paymentStatuses[PAYMENT_STATUS_PAID] = JTEXT::_("LNG_PAYMENT_STATUS_PAID");
		$paymentStatuses[PAYMENT_STATUS_NOT_PAID] = JTEXT::_("LNG_PAYMENT_STATUS_NOT_PAID");
		$paymentStatuses[PAYMENT_STATUS_FAILURE] = JTEXT::_("LNG_PAYMENT_STATUS_FAILURE");
		$paymentStatuses[PAYMENT_STATUS_CANCELED] = JTEXT::_("LNG_PAYMENT_STATUS_CANCELED");
		$paymentStatuses[PAYMENT_STATUS_WAITING] = JTEXT::_("LNG_PAYMENT_STATUS_WAITING");
		$paymentStatuses[PAYMENT_STATUS_PENDING] = JTEXT::_("LNG_PAYMENT_STATUS_PENDING");

		return $paymentStatuses;
	}

	public function getPaymentTypes() {
		$paymentTypes = array();
		$paymentTypes[PAYMENT_TYPE_PACKAGE] = JTEXT::_("LNG_PAYMENT_TYPE_PACKAGE");
		$paymentTypes[PAYMENT_TYPE_SERVICE] = JTEXT::_("LNG_PAYMENT_TYPE_SERVICE");
		$paymentTypes[PAYMENT_TYPE_CAMPAIGN] = JTEXT::_("LNG_PAYMENT_TYPE_CAMPAIGN");
		$paymentTypes[PAYMENT_TYPE_OFFER] = JTEXT::_("LNG_PAYMENT_TYPE_OFFER");
		$paymentTypes[PAYMENT_TYPE_EVENT] = JTEXT::_("LNG_PAYMENT_TYPE_EVENT");

		return $paymentTypes;
	}

	public function getCommissionRates() {
		$appSettings        = JBusinessUtil::getApplicationSettings();

		if(!is_numeric($appSettings->appointments_commission) || empty($appSettings->appointments_commission)) {
			$appSettings->appointments_commission = 0;
		}

		if(!is_numeric($appSettings->offer_selling_commission) || empty($appSettings->offer_selling_commission)) {
			$appSettings->offer_selling_commission = 0;
		}

		if(!is_numeric($appSettings->event_tickets_commission) || empty($appSettings->event_tickets_commission)) {
			$appSettings->event_tickets_commission = 0;
		}
		
		$commissionRates = array();
		$commissionRates[PAYMENT_TYPE_PACKAGE] = 0;
		$commissionRates[PAYMENT_TYPE_CAMPAIGN] = 0;
		$commissionRates[PAYMENT_TYPE_SERVICE] = $appSettings->appointments_commission/100;
		$commissionRates[PAYMENT_TYPE_OFFER] = $appSettings->offer_selling_commission/100;
		$commissionRates[PAYMENT_TYPE_EVENT] = $appSettings->event_tickets_commission/100;

		return $commissionRates;
	}	
}