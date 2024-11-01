<?php
/**
 * @package     JBD.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_SITE.'/helpers/defines.php';
require_once BD_HELPERS_PATH.'/utils.php';

/**
 * Content Component Association Helper
 *
 * @since  3.0
 */
abstract class JBusinessDirectoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id      Id of the item
	 * @param   string   $view    Name of the view
	 * @param   string   $layout  View layout
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */
    public static function getAssociations($id = 0, $view = null, $layout = null)
    {
        $jinput    = JFactory::getApplication()->input;
        $view      = $view === null ? $jinput->get('view') : $view;
        $component = $jinput->getCmd('option');
        $id        = empty($id) ? $jinput->getInt('id') : $id;

        if ($layout === null && $jinput->get('view') == $view && $component == 'com_jbusinessdirectory')
        {
            $layout = $jinput->get('layout', '', 'string');
        }

        if ($view === 'companies')
        {
            if ($id)
            {
                $associations = array();
                $languages = JBusinessUtil::getLanguages();
                $cTag = JBusinessUtil::getCurrentLanguageCode();
                $link = JBusinessUtil::getCompanyDefaultLink($id);
                foreach ( $languages as $language => $tag)
                {
                    $lang = explode("-", $tag);
                    $associations[$tag] = str_replace("/$cTag/","/$lang[0]/",$link);
                }
                return $associations;
            }
        }

        if ($view === 'event'){
            $id = $jinput->getInt('eventId');
            if ($id)
            {
                $associations = array();
                $languages = JBusinessUtil::getLanguages();
                $cTag = JBusinessUtil::getCurrentLanguageCode();
                $event = JBusinessUtil::getEvent($id);
                $link = JBusinessUtil::getEventLink($event->id, $event->alias);
                foreach ( $languages as $language => $tag)
                {
                    $lang = explode("-", $tag);
                    $associations[$tag] = str_replace("/$cTag/","/$lang[0]/",$link);
                }
                return $associations;
            }

        }

        if ($view === 'offer'){
            $id = $jinput->getInt('offerId');
            if ($id)
            {
                $associations = array();
                $languages = JBusinessUtil::getLanguages();
                $cTag = JBusinessUtil::getCurrentLanguageCode();
                $offer = JBusinessUtil::getOffer($id);
                $link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
                foreach ( $languages as $language => $tag)
                {
                    $lang = explode("-", $tag);
                    $associations[$tag] = str_replace("/$cTag/","/$lang[0]/",$link);
                }
                return $associations;
            }

        }

        return array();
    }
}
