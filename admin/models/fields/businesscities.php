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

class JFormFieldBusinessCities extends JFormFieldList {
	protected $type = 'businesscities';
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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_CITIES"));
	
		// Initialize some field attributes.
		$key = "id";
		$value = "name";
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
		if($appSettings->limit_cities_regions){
			$query = "select distinct c.name, c.id FROM #__jbusinessdirectory_cities c 
			inner join #__jbusinessdirectory_company_activity_city cac on c.id = cac.city_id
			where c.name!=''
			order by c.name asc";
		} else {
			$query = "SELECT distinct city FROM #__jbusinessdirectory_companies
			where state =1 and city!=''
			".(($appSettings->show_secondary_locations)? "
			UNION
			select distinct city FROM #__jbusinessdirectory_company_locations where city!=''
			":"")."
			order by city asc";
		}
	
		// Get the database object.
		$db = JFactory::getDBO();
	
		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();
	
		// Build the field options.
		if (!empty($items)) {
			if($appSettings->limit_cities_regions){
				foreach ($items as $item) {
					if ($translate == true) {
						$options[] = JHtml::_('select.option', $item->id, $item->name);
					} else {
						$options[] = JHtml::_('select.option', $item->id, $item->name);
					}
				}
			} else {
				foreach ($items as $item) {
					if ($translate == true) {
						$options[] = JHtml::_('select.option', $item->city, $item->city);
					} else {
						$options[] = JHtml::_('select.option', $item->city, $item->city);
					}
				}
			}
		}
	
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
	
		return $options;
	}
}
