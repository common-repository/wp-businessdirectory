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

class JFormFieldSearchLogObjectTypes extends JFormFieldList {
	protected $type = 'searchlogobjecttypes';


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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_SELECT_OBJECT"));

		$items = array(
			SEARCH_LOG_TYPE_LISTING => JText::_('LNG_SEARCH_LOG_TYPE_LISTING'),
			SEARCH_LOG_TYPE_OFFER => JText::_('LNG_SEARCH_LOG_TYPE_OFFER'),
			SEARCH_LOG_TYPE_EVENT => JText::_('LNG_SEARCH_LOG_TYPE_EVENT')
		);

		// Build the field options.
		foreach ($items as $key => $item) {
			$options[] = JHtml::_('select.option', $key, $item);
		}
	
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
