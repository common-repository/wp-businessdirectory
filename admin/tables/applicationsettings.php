<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableApplicationSettings extends JTable {
	public $applicationsettings_id		= null;
	public $company_name				= null;
	public $company_email				= null;
	public $currency_id				= null;
	public $css_style					= null;
	public $css_module_style			= null;
	public $show_frontend_language		= null;
	public $default_frontend_language	= null;
	public $date_format_id				= null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_application_settings', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function updateOrder($orderId, $orderEmail) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_application_settings SET value = '".$orderId."' where name = 'order_id'";
		$db->setQuery($query);
		$db->execute();
		$query = " UPDATE #__jbusinessdirectory_application_settings SET value = '".$orderEmail."' where name = 'order_email'";
		$db->setQuery($query);
		$db->execute();
	
		return true;
	}

	public function deleteDemoData() {
		$db =JFactory::getDBO();

		$query = 'SELECT DATABASE() as db';
		$db->setQuery($query);
		$tableSchema = $db->loadObject()->db;

		$query = "SELECT DISTINCT concat('TRUNCATE TABLE ', TABLE_SCHEMA,'.',TABLE_NAME, ';') as truncateQuery
					FROM INFORMATION_SCHEMA.TABLES
					WHERE TABLE_NAME LIKE '%jbusinessdirectory%' and 
					      TABLE_NAME NOT LIKE '%application_settings%' and 
					      TABLE_NAME NOT LIKE '%applicationsettings%' and
						  TABLE_NAME NOT LIKE '%jbusinessdirectory_attribute_types%' and 
						  TABLE_NAME NOT LIKE '%jbusinessdirectory_countries%' and 
					      TABLE_NAME NOT LIKE '%date_formats%' and
					      TABLE_NAME NOT LIKE '%currencies%' and
						  TABLE_NAME NOT LIKE '%categories%' and
						  TABLE_NAME NOT LIKE '%jbusinessdirectory_countries%' and
					      TABLE_NAME NOT LIKE '%directory_apps%' and
					      TABLE_NAME NOT LIKE '%jbusinessdirectory_emails%' and
					      TABLE_NAME NOT LIKE '%default_attributes%' and
					      TABLE_NAME NOT LIKE '%jbusinessdirectory_news' and
					      TABLE_SCHEMA LIKE '$tableSchema'
					      ORDER BY TABLE_NAME";
		$db->setQuery($query);
		$tablesToClear = $db->loadObjectList();
		foreach ($tablesToClear as $item) {
			$query = $item->truncateQuery;
			$db->setQuery($query);
			$db->execute();
		}
		return true;
	}

	public function getApplicationSettings() {
		$db    = JFactory::getDbo();
		$query = "SELECT * FROM #__jbusinessdirectory_application_settings";
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	public function updateAppsettings($data) {
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_application_settings(name, value) values ";
		foreach ($data as $key => $item) {
			//this are part of fields that are included during import of other files like it is uploader.php that contains forms for uploads.
			if (in_array($key, array('attachment_name','attachment_status','attachment_path','picture_info','picture_enable','picture_path','notification_title','notification_body','uploadfile',
				'package','sendmail_from','option','task','sendmail_name'))) {
				continue;
			}

			if (strpos($key, "attribute-")!==false) {
				continue;
			}
			
			if (is_numeric($key[0])) {
				continue;
			}
			
			if (is_array($item)) {
				$item = implode(',', $item);
			}

			$item = $db->escape($item);
			$query = $query."('".$key."','".$item."'),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE value=values(value) ";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}
}
