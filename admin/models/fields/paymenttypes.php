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
class JFormFieldPaymentTypes extends JFormFieldList {
	protected $type = 'paymenttypes';

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   5.0
	 */
	protected function getOptions() {
		$options = array();

		$options[] = JHtml::_('select.option', "", JTEXT::_("LNG_ALL_TYPES"));

		$options[] = JHtml::_('select.option', PAYMENT_TYPE_PACKAGE, JTEXT::_("LNG_PAYMENT_TYPE_PACKAGE"));
		$options[] = JHtml::_('select.option', PAYMENT_TYPE_SERVICE, JTEXT::_("LNG_PAYMENT_TYPE_SERVICE"));
		$options[] = JHtml::_('select.option', PAYMENT_TYPE_CAMPAIGN, JTEXT::_("LNG_PAYMENT_TYPE_CAMPAIGN"));
		$options[] = JHtml::_('select.option', PAYMENT_TYPE_OFFER, JTEXT::_("LNG_PAYMENT_TYPE_OFFER"));
		$options[] = JHtml::_('select.option', PAYMENT_TYPE_EVENT, JTEXT::_("LNG_PAYMENT_TYPE_EVENT"));

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
