<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableCompanyPicturesExtra extends JTable {
	public $id				= null;
	public $companyId		= null;
	public $image_info	= null;
	public $image_path	= null;
	public $image_enable	= null;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_pictures_extra', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
}
