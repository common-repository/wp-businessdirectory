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

class JFormFieldSessionLocations extends JFormFieldList {
	protected $type = 'sessionlocations';
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
		$options = array();

		$removeEmptyField = !empty($this->element["remove_empty_option"])?$this->element["remove_empty_option"]:false;

		if(!$removeEmptyField){
			$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_LOCATIONS"));
		}

		$query = 'select id as value, name as text from #__jbusinessdirectory_conference_session_locations order by name';

		// Get the database object.
		$db = JFactory::getDBO();
		$db->setQuery($query);

		$items = $db->loadObjectlist();
		
		// Build the field options.
		if (!empty($items)) {
			foreach ($items as $item) {
				$options[] = JHtml::_('select.option', $item->value, $item->text);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}