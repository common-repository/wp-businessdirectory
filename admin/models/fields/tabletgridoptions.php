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

class JFormFieldTabletGridOptions extends JFormFieldList {
	protected $type = 'tabletgridoptions';
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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_SELECT_SIZE"));

		$items = array();
		$items[] = 'col-md-1';
		$items[] = 'col-md-2';
		$items[] = 'col-md-3';
		$items[] = 'col-md-4';
		$items[] = 'col-md-5';
		$items[] = 'col-md-6';
		$items[] = 'col-md-7';
		$items[] = 'col-md-8';
		$items[] = 'col-md-9';
		$items[] = 'col-md-10';
		$items[] = 'col-md-11';
		$items[] = 'col-md-12';

		// Build the field options.
		foreach ($items as $item) {
			$options[] = JHtml::_('select.option', $item, JText::_($item));
		}
	
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
