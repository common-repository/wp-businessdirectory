<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

class JTableOfferMessages extends JTable {

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db){

        parent::__construct('#__jbusinessdirectory_offer_messages', 'id', $db);
    }

    function setKey($k){
        $this->_tbl_key = $k;
    }

    function getUserOfferMessages($offerIds, $limitstart=0, $limit=0){
        $db =JFactory::getDBO();
        if(empty($offerIds))
            return array();

        $offerIds = implode(",", $offerIds);

        $query = "select cm.*, cp.subject as offerName
					from
					#__jbusinessdirectory_offer_messages cm
					left join #__jbusinessdirectory_company_offers cp on cp.id = cm.offer_id
					where cm.offer_id in ($offerIds)
					group by cm.id	";

        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getTotalOfferMessagesByUser($offerIds, $limitstart=0, $limit=0){
        $db =JFactory::getDBO();

        if(!empty($offerIds))
            $offerIds = implode(",", $offerIds);
        else
            $offerIds = 0;

        $query = "select cm.*, cp.subject as eventName
					from
					#__jbusinessdirectory_offer_messages cm
					left join #__jbusinessdirectory_company_offers cp on cp.id = cm.offer_id
					where offer_id in ($offerIds)
					group by cm.id	";

        $db->setQuery($query, $limitstart, $limit);
        $db->execute();
        return $db->getNumRows();

    }
}
