<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class TableCompanyArticles extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_articles', 'article_id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getCompanyArticles($companyId) {

		$db =JFactory::getDBO();
		$query = "select *
                  from #__jbusinessdirectory_company_articles ca
                  inner join #__posts ct on ct.id =  ca.article_id
                  where ca.company_id= $companyId and ct.post_status = 'publish'
                  order by ct.post_date desc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Retrieve the article company id
	 *
	 * @return void
	 */
	public function getArticleCompanyId($articleId){
		$db =JFactory::getDBO();
		$query = "select *
                  from #__jbusinessdirectory_company_articles ca
                   where article_id= $articleId 
                  ";
		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->company_id;
	}

}
