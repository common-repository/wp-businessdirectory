<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanyPictures extends JTable {
	public $id				= null;
	public $companyId		= null;
	public $picture_info	= null;
	public $picture_path	= null;
	public $picture_enable	= null;


	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_pictures', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
}
