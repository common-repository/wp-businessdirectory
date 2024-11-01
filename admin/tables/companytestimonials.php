<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableCompanyTestimonials extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_testimonials', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getAllCompanyTestimonials($companyId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_testimonials where companyId=$companyId order by testimonial_title";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function deleteCompanyTestimonials($companyId, $ids) {
		if (!empty($ids)) {
			$query = "delete from #__jbusinessdirectory_company_testimonials where companyId in ($companyId) and id not in ($ids)";
		} else {
			$query = "delete from #__jbusinessdirectory_company_testimonials where companyId in ($companyId)";
		}
		$this->_db->setQuery($query);
		$this->_db->execute();
	}
}
