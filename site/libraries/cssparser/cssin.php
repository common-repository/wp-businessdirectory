<?php

/*
	Copyright 2013-2014, François-Marie de Jouvencel

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace FM;

require_once dirname(__FILE__).'/csstidy/class.csstidy.php';


/*
* A class to inline CSS.
*
* It honours !important attributes and doesn't choke on complex styles.
*
*
*/

class CSSIN
{

	private $cssFiles = array();
	private $parsed_css = array();

	/*
	* Retrieves a CSS stylesheet and caches it before returning it.
	*/
	public function getCSS($url)
	{

		if(!isset($cssFiles[$url]))
		{
			$cssFiles[$url]	= file_get_contents($url);
		}
		return $cssFiles[$url];
	}

	/*
	* Take a list of absolute URLs pointing to CSS stylesheets,
	* retrieve the CSS, parse it, sort the rules by increasing order of specificity,
	* cache the rules, return them.
	*/
	public function getCSSFromFiles($urls)
	{
		$key = implode('::', $urls);
		if(!isset($this->parsed_css[$key]))
		{
			$texts = array();
			foreach($urls as $url)
			{
				$texts[] = $this->getCSS($url);
			}
			$text = implode("\n\n", $texts);
			$this->parsed_css[$key] = $text;
		}
		return $this->parsed_css[$key];
    }

    public static function splitMediaQueries($css)
    {

		$start = 0;
		$queries = '';

		while (($start = strpos($css, "@media", $start)) !== false)
		{
			// stack to manage brackets
			$s = array();

			// get the first opening bracket
			$i = strpos($css, "{", $start);

			// if $i is false, then there is probably a css syntax error
			if ($i !== false)
			{
				// push bracket onto stack
				array_push($s, $css[$i]);

				// move past first bracket
				$i++;

				while (!empty($s))
				{
					// if the character is an opening bracket, push it onto the stack, otherwise pop the stack
					if ($css[$i] == "{")
					{
						array_push($s, "{");
					}
					elseif ($css[$i] == "}")
					{
						array_pop($s);
					}

					$i++;
				}

				$queries .= substr($css, $start-1, $i+1-$start) . "\n";
				$css = substr($css, 0, $start-1) . substr($css, $i);
				$i = $start;
			}
		}

		return array($css, $queries);
    }

	public function parseCSS($text)
    {
			$css  = new \csstidy();
			$css->parse($text);

			$rules 		= array();
			$position 	= 0;

			foreach($css->css as $declarations)
			{
				foreach($declarations as $selectors => $properties)
				{
					foreach(explode(",", $selectors) as $selector)
					{

						$selector = str_replace(".","",$selector);
						$selector = explode(" ", $selector);
						$selector = end($selector);
						$rules[] = array(
							'position' 		=> $position,
							'specificity' 	=> self::calculateCSSSpecifity($selector),
							'selector' 		=> $selector,
							'properties' 	=> $properties
						);
					}

					$position += 1;
				}
			}

			usort($rules, function($a, $b){
				if($a['specificity'] > $b['specificity'])
				{
					return 1;
				}
				else if($a['specificity'] < $b['specificity'])
				{
					return -1;
				}
				else
				{
					if($a['position'] > $b['position'])
					{
						return 1;
					}
					else
					{
						return -1;
					}
				}
			});

		return $rules;
	}

	/**
	 * The following function fomes from CssToInlineStyles.php - here is the original licence FOR THIS FUNCTION
	 *
	 * CSS to Inline Styles class
	 *
	 * @author		Tijs Verkoyen <php-css-to-inline-styles@verkoyen.eu>
	 * @version		1.2.1
	 * @copyright	Copyright (c), Tijs Verkoyen. All rights reserved.
	 * @license		BSD License
	 */

