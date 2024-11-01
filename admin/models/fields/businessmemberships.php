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

class JFormFieldBusinessMemberships extends JFormFieldList {
	protected $type = 'businessmemberships';
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
		$options[] = JHtml::_('select.option', "", JText::_("LNG_ALL_MEMBERSHIPS"));
	
		// Initialize some field attributes.
		$key = "id";
		$value = "name";
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
		$query = 'SELECT distinct name as text, id as value 
                    FROM #__jbusinessdirectory_memberships
                   where name!="" order by id asc';
	
	
		// Get the database object.
		$db = JFactory::getDBO();
		$db->setQuery($query);
		
		$items = $db->loadObjectlist();

		// Build the field options.
		if (!empty($items)) {
			foreach ($items as $item) {
				$options[] = JHtml::_('select.option', $item->value, JText::_($item->text));
			}
		}
	
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
	
		return $options;
	}
}
