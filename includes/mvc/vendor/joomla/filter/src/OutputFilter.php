<?php
/**
 * Part of the Joomla Framework Filter Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace MVC\Filter;

use MVC\Language\Language;
use MVC\String\StringHelper;

/**
 * OutputFilter
 *
 * @since  1.0
 */
class OutputFilter
{
	/**
	 * Makes an object safe to display in forms
	 *
	 * Object parameters that are non-string, array, object or start with underscore
	 * will be converted
	 *
	 * @param   object   &$mixed        An object to be parsed
	 * @param   integer  $quote_style   The optional quote style for the htmlspecialchars function
	 * @param   mixed    $exclude_keys  An optional string single field name or array of field names not to be parsed (eg, for a textarea)
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function objectHtmlSafe(&$mixed, $quote_style = ENT_QUOTES, $exclude_keys = '')
	{
		if (is_object($mixed))
		{
			foreach (get_object_vars($mixed) as $k => $v)
			{
				if (is_array($v) || is_object($v) || $v == null || substr($k, 1, 1) == '_')
				{
					continue;
				}

				if (is_string($exclude_keys) && $k == $exclude_keys)
				{
					continue;
				}
				elseif (is_array($exclude_keys) && in_array($k, $exclude_keys))
				{
					continue;
				}

				$mixed->$k = htmlspecialchars($v, $quote_style, 'UTF-8');
			}
		}
	}

	/**
	 * This method processes a string and replaces all instances of & with &amp; in links only.
	 *
	 * @param   string  $input  String to process
	 *
	 * @return  string  Processed string
	 *
	 * @since   1.0
	 */
	public static function linkXhtmlSafe($input)
	{
		$regex = 'href="([^"]*(&(amp;){0})[^"]*)*?"';

		return preg_replace_callback(
			"#$regex#i",
			function($m)
			{
				return preg_replace('#&(?!amp;)#', '&amp;', $m[0]);
			},
			$input
		);
	}

	/**
	 * This method processes a string and replaces all accented UTF-8 characters by unaccented
	 * ASCII-7 "equivalents", whitespaces are replaced by hyphens and the string is lowercase.
	 *
	 * @param   string  $string    String to process
	 * @param   string  $language  Language to transliterate to
	 *
	 * @return  string  Processed string
	 *
	 * @since   1.0
	 */
	public static function stringUrlSafe($string, $language = '')
	{
		// Remove any '-' from the string since they will be used as concatenaters
		$str = str_replace('-', ' ', $string);

		// Transliterate on the language requested (fallback to current language if not specified)
		$lang = empty($language) ? Language::getInstance() : Language::getInstance($language);
		$str = $lang->transliterate($str);

		// Trim white spaces at beginning and end of alias and make lowercase
		$str = trim(strtolower($str));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

		// Trim dashes at beginning and end of alias
		$str = trim($str, '-');

		return $str;
	}

	/**
	 * This method implements unicode slugs instead of transliteration.
	 *
	 * @param   string  $string  String to process
	 *
	 * @return  string  Processed string
	 *
	 * @since   1.0
	 */
	public static function stringUrlUnicodeSlug($string)
	{
		// Replace double byte whitespaces by single byte (East Asian languages)
		$str = preg_replace('/\xE3\x80\x80/', ' ', $string);

		// Remove any '-' from the string as they will be used as concatenator.
		// Would be great to let the spaces in but only Firefox is friendly with this

		$str = str_replace('-', ' ', $str);

		// Replace forbidden characters by whitespaces
		$str = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $str);

		// Delete all '?'
		$str = str_replace('?', '', $str);

		// Trim white spaces at beginning and end of alias and make lowercase
		$str = trim(StringHelper::strtolower($str));

		// Remove any duplicate whitespace and replace whitespaces by hyphens
		$str = preg_replace('#\x20+#', '-', $str);

		return $str;
	}

	/**
	 * Replaces &amp; with & for XHTML compliance
	 *
	 * @param   string  $text  Text to process
	 *
	 * @return  string  Processed string.
	 *
	 * @since   1.0
	 */
	public static function ampReplace($text)
	{
		return preg_replace('/(?<!&)&(?!&|#|[\w]+;)/', '&amp;', $text);
	}

	/**
	 * Cleans text of all formatting and scripting code
	 *
	 * @param   string  &$text  Text to clean
	 *
	 * @return  string  Cleaned text.
	 *
	 * @since   1.0
	 */
	public static function cleanText(&$text)
	{
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace('/&nbsp;/', ' ', $text);
		$text = preg_replace('/&amp;/', ' ', $text);
		$text = preg_replace('/&quot;/', ' ', $text);
		$text = strip_tags($text);
		$text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

		return $text;
	}

	/**
	 * Strip img-tags from string
	 *
	 * @param   string  $string  Sting to be cleaned.
	 *
	 * @return  string  Cleaned string
	 *
	 * @since   1.0
	 */
	public static function stripImages($string)
	{
		return preg_replace('#(<[/]?img.*>)#U', '', $string);
	}

	/**
	 * Strip iframe-tags from string
	 *
	 * @param   string  $string  Sting to be cleaned.
	 *
	 * @return  string  Cleaned string
	 *
	 * @since   1.0
	 */
	public static function stripIframes($string)
	{
		return preg_replace('#(<[/]?iframe.*>)#U', '', $string);
	}
}
