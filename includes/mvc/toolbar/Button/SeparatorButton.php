<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MVC\Toolbar\Button;

defined('JPATH_PLATFORM') or die;

use MVC\Layout\FileLayout;
use MVC\Toolbar\ToolbarButton;

/**
 * Renders a button separator
 *
 * @since  3.0
 */
class SeparatorButton extends ToolbarButton
{
	/**
	 * Button type
	 *
	 * @var   string
	 */
	protected $_name = 'Separator';

	/**
	 * Get the HTML for a separator in the toolbar
	 *
	 * @param   array  &$definition  Class name and custom width
	 *
	 * @return  string  The HTML for the separator
	 *
	 * @see     ToolbarButton::render()
	 * @since   3.0
	 */
	public function render(&$definition)
	{
		// Store all data to the options array for use with JLayout
		$options = array();

		// Separator class name
		$options['class'] = empty($definition[1]) ? '' : $definition[1];

		// Custom width
		$options['style'] = empty($definition[2]) ? '' : ' style="width:' . (int) $definition[2] . 'px;"';

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new FileLayout('joomla.toolbar.separator');

		return $layout->render($options);
	}

	/**
	 * Empty implementation (not required for separator)
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function fetchButton()
	{
	}
}
