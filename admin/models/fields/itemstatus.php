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

class JFormFieldItemStatus extends JFormFieldList {
	protected $type = 'itemstatus';

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
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_TYPES"));

		$options[] = JHtml::_('select.option', 0, JTEXT::_("LNG_NEEDS_CREATION_APPROVAL"));
		$options[] = JHtml::_('select.option', 1, JTEXT::_("LNG_APPROVED"));
		$options[] = JHtml::_('select.option', -1, JTEXT::_("LNG_DISAPPROVED"));

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}