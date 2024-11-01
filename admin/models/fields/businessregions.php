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

class JFormFieldBusinessRegions extends JFormFieldList {
	protected $type = 'businessregions';
    protected $layout = 'joomla.form.field.list-fancy-select';

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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_REGIONS"));

		// Initialize some field attributes.
		$key = "id";
		$value = "name";
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
		if($appSettings->limit_cities_regions){
			$query = "select distinct r.name, r.id FROM #__jbusinessdirectory_regions r
			inner join #__jbusinessdirectory_company_activity_region car on r.id = car.region_id
			where r.name!=''
			order by r.name asc";
		} else {
			$query = "select distinct county FROM #__jbusinessdirectory_companies where state=1 and county!=''
			".(($appSettings->show_secondary_locations)? "
			UNION
			select distinct county FROM #__jbusinessdirectory_company_locations where county!=''
			":"")."
			order by county asc";
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
						$options[] = JHtml::_('select.option', $item->county, $item->county);
					} else {
						$options[] = JHtml::_('select.option', $item->county, $item->county);
					}
				}
			}
		}
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
