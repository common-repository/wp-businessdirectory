<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryModelManageCompanyArticle extends JModelAdmin {
	

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jbusinessdirectory.edit.managecompanyarticle';

	
	public function __construct($config = array()) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		
		parent::__construct($config);
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEdit($record) {
		return true;
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$id = JFactory::getApplication()->input->getInt('id');
		$this->setState('article.id', $id);
	}


	public function getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('article.id');

		if(empty($itemId)){
			$item = new stdClass();
			$item->post_title = "";
			$item->post_content  = "";
			$item->post_type="post";
			$item->post_status="pending";
			$item->ID = 0;
			
			$businessId = JFactory::getSession()->get('business_id');
			$item->company_id = $businessId;
		}else{
			$item = get_post($itemId);
			$table = $this->getTable();
			$item->company_id = $table->getArticleCompanyId($itemId);;
		}
		
		return $item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data) {
		//dump($data);exit;
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('article.id');
		$data['id'] =$id;
		$isNew = $id > 0? false:true;

		$post_information = array(
			'ID'=> $data['id'],
			'post_title' => wp_strip_all_tags( $data['post_title'] ),
			'post_content' => $data['post_content'],
			'post_type' => 'post',
			'post_status' => 'pending'
		);
	 
		$id = wp_insert_post( $post_information );

		if (!is_numeric($id)) {
			JError::raiseWarning('', JText::_($id));
			return false;
		} // something went wrong!!

		$this->setState('article.id', $id);
		$this->saveRelation($id, $data['company_id']);

		return true;
	}

	/**
	 * Save relation between article id and company id
	 *
	 * @param [type] $articleId
	 * @param [type] $businessId
	 * @return void
	 */
	private function saveRelation($articleId, $companyId){
		if (!empty($companyId) && !empty($articleId)) {
			$db = JFactory::getDbo();
			$db->setQuery("INSERT INTO #__jbusinessdirectory_company_articles(article_id, company_id) VALUES ($articleId, $companyId) 
							ON DUPLICATE KEY UPDATE company_id = values(company_id)");
			$db->execute();

			return true;
		}

		return false;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		//exit;
		// The folder and element vars are passed when saving the form.
		if (empty($data)) {
			$item		= $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jbusinessdirectory.company', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form)) {
			return false;
		}
		
		return $form;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'CompanyArticles', $prefix = 'Table', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

}
