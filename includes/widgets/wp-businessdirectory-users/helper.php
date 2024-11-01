<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0.en.html; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_login
 *
 * @since  1.5
 */
class ModJBusinessUserHelper {
	/**
	 * Retrieve the URL where the user should be returned after logging in
	 *
	 * @param   \MVC\Registry\Registry  $params  module parameters
	 * @param   string                     $type    return type
	 *
	 * @return string
	 */
	public static function getReturnUrl($params, $type) {
		$app  = JFactory::getApplication();
		
		// Stay on the same page
		global $wp;
		$url =  home_url( $wp->request );

		$url = base64_encode($url);

		return $url;
	}

	/**
	 * Returns the current users type
	 *
	 * @return string
	 */
	public static function getType() {
		$user = JBusinessUtil::getUser();

		return (!$user->get('guest')) ? 'logout' : 'login';
	}

	/**
	 * Get list of available two factor methods
	 *
	 * @return array
	 *
	 * @deprecated  4.0  Use JAuthenticationHelper::getTwoFactorMethods() instead.
	 */
	public static function getTwoFactorMethods() {
		JLog::add(__METHOD__ . ' is deprecated, use JAuthenticationHelper::getTwoFactorMethods() instead.', JLog::WARNING, 'deprecated');

		return JAuthenticationHelper::getTwoFactorMethods();
	}
	
	public static function getCartItemsCount() {
		if(class_exists("OfferSellingService")){
			$items = OfferSellingService::getCartData();
			$result = 0;
			
			if (!empty($items)) {
				$items = $items["items"];
				
				foreach ($items as $item) {
					$result +=$item->quantity;
				}
			}
			
			return $result;
		}

		return 0;

	}
}
