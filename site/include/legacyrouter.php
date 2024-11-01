<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 *
 * @param
 *            array A named array
 * @return array
 */
class JBusinessDirectoryRouterRulesLegacy implements JComponentRouterRulesInterface {

	/**
	 * Constructor for this legacy router
	 *
	 * @param JComponentRouterView $router
	 *            The router this rule belongs to
	 *
	 * @since 3.6
	 * @deprecated 4.0
	 */
	public function __construct($router) {
		$this->router = $router;
	}

	/**
	 * Preprocess the route for the com_content component
	 *
	 * @param
	 *            array &$query An array of URL arguments
	 *
	 * @return void
	 *
	 * @since 3.6
	 * @deprecated 4.0
	 */
	public function preprocess(&$query) {
	}

	/**
	 * Build the route for the com_content component
	 *
	 * @param
	 *            array &$query An array of URL arguments
	 * @param
	 *            array &$segments The URL arguments to use to assemble the subsequent URL.
	 *
	 * @return void
	 *
	 * @since 3.6
	 * @deprecated 4.0
	 */
	public function build(&$query, &$segments) {
		$segments = array();
		
		if (isset($query['view'])) {
			$segments[] = $query['view'];
			unset($query['view']);
		}
		
		if (isset($query['companyId'])) {
			$segments[] = $query['companyId'];
			unset($query['companyId']);
		}
		
		if (isset($query['categoryId'])) {
			$segments[] = $query['categoryId'];
			unset($query['categoryId']);
		}
		
		if (isset($query['eventId'])) {
			$segments[] = $query['eventId'];
			unset($query['eventId']);
		}
		
		if (isset($query['offerId'])) {
			$segments[] = $query['offerId'];
			unset($query['offerId']);
		}
		
		if (isset($query['cSessionId'])) {
			$segments[] = $query['cSessionId'];
			unset($query['cSessionId']);
		}
		
		if (isset($query['conferenceId'])) {
			$segments[] = $query['conferenceId'];
			unset($query['conferenceId']);
		}
		
		if (isset($query['speakerId'])) {
			$segments[] = $query['speakerId'];
			unset($query['speakerId']);
		}
		// var_dump($segments);
		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param
	 *            array &$segments The segments of the URL to parse.
	 * @param
	 *            array &$vars The URL attributes to be used by the application.
	 *
	 * @return void
	 *
	 * @since 3.6
	 * @deprecated 4.0
	 */
	public function parse(&$segments, &$vars) {
		$vars = array();
		
		// view is always the first element of the array
		$count = count($segments);
		
		// Count route segments
		$count = count($segments);
		
		// Standard routing for articles. If we don't pick up an Itemid then we get the view from the segments
		// the first segment is the view and the last segment is the id of the article or category.
		
		$vars['view'] = $segments[0];
		
		switch ($vars['view']) {
			case "companies":
				$temp = explode(":", $segments[$count - 1]);
				$vars['companyId'] = $temp[0];
				break;
			case "search":
				$temp = explode(":", $segments[$count - 1]);
				if (is_numeric($temp[0])) {
					$vars['categoryId'] = $temp[0];
				}
				break;
			case "event":
				$temp = explode(":", $segments[$count - 1]);
				$vars['eventId'] = $temp[0];
				break;
			case "offer":
				$temp = explode(":", $segments[$count - 1]);
				$vars['offerId'] = $temp[0];
				break;
			case "events":
				$temp = explode(":", $segments[$count - 1]);
				break;
			case "offers":
				$temp = explode(":", $segments[$count - 1]);
				break;
			case "payment":
				$temp = explode(":", $segments[$count - 1]);
				$vars['companyId'] = $temp[0];
				break;
			case "conferences":
				$temp = explode(":", $segments[$count - 1]);
				break;
			case "conference":
				$temp = explode(":", $segments[$count - 1]);
				$vars['conferenceId'] = $temp[0];
				break;
			case "conferencesessions":
				$temp = explode(":", $segments[$count - 1]);
				break;
			case "conferencesession":
				$temp = explode(":", $segments[$count - 1]);
				$vars['cSessionId'] = $temp[0];
				break;
			case "speakers":
				$temp = explode(":", $segments[$count - 1]);
				break;
			case "speaker":
				$temp = explode(":", $segments[$count - 1]);
				$vars['speakerId'] = $temp[0];
				break;
		}
		
		// var_dump($vars);
		
		return $vars;
	}
}
