<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
require_once JPATH_COMPONENT_SITE.'/helpers/defines.php';
require_once BD_HELPERS_PATH.'/utils.php';

class JFormFieldBusinessCountries extends JFormFieldList {
	protected $type = 'businesscountries';
    protected $layout = 'joomla.form.field.list-fancy-select';

	// getLabel() left out

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$options = array();
		$showAll = !empty($this->element["show_all"])?$this->element["show_all"]:false;

		if(!$showAll){
			$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_COUNTRIES"));
		}	
		// Initialize some field attributes.
		$key = "id";
		$value = "name";
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
		$query = "select distinct c.id, c.country_name FROM #__jbusinessdirectory_countries c
                    inner join #__jbusinessdirectory_companies cp on c.id = cp.countryId
                      where country_name!=''
                    ".(($appSettings->show_secondary_locations)? "
                  UNION
                  select distinct c.id, c.country_name FROM #__jbusinessdirectory_countries c
                    inner join #__jbusinessdirectory_company_locations lo on lo.countryId = c.id
                      where country_name!=''
                    ":"")."
                  order by country_name asc";

		if($showAll){
			$query = "select distinct c.id, c.country_name FROM #__jbusinessdirectory_countries c";
		}

		// Get the database object.
		$db = JFactory::getDBO();
	
		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();
		
		// Build the field options.
		if (!empty($items)) {
			foreach ($items as $item) {
				if ($translate == true) {
					$options[] = JHtml::_('select.option', $item->id, $item->country_name);
				} else {
					$options[] = JHtml::_('select.option', $item->id, $item->country_name);
				}
			}
		}
	
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
	
		return $options;
	}
}
