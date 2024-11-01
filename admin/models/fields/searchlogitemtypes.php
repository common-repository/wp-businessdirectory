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

class JFormFieldSearchLogItemTypes extends JFormFieldList {
	protected $type = 'searchlogitemtypes';


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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_SELECT_TYPE"));

		$items = array(
			SEARCH_LOG_KEYWORD => JText::_('LNG_SEARCH_LOG_KEYWORD'),
			SEARCH_LOG_CATEGORY => JText::_('LNG_SEARCH_LOG_CATEGORY'),
			SEARCH_LOG_TYPE => JText::_('LNG_SEARCH_LOG_TYPE'),
			SEARCH_LOG_LOCATION => JText::_('LNG_SEARCH_LOG_LOCATION'),
			SEARCH_LOG_COUNTRY => JText::_('LNG_SEARCH_LOG_COUNTRY'),
			SEARCH_LOG_PROVINCE => JText::_('LNG_SEARCH_LOG_PROVINCE'),
			SEARCH_LOG_REGION => JText::_('LNG_SEARCH_LOG_REGION'),
			SEARCH_LOG_CITY => JText::_('LNG_SEARCH_LOG_CITY'),
			SEARCH_LOG_CUSTOM_ATTRIBUTE => JText::_('LNG_SEARCH_LOG_CUSTOM_ATTRIBUTE'),
			SEARCH_LOG_MIN_PRICE => JText::_('LNG_SEARCH_LOG_MIN_PRICE'),
			SEARCH_LOG_MAX_PRICE => JText::_('LNG_SEARCH_LOG_MAX_PRICE'),
			SEARCH_LOG_START_DATE => JText::_('LNG_SEARCH_LOG_START_DATE'),
			SEARCH_LOG_END_DATE => JText::_('LNG_SEARCH_LOG_END_DATE')
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
