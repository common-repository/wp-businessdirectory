<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableRating extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_ratings', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function getRating($ratingId) {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_company_ratings where id=".$ratingId;
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function saveRating($data) {
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_company_ratings(id,companyId,rating,ipAddress) values ";
		$query = $query."(".$db->escape($data['ratingId']).",".$db->escape($data['companyId']).",".$db->escape($data['rating']).",'".$db->escape($data['ipAddress'])."') ";
		$query = $query." ON DUPLICATE KEY UPDATE rating= values(rating)";
		//dump($query);
		//exit();
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			exit();
			return false;
		}
		return $db->insertid();
	}
	public function getAllRatings() {
		$db = JFactory::getDBO();
		$query = "select cr.*, c.name as name from #__jbusinessdirectory_company_ratings cr
		inner join #__jbusinessdirectory_companies c on cr.companyId = c.id order by cr.id desc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getNumberOfRatings($companyId) {
		$db = JFactory::getDBO();
		$query = "select count(*) as nrRatings from #__jbusinessdirectory_company_ratings where companyId=".$companyId;
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result->nrRatings;
	}

	public function deleteRating($ratingId) {
		$db = JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_company_ratings WHERE id = ".$ratingId ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}
	
	public function updateCompanyRating($companyId) {
		$db =JFactory::getDBO();
		$query = "update #__jbusinessdirectory_companies set averageRating=(select avg(rating) from #__jbusinessdirectory_company_ratings where companyId=".$companyId.") where id=".$companyId;
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		return true;
	}
}
