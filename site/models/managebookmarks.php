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
require_once(BD_HELPERS_PATH.'/category_lib.php');
require_once JPATH_COMPONENT_SITE.'/libraries/tfpdf/tfpdf.php';
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
/**
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelManageBookmarks extends JModelList {
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

		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'ManageBookmark', $prefix = 'JTable', $config = array()) {
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

        $items = $this->prepareBookmarks($items);

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
		$query->select($this->getState('list.select', 'b.id as bookmarkId,b.note, b.item_type, b.ordering'));
		$query->from($db->quoteName('#__jbusinessdirectory_bookmarks').' AS b');
		
		// Join over the companies
		$query->select('bc.id as company_id, bc.name as company_name, bc.alias as company_alias');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies').' AS bc ON bc.id=b.item_id and b.item_type='.BOOKMARK_TYPE_BUSINESS);

		// Join over the offers
		$query->select('co.id as offer_id, co.subject as offer_name, co.alias as offer_alias');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_offers').' AS co ON co.id=b.item_id  and b.item_type='.BOOKMARK_TYPE_OFFER);

        // Join over the speakers
        $query->select('cs.id as speaker_id, cs.name as speaker_name, cs.alias as speaker_alias');
        $query->join('LEFT', $db->quoteName('#__jbusinessdirectory_conference_speakers').' AS cs ON cs.id=b.item_id  and b.item_type='.BOOKMARK_TYPE_SPEAKER);

        // Join over the sessions
        $query->select('cns.id as session_id, cns.name as session_name, cns.alias as session_alias');
        $query->join('LEFT', $db->quoteName('#__jbusinessdirectory_conference_sessions').' AS cns ON cns.id=b.item_id  and b.item_type='.BOOKMARK_TYPE_SESSION);
		
		$user = JBusinessUtil::getUser();
		$query->where('b.user_id ='.$user->ID);

        $typeId = $this->getState('filter.type');
        if(!empty($typeId)) {
            $query->where('b.item_type =' . $typeId);
        }

		$query->group('b.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'b.ordering asc')).' '.$db->escape($this->getState('list.direction', 'ASC')));

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
		$app = JFactory::getApplication('site');

        $typeId = $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
        $this->setState('filter.type', $typeId);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState('b.ordering', 'asc');
	}
	
	
	public function getBookmarkTypes() {
        $types       = array();

        $type        = new stdClass();
        $type->id    = BOOKMARK_TYPE_BUSINESS;
        $type->name  = JTEXT::_("LNG_COMPANY");
        $types[]     = $type;

        $type        = new stdClass();
        $type->id    = BOOKMARK_TYPE_OFFER;
        $type->name  = JTEXT::_("LNG_OFFER");
        $types[]     = $type;

        $type        = new stdClass();
        $type->id    = BOOKMARK_TYPE_SPEAKER;
        $type->name  = JTEXT::_("LNG_SPEAKER");
        $types[]     = $type;

        $type        = new stdClass();
        $type->id    = BOOKMARK_TYPE_SESSION;
        $type->name  = JTEXT::_("LNG_SESSION");
        $types[]     = $type;

        return $types;
	}

    /**
     * Prepare the list of bookmarks with the corresponding item name
     *
     */
	private function prepareBookmarks($items){

        foreach($items as &$item) {
            switch ($item->item_type) {
                case BOOKMARK_TYPE_BUSINESS:
                    $item->itemName = !empty($item->company_name) ? $item->company_name : JText::_('LNG_ITEM_REMOVED');
                    $item->link = !empty($item->company_name) ? JBusinessUtil::getCompanyDefaultLink($item->company_id) : '';
                    $item->item_type =  JText::_('LNG_COMPANY');
                    break;
                case BOOKMARK_TYPE_OFFER:
                    $item->itemName = !empty($item->offer_name) ? $item->offer_name : JText::_('LNG_ITEM_REMOVED');
                    $item->link = !empty($item->offer_name) ? JBusinessUtil::getOfferLink($item->offer_id, $item->offer_alias) : '';
                    $item->item_type =  JText::_('LNG_OFFER');
                    break;
                case BOOKMARK_TYPE_EVENT:
                    $item->itemName = !empty($item->event_name) ? $item->event_name : JText::_('LNG_ITEM_REMOVED');
                    $item->link = !empty($item->event_name) ? JBusinessUtil::getEventLink($item->event_id, $item->event_alias) : '';
                    $item->item_type =  JText::_('LNG_EVENT');
                    break;
                case BOOKMARK_TYPE_CONFERENCE:
                    $item->itemName = !empty($item->conference_name) ? $item->conference_name : JText::_('LNG_ITEM_REMOVED');
                    $item->link = !empty($item->conference_name) ? JBusinessUtil::getConferenceLink($item->conference_id, $item->conference_alias) : '';
                    $item->item_type =  JText::_('LNG_CONFERENCE');
                    break;
                case BOOKMARK_TYPE_SPEAKER:
                    $item->itemName = !empty($item->speaker_name) ? $item->speaker_name : JText::_('LNG_ITEM_REMOVED');
                    $item->link = !empty($item->speaker_name) ? JBusinessUtil::getSpeakerLink($item->speaker_id, $item->speaker_alias) : '';
                    $item->item_type =  JText::_('LNG_SPEAKER');
                    break;
                case BOOKMARK_TYPE_SESSION:
                    $item->itemName = !empty($item->session_name) ? $item->session_name : JText::_('LNG_ITEM_REMOVED');
                    $item->link = !empty($item->session_name) ? JBusinessUtil::getConferenceSessionLink($item->session_id, $item->session_alias) : '';
                    $item->item_type =  JText::_('LNG_SESSION');
                    break;
            }
        }

        return $items;
    }

    /**
     * Gets raw bookmark data from the database along with the corresponding company/offer data,
     * arranges and processes them into a CSV string and returns the output.
     *
     * @return string
     *
     * @since 4.9.0
     */
    public function getBookmarksCSV() {
        $jinput    = JFactory::getApplication()->input;
        $delimiter = $jinput->getString("delimiter", ",");

        $table     = JTable::getInstance("Bookmark", "JTable");
        $user      = JBusinessUtil::getUser();
        $bookmarks = $table->getBookmarksForExport($user->ID);

        $bookmarks = $this->prepareBookmarks($bookmarks);

        $csv_output = "type" . $delimiter ."item_name" . $delimiter . "note". $delimiter . "item_url";

        $csv_output .= "\n";

        foreach ($bookmarks as $bookmark) {
            $bookmark->note = str_replace(array("\r\n", "\r", "\n"), "<br />", $bookmark->note);
            $bookmark->note = str_replace('"', '""', $bookmark->note);

            $csv_output .= "\"$bookmark->item_type\"" . $delimiter . "\"$bookmark->itemName\"" . $delimiter . "\"$bookmark->note\"" . $delimiter . "\"$bookmark->link\"";

            $csv_output .= "\n";
        }

        return $csv_output;
    }

    /**
     * Gets the CSV data and prints the output
     *
     * @since 4.9.0
     */
    public function exportBookmarksCSV() {
        $csv_output = $this->getBookmarksCSV();

        $fileName = "jbusinessdirectory_bookmarks";
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header("Content-disposition: filename=" . $fileName . ".csv");

        print $csv_output;
    }

	/**
	 * Gets raw bookmark data from the database along with the corresponding company/offer data,
	 * arranges and processes them into a CSV string and returns the output.
	 *
	 * @return string
	 *
	 * @since 4.9.0
	 */
	public function getListingsBookmarksCSV() {
		$jinput    = JFactory::getApplication()->input;
		$delimiter = $jinput->getString("delimiter", ",");

		$table     = JTable::getInstance("Bookmark", "JTable");
		$user      = JBusinessUtil::getUser();
		$bookmarks = $table->getListingBookmarksForExport($user->ID);

		// retrieve the business custom attributes that will be added in the csv file
		$attributesTable        = JTable::getInstance("Attribute", "JTable");
		$companyAttributesTable = JTable::getInstance("CompanyAttributes", "JTable");
		$attributes             = $attributesTable->getAttributes(ATTRIBUTE_TYPE_BUSINESS);

		$csv_output = "name" . $delimiter . "address" . $delimiter . "city". $delimiter . "county". $delimiter. "contact_number"
			. $delimiter . "hours" . $delimiter . "note";

		// if attribute are present, add them as headers
		if (!empty($attributes)) {
			foreach ($attributes as $attribute) {
				$csv_output = $csv_output . $delimiter . "attribute_" . $attribute->name;
			}
		}

		$csv_output .= "\n";

		foreach ($bookmarks as $bookmark) {
			$bookmark->note = str_replace(array("\r\n", "\r", "\n"), "<br />", $bookmark->note);
			$bookmark->note = str_replace('"', '""', $bookmark->note);

			// parse the opening hours for each business
			$bookmark->hours = '';
			if (!empty($bookmark->start_hours)) {
				$startHours = explode(',', $bookmark->start_hours);
				$endHours   = explode(',', $bookmark->end_hours);
				$weekdays   = explode(',', $bookmark->weekdays);
				asort($weekdays);

				if (!empty($bookmark->weekdays)) {
					// arranges hours in the following format: Monday(9:00AM - 6:00PM);
					foreach ($weekdays as $key => $day) {
						$bookmark->hours .= JBusinessUtil::getWeekdayFromIndex($day) . ' (';
						$bookmark->hours .= JBusinessUtil::getTimeText($startHours[$key]) . ' - ' . JBusinessUtil::getTimeText($endHours[$key]);
						$bookmark->hours .= '); ';
					}
				}
			}

			$csv_output .= "\"$bookmark->item_name\"" . $delimiter . "\"$bookmark->address\"" . $delimiter . "\"$bookmark->city\"" . $delimiter . "\"$bookmark->county\"" . $delimiter. "\"$bookmark->contact_number\""
				. $delimiter . "\"$bookmark->hours\"" . $delimiter . "\"$bookmark->note\"";

			// add the values for the custom attributes if the bookmark is of type business
			if ($bookmark->item_type == BOOKMARK_TYPE_BUSINESS) {
				$companyAttributes = $companyAttributesTable->getCompanyAttributes($bookmark->item_id);
				foreach ($attributes as $attribute) {
					$found = false;
					foreach ($companyAttributes as $key => $companyAttribute) {
						if ($attribute->code == $companyAttribute->code) {
							$attributeValue = AttributeService::getAttributeValues($companyAttribute);
							$csv_output     .= $delimiter . "\"$attributeValue\"";
							$found          = true;
							unset($companyAttributes[$key]);
							break;
						}
					}
					if (!$found) {
						$csv_output .= $delimiter . "\"\"";
					}
				}
			}

			$csv_output .= "\n";
		}

		return $csv_output;
	}

	/**
	 * Gets the CSV data and prints the output
	 *
	 * @since 4.9.0
	 */
	public function exportListingsBookmarksCSV() {
		$csv_output = $this->getListingsBookmarksCSV();

		$fileName = "jbusinessdirectory_bookmarks";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=" . $fileName . ".csv");

		print $csv_output;
	}

	/**
	 * Reorder the wish list.
	 *
	 * @param $newOrder array with the new order
	 * @return bool
	 *
	 * @since 4.9.1
	 */
	public function reOrderList($newOrder) {
		$bookmarksTable = $this->getTable("Bookmark");
		foreach ($newOrder as $key=>$order) {
			if (!$bookmarksTable->updateBookmarkOrder($key, $order)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Generates a PDF with a list of bookmarks for the current user.
	 *
	 * @since 4.9.0
	 */
	public function generateBookmarkListringsPDF() {
		$table     = JTable::getInstance("Bookmark", "JTable");
		$user      = JBusinessUtil::getUser();
		$bookmarks = $table->getListingBookmarksForExport($user->ID);

		// retrieve the business custom attributes that will be added in the csv file
		$attributesTable        = JTable::getInstance("Attribute", "JTable");
		$companyAttributesTable = JTable::getInstance("CompanyAttributes", "JTable");
		$attributes             = $attributesTable->getAttributes(ATTRIBUTE_TYPE_BUSINESS);
		
		
		// create the pdf object
		$pdf = new tFPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial', '', 18);

		// if no bookmarks, display a message and return
		if (empty($bookmarks)) {
			$pdf->Cell(40, 10, JText::_('LNG_NO_BOOKMARKS'), 0);
			$pdf->Output();
			return;
		}

		// add title
		$pdf->Cell(40, 10, JText::_('LNG_BOOKMARKS'), 0);
		$pdf->Ln(20);

		// add all the table rows and data
		$j = 1;

		foreach ($bookmarks as $bookmark) {
			$bookmark->itemType = JText::_('LNG_COMPANY');
			$pdf->SetFont('Arial', '', 12);
			$pdf->Cell(4, 5, $j.".", 0);
			$pdf->SetFont('Arial', '', 13);
			$pdf->Cell(70, 5, $bookmark->item_name, 0);
			$pdf->Ln();
			$pdf->SetFont('Arial', '', 10);
			
			$address = $bookmark->street_number." ".$bookmark->address.", ".$bookmark->city.", ".$bookmark->county;
			$pdf->write(6, $address, 0);
			$pdf->Ln();
			$pdf->Cell(30, 5, $bookmark->contact_number, 0);
			$pdf->Ln();
			
			// add the values for the custom attributes if the bookmark is of type business
			if ($bookmark->item_type == BOOKMARK_TYPE_BUSINESS) {
				$companyAttributes = $companyAttributesTable->getCompanyAttributes($bookmark->item_id);
				foreach ($attributes as $attribute) {
					foreach ($companyAttributes as $key => $companyAttribute) {
						if ($attribute->code == $companyAttribute->code) {
							$attributeValue = AttributeService::getAttributeValues($companyAttribute);
							if (!empty($attributeValue)) {
								$pdf->write(7, $attributes[$key]->name.': ', 0);
								$pdf->write(7, $attributeValue, 0);
								$pdf->Ln();
							}
							unset($companyAttributes[$key]);
							break;
						}
					}
				}
				$pdf->Ln();
			}
			
			// parse the opening hours for each business
			if (!empty($bookmark->start_hours) && $bookmark->start_hours!="#") {
				$pdf->SetFont('Arial', '', 10);
				$startHours = explode(',', $bookmark->start_hours);
				$endHours   = explode(',', $bookmark->end_hours);
				$weekdays   = explode(',', $bookmark->weekdays);

				// arranges hours in the following format: Monday(9:00AM - 6:00PM);
				for ($i=1; $i<=7; $i++) {
					$pdf->Cell(26, 4, JBusinessUtil::getWeekdayFromIndex($i), 0);
				}
			
				$pdf->Ln();

				for ($i = 1; $i <= 7; $i++) {
					$found = false;
					// find the current day on the weekdays array
					foreach ($weekdays as $key=>$day) {
						if ($day == $i) {
							$pdf->SetFont('Arial', '', 8);
							$bookmark->hours = JBusinessUtil::getTimeText($startHours[$key]) . ' - ' . JBusinessUtil::getTimeText($endHours[$key]);
							$pdf->Cell(26, 6, $bookmark->hours, 0);
							$found = true;
							break;
						}
					}

					// if day not found, then it is closed
					if (!$found) {
						$pdf->Cell(26, 6, JText::_('LNG_CLOSED'), 0);
					}
				}

				$pdf->Ln();
			}
			$pdf->Ln();
			
			$j++;
		}
	
		// add the footer
		$pdf->SetY(0);
		$pdf->SetFont('Arial', 'I', 8);
		$pdf->Cell(0, 5, 'Page ' . $pdf->PageNo(), 0, 0, 'C');
		// print output
		$pdf->Output();
	}

    /**
     * Generates a PDF with a list of bookmarks for the current user.
     *
     * @since 4.9.0
     */
    public function generateBookmarkPDF() {
        $table     = JTable::getInstance("Bookmark", "JTable");
        $user      = JBusinessUtil::getUser();
        $bookmarks = $table->getBookmarksForExport($user->ID);

        $bookmarks = $this->prepareBookmarks($bookmarks);

        // create the pdf object
        $pdf = new tFPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 18);

        // if no bookmarks, display a message and return
        if (empty($bookmarks)) {
            $pdf->Cell(40, 10, JText::_('LNG_NO_BOOKMARKS'), 0);
            $pdf->Output();
            return;
        }

        // add title
        $pdf->Cell(40, 10, JText::_('LNG_BOOKMARKS'), 0);
        $pdf->Ln(20);

        // add all the table rows and data
        $j = 1;

        foreach ($bookmarks as $bookmark) {
            $pdf->SetFont('Arial', '', 12);
            if($j>9) {
                $pdf->Cell(7, 5, $j . ".", 0);
            }else{
                $pdf->Cell(4, 5, $j . ".", 0);
            }
            $pdf->SetFont('Arial', '', 13);
            $pdf->Cell(70, 5, $bookmark->itemName . " (".$bookmark->item_type.")", 0);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 10);

            $pdf->write(6, $bookmark->note, 0);
            $pdf->Ln();
            $pdf->Cell(30, 5, $bookmark->link, 0);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $j++;
        }

        // add the footer
        $pdf->SetY(0);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, 'Page ' . $pdf->PageNo(), 0, 0, 'C');
        // print output
        $pdf->Output();
    }
}
