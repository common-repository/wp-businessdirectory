<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');

class JBusinessDirectoryPagination extends JPagination {

	/**
	 * Creates a dropdown box for selecting how many records to show per page.
	 *
	 * @return  string  The HTML for the limit # input box.
	 *
	 * @since   1.5
	 */
	public function getLimitBox() {
		$limits = array();
		
		// Make the option list.
		for ($i = 5; $i <= 30; $i += 5) {
			$limits[] = \JHtml::_('select.option', "$i");
		}
		
		$limits[] = \JHtml::_('select.option', '50', \JText::_('J50'));
		$limits[] = \JHtml::_('select.option', '100', \JText::_('J100'));
		
		$selected = $this->viewall ? 0 : $this->limit;
		
	  
		$html = \JHtml::_(
			'select.genericlist',
			$limits,
			$this->prefix . 'limit',
			'class="inputbox input-mini" size="1" onchange="this.form.submit()"',
			'value',
			'text',
			$selected
		);
	
		return $html;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 *
	 * @return  mixed    The value of the property.
	 *
	 * @since   3.0
	 * @deprecated  4.0  Access the properties directly.
	 */
	public function get($property, $default = null)
	{
		\JLog::add('Pagination::get() is deprecated. Access the properties directly.', \JLog::WARNING, 'deprecated');

		if (strpos($property, '.'))
		{
			$prop     = explode('.', $property);
			$prop[1]  = ucfirst($prop[1]);
			$property = implode($prop);
		}

		if (isset($this->$property))
		{
			return $this->$property;
		}

		return $default;
	}

	public function getResultsCounter()
	{
		$html = null;
		$fromResult = $this->limitstart + 1;

		// If the limit is reached before the end of the list.
		if ($this->limitstart + $this->limit < $this->total)
		{
			$toResult = $this->limitstart + $this->limit;
		}
		else
		{
			$toResult = $this->total;
		}

		// If there are results found.
		if ($this->total > 0)
		{
			$fromResult = "<span class='pag-from-result'>$fromResult</span>";
			$toResult   = "<span class='pag-to-result'>$toResult</span>";
			$total 		= "<span class='pag-total-result'>$this->total</span>";

			$msg = JText::sprintf('JLIB_HTML_RESULTS_OF', $fromResult, $toResult, $total);
			$html .= "\n" . $msg;
		}
		else
		{
			$html .= "\n" . JText::_('JLIB_HTML_NO_RECORDS_FOUND');
		}

		return $html;
	}
}
