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

class JFormFieldBusinessCategories extends JFormFieldList {
	protected $type = 'businesscategories';
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

		if (!$removeEmptyField) {
			$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_CATEGORIES"));
		}

		$categortType = !empty($this->element["category_type"])?$this->element["category_type"]:1;
			
		// Get the database object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
					->select('a.id AS value, a.name AS text, a.level, a.published')
					->from('#__jbusinessdirectory_categories AS a')
					->join('LEFT', $db->quoteName('#__jbusinessdirectory_categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
					->where('a.published = 1')
					->where('a.id > 1')
					->where('a.type = '.$categortType) // type=1 => Only business categories
					->group('a.id, a.name, a.level, a.lft, a.rgt, a.parent_id, a.published')
					->order('a.lft ASC');
	
		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();

		// Pad the option text with spaces using depth level as a multiplier.
		foreach ($items as $item) {
			if($item->level > 1){
				$item->text = str_repeat('- ', $item->level-1) . $item->text;
			}
		}

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
