<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

class JBusinessDirectoryModelReports extends JModelList {

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
				'id', 'r.id',
				'name', 'r.name',
				'description', 'r.description'
			);
		}

		parent::__construct($config);
	}
	
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId('getItems');
	
		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}
	
		// Load the list items.
		$items = parent::getItems();
	
		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		}
	
		// Add the items to the internal cache.
		$this->cache[$store] = $items;
	
		return $this->cache[$store];
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
		$query = $db->getQuery(true);
	
	
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'r.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_reports').' AS r');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("r.name LIKE '%" . trim($db->escape($search)) . "%'");
		}
	
		$query->group('r.id');
	
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'r.id')).' '.$db->escape($this->getState('list.direction', 'ASC')));
	
		return $query;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication('administrator');
	
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
	
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	
		// List state information.
		parent::populateState('r.id', 'desc');
	}
	

	public function getReports() {
		$reportTable = JTable::getInstance("Report", "JTable");
		$reports = $reportTable->getReports();
		
		return $reports;
	}
	
	public function getReportData() {
		$input = JFactory::getApplication()->input;
		$reportId = $input->get("reportId");

		if (empty($reportId)) {
			return null;
		}
		
		$reportTable = JTable::getInstance("Report", "JTable");
		$report = $reportTable->getReport($reportId);
		//set 2 manually because an listing can have only 2 values for the state
		//1->active
		//0->inactive
		//2->means all the listings
		$displayActive = $report->listing_status;

		if ($report->type == 1) {
			$reportData = $reportTable->getConferenceReportData($report->selected_params);
		} else if ($report->type == 2) {
			$reportData = $reportTable->getOfferReportData($report, $displayActive);
			foreach ($reportData as $data) {
				if(isset($data->item_selling_type)){
					switch ($data->item_selling_type) {
						case '0':
							$data->item_selling_type = JText::_('LNG_DISABLED');
							break;
						case '1':
							$data->item_selling_type = JText::_('LNG_OFFER');
							break;
						case '2':
							$data->item_selling_type = JText::_('LNG_COUPON');
							break;
					}
				}
			}
		} else {
			$orderBy = $input->get("orderBy", "cp.id");
			$reportData = $reportTable->getReportData($report, $orderBy, $displayActive);
			$reportData = JBusinessUtil::processPackages($reportData);
			foreach ($reportData as $data) {
				$data->viewCount = 0;
				if (!empty($data->viewCounts)) {
					$viewCounts = explode('##', $data->viewCounts);
					foreach ($viewCounts as $viewCount) {
						$data->viewCount += explode('-', $viewCount)[1];
					}
				}

				$data->contactCount = 0;
				if (!empty($data->contactCounts)) {
					$contactCounts = explode('##', $data->contactCounts);
					foreach ($contactCounts as $contactCount) {
						$data->contactCount += explode('-', $contactCount)[1];
					}
				}

				$data->websiteCount = 0;
				if (!empty($data->websiteCounts)) {
					$websiteCounts = explode('##', $data->websiteCounts);
					foreach ($websiteCounts as $websiteCount) {
						$data->websiteCount += explode('-', $websiteCount)[1];
					}
				}

				$data->whatsupCount = 0;
				if (!empty($data->whatsupCounts)) {
					$websiteCounts = explode('##', $data->whatsupCounts);
					foreach ($websiteCounts as $websiteCount) {
						$data->whatsupCount += explode('-', $websiteCount)[1];
					}
				}

				if (!empty($data->packgeInfo)) {
					$data->package = $data->packgeInfo[0]->name;
					$packageInfo = end($data->packgeInfo);
					$data->start_package = $packageInfo->start_date;
					$data->expire_package = $packageInfo->expirationDate;
				} else {
					$data->start_package = '-';
					$data->expire_package = '-';
				}
			}
		}
		
		$generatedReport = new stdClass();
		$generatedReport->headers = explode(",", $report->selected_params);
		
		$generatedReport->customHeaders = explode(",", $report->custom_params);
		//var_dump($generatedReport->customHeaders);
		$generatedReport->data = $reportData;
		$generatedReport->report = $report;

		if (($report->type == 0) || ($report->type == 2))  {
			$attributesTable = JTable::getInstance("Attribute", "JTable");
			$generatedReport->attributes = $attributesTable->getAttributes();
			$generatedReport->customHeaders= $this->processHeaders($generatedReport->customHeaders, $generatedReport->attributes);
			
			$attributeOptionsTable = JTable::getInstance("AttributeOptions", "JTable");
			$attributeOptions = $attributeOptionsTable->getAllAttributeOptions();
		
			$generatedReport->data = $this->processData($generatedReport->data, $attributeOptions);
		}

		$customHeaders = $this->renderHeaders($generatedReport->data, $generatedReport->customHeaders);
		$generatedReport->customHeaders = $customHeaders!=null?$customHeaders:"";

		return $generatedReport;
	}

	public function renderHeaders($datas, $customHeaders) {
		foreach ($datas as $data) {
			if (!empty($data->customAttributes)) {
				foreach ($data->customAttributes as $i => $attribute) {
					foreach ($customHeaders["id"] as $index => $id) {
						if ($attribute->id == $id) {
							$customHeaders["name"][$index] = $i;
						}
					}
				}
			}
		}
		if (!isset($customHeaders["name"])) {
			return null;
		}
		unset($customHeaders["id"]);
		return $customHeaders["name"];
	}
	
	public function processData($reportData, $attributeOptions) {

		foreach ($reportData as $data) {

			$data->customAttributes = array();
			$customAttributes = explode("#", $data->custom_attributes);
			$index = 2;
			foreach ($customAttributes as $customAttribute) {
				$values = explode("||", $customAttribute);
				$obj = new stdClass();
			
				if (count($values)<=4) {
					continue;
				}

				$obj->name = $values[0];
				$obj->code = $values[1];
				$obj->atr_code = $values[2];
				$obj->value = $values[3];
				$obj->id = $values[4];
				if ($obj->atr_code !="input") {
					$values = explode(",", $obj->value);
					$result = array();
					foreach ($values as $value) {
						foreach ($attributeOptions as $attributeOption) {
							if ($value == $attributeOption->id) {
								$result[] = $attributeOption->name;
							}
						}
					}
					if (!empty($result)) {
						$obj->value = implode(",", $result);
					}
				}
				if (!array_key_exists($obj->name, $data->customAttributes)) {
					$data->customAttributes[$obj->name] = $obj;
				}
			}
		}

		return $reportData;
	}
	
	public function processHeaders($headers, $attributes) {
		$result = array();
		$result["id"] = array();
		foreach ($headers as $header) {
			foreach ($attributes as $attribute) {
				if ($attribute->code == $header && !in_array($attribute->id, $result["id"])) {
					$result["name"][] = $attribute->name;
					$result["id"][] = $attribute->id;
				}
			}
		}
		return $result;
	}

	public function exportReportToCSV($generatedReport) {
		$output = $this->getReportsToExportCSV($generatedReport);

		$fileName = "jbusinessdirectory_report";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$fileName.".csv");

		print $output;
	}

	public function getReportsToExportCSV($generatedReport) {
		require_once BD_HELPERS_PATH.'/helper.php';
		$delimiter = JFactory::getApplication()->input->getString("delimiter");
		$csv_output = '';
		$params = JBusinessDirectoryHelper::getCompanyParams();
		$conferenceParams = JBusinessDirectoryHelper::getConferenceParams();
		$offerParams = JBusinessDirectoryHelper::getOfferParams();

		if ($generatedReport->report->type == 1) {
			foreach ($generatedReport->headers as $header) {
				$csv_output .= strtolower(JText::_($params[$header]));
				$csv_output .= $delimiter;
			}
		} else if ($generatedReport->report->type == 2) {
			foreach ($generatedReport->headers as $header) {
				$csv_output .= strtolower(JText::_($offerParams[$header]));
				$csv_output .= $delimiter;
			}
			if (!empty($generatedReport->customHeaders)) {
				foreach ($generatedReport->customHeaders as $header) {
					$csv_output .= $header;
					$csv_output .= $delimiter;
				}
			}
		} else {
			foreach ($generatedReport->headers as $header) {
				$csv_output .= strtolower(JText::_($params[$header]));
				$csv_output .= $delimiter;
			}
			if (!empty($generatedReport->customHeaders)) {
				foreach ($generatedReport->customHeaders as $header) {
					$csv_output .= $header;
					$csv_output .= $delimiter;
				}
			}
		}
		
		$csv_output .= "\n";

		if ($generatedReport->report->type == 1) {
			foreach ($generatedReport->data as $data) {
				foreach ($generatedReport->headers as $header) {
					$csvElement = $data->$header;
					$csvElement = str_replace(array("\r\n", "\r", "\n"), '<br />', $csvElement);
					$csvElement = str_replace('"', '""', $csvElement);
					$csv_output .= "\"$csvElement\"";
					$csv_output .= $delimiter;
				}
				$csv_output .= "\n";
			}
		} else {
			foreach ($generatedReport->data as $data) {
				foreach ($generatedReport->headers as $header) {
					$csvElement = $data->$header;
					$csvElement = str_replace(array("\r\n", "\r", "\n"), '<br />', $csvElement);
					$csvElement = str_replace('"', '""', $csvElement);
					$csv_output .= "\"$csvElement\"";
					$csv_output .= $delimiter;
				}
				if (!empty($generatedReport->customHeaders)) {
					foreach ($generatedReport->customHeaders as $header) {
						$csvElement = !empty($data->customAttributes[$header]) ? $data->customAttributes[$header]->value : "";
						$csvElement = str_replace(array("\r\n", "\r", "\n"), '<br />', $csvElement);
						$csvElement = str_replace('"', '""', $csvElement);
						$csv_output .= "\"$csvElement\"";
						$csv_output .= $delimiter;
					}
				}
				$csv_output .= "\n";
			}
		}

		return $csv_output;
	}
}
