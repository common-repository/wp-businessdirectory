<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
switch ($appSettings->social_profile) {
	case 1:
		require 'profile_easysocial.php';
		break;
	case 2:
		require 'profile_jomsocial.php';
		break;
	case 3:
		require 'profile_cbuilder.php';
		break;
}
