<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldCompanies
 *
 * @since 5.0
 */
class JFormFieldCompanies extends JFormFieldList {
	protected $type = 'companies';
    protected $layout = 'joomla.form.field.list-fancy-select';

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   5.0
	 */
	protected function getOptions() {
		$options   = array();
		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_SELECT_COMPANY"));

		$query = 'SELECT DISTINCT name AS text, id AS value
                    FROM #__jbusinessdirectory_companies
                   ORDER BY name ASC';

		// Get the database object.
		$db = JFactory::getDbo();
		$db->setQuery($query, 0, 15000);
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
