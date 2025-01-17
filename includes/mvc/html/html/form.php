<?php
/**
 * @package     JBD.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use MVC\Utilities\ArrayHelper;

/**
 * Utility class for form elements
 *
 * @since  1.5
 */
abstract class JHtmlForm
{
	/**
	 * Array containing information for loaded files.
	 *
	 * @var    array
	 *
	 * @since  3.8.0
	 */
	protected static $loaded = array();

	/**
	 * Displays a hidden token field to reduce the risk of CSRF exploits
	 *
	 * Use in conjunction with JSession::checkToken()
	 *
	 * @param   array  $attribs  Input element attributes.
	 *
	 * @return  string  A hidden input field with a token
	 *
	 * @see     JSession::checkToken()
	 * @since   1.5
	 */
	public static function token(array $attribs = array())
	{
		$attributes = '';

		if ($attribs !== array())
		{
			$attributes .= ' ' . ArrayHelper::toString($attribs);
		}

		//TODO add form token
		return '<input type="hidden" name="' . JSession::getFormToken() . '" value="1"' . $attributes . ' />';
	}

	/**
	 * Add CSRF form token to Joomla script options that developers can get it by Javascript.
	 *
	 * @param   string  $name  The script option key name.
	 *
	 * @return  void
	 *
	 * @since   3.8.0
	 */
	public static function csrf($name = 'csrf.token')
	{
		if (isset(static::$loaded[__METHOD__][$name]))
		{
			return;
		}

		
		/** @var JDocumentHtml $doc */
		$doc = JFactory::getDocument();

		if (!$doc instanceof JDocumentHtml || $doc->getType() !== 'html')
		{
			return;
		}

		$doc->addScriptOptions($name, JSession::getFormToken());
 
		static::$loaded[__METHOD__][$name] = true;
	}
}
