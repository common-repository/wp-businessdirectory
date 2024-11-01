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

class JFormFieldCustomAttributes extends JFormFieldList {
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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_SELECT_ATTRIBUTE"));

		$attributeType = !empty($this->element["attributetype"])?$this->element["attributetype"]:1;
		$attributeSearch = !empty($this->element["attributeSearch"])?$this->element["attributeSearch"]:0;


		$typeCond = "";
		if ($attributeSearch) {
			// ids of attribute types header and link
			$typeCond = " and type not in(5,7)";
		}

		$query = " SELECT id,name FROM #__jbusinessdirectory_attributes where status = 1 
                        and attribute_type=$attributeType $typeCond order by name asc";
	
		// Get the database object.
		$db = JFactory::getDBO();
	
		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();

		$attributeType = !empty($this->element["attributetype"]) ? $this->element["attributetype"] : 1;

		$query = " SELECT id,name FROM #__jbusinessdirectory_attributes where status = 1 and attribute_type=$attributeType order by name asc";

		// Get the database object.
		$db = JFactory::getDBO();

		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist();

		// Build the field options.
		if (!empty($items)) {
			foreach ($items as $item) {
				$options[] = JHtml::_('select.option', $item->id, $item->name);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