	public static function calculateCSSSpecifity($selector)
	{
	    // cleanup selector
	    $selector = str_replace(array('>', '+'), array(' > ', ' + '), $selector);

	    // init var
	    $specifity = 0;

	    // split the selector into chunks based on spaces
	    $chunks = explode(' ', $selector);

	    // loop chunks
	    foreach ($chunks as $chunk) {
	        // an ID is important, so give it a high specifity
	        if(strstr($chunk, '#') !== false) $specifity += 100;

	        // classes are more important than a tag, but less important then an ID
	        elseif(strstr($chunk, '.')) $specifity += 10;

	        // anything else isn't that important
	        else $specifity += 1;
	    }

	    // return
	    return $specifity;
	}

	/*
	* Turns a CSS style string (like: "border: 1px solid black; color:red")
	* into an array of properties (like: array("border" => "1px solid black", "color" => "red"))
	*/
	public static function styleToArray($str)
	{
		$array = array();

		if(trim($str) === '')return $array;

		foreach(explode(';', $str) as $kv)
		{
			if ($kv === '')
				continue;

			$key_value = explode(':', $kv);
			$array[trim($key_value[0])] = trim($key_value[1]);
		}

		return $array;
	}

	/*
	* Reverses what styleToArray does, see above.
	* array("border" => "1px solid black", "color" => "red") yields "border: 1px solid black; color:red"
	*/
	public static function arrayToStyle($array)
	{
		$parts = array();
		foreach($array as $k => $v)
		{
			$parts[] = "$k:$v";
		}
		return implode(';', $parts);
	}

	/*
	* Get an absolute URL from an URL ($relative_url, but relative or not actually!)
	* that is found on the page with url $page_url.
	* Determine it as a browser would do. For instance if "<a href='/bob/hello.html'>hi</a>"
	* (here '/bob/hello.html' is the $relative_url)
	* is found on a page at $page_url := "http://example.com/stuff/index.html"
	* then the function returns "http://example.com/bob/hello.html"
	* because that's where you'd go to if you clicked on the link in your browser.
	* This is used to find where to download the CSS files from when inlining.
	*/
	public static function absolutify($page_url, $relative_url)
	{
		$parsed_url = parse_url($page_url);

		$absolute_url 			= '';
		$parsed_relative_url 	= parse_url($relative_url);

		// If $relative_url has a host it is actually absolute, return it.
		if(isset($parsed_relative_url['host']))
		{
			$absolute_url = $relative_url;
		}
		// If $relative_url begins with / then it is a path relative to the $page_url's host
		else if(preg_match('/^\//', $parsed_relative_url['path']))
		{
			$absolute_url = $parsed_url['scheme'].'://'.$parsed_url['host'].$parsed_relative_url['path'];
		}
		// No leading slash: append the path of $relative_url to the 'folder' path of $page_url
		else
		{
			$absolute_url = $parsed_url['scheme'].'://'.$parsed_url['host'].dirname($parsed_url['path']).'/'.$parsed_relative_url['path'];
		}

		return $absolute_url;
	}

	/*
	* The core of the algorithm, takes a URL and returns the HTML found there with the CSS inlined.
	* If you pass $contents then the original HTML is not downloaded and $contents is used instead.
	* $url is mandatory as it is used to resolve the links to the stylesheets found in the HTML.
	*/
	function inlineCSS($contents=null, $urls=null)
	{
		
		$raw_css = '';
		if (!empty($urls)) {
			$raw_css .= $this->getCSSFromFiles($urls);
		}
		
		
		// Get the CSS rules by decreasing order of specificity.
		// This is an array with, amongst other things, the keys 'properties', which hold the CSS properties
		// and the 'selector', which holds the CSS selector
		$rules = $this->parseCSS($raw_css);

		//dump($html);


		// We loop over each rule by increasing order of specificity, find the nodes matching the selector
		// and apply the CSS properties
		foreach ($rules as $rule){
			
				$selector = $rule['selector'];
				if(strpos($contents, "\"$selector\"") !== false){
					$style = 'style="'.self::arrayToStyle($rule['properties']).'"';
					$class = 'class="'.$rule['selector'].'"';
					$contents = str_replace($class, $style, $contents);
				}
		}

		return $contents;
	}
}
