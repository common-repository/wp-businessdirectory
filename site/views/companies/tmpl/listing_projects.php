<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$appSettings = JBusinessUtil::getApplicationSettings();
if ($appSettings->projects_style == 1) {
    require_once "listing_projects_style_1.php";
} else if ($appSettings->projects_style == 2) {
    require_once "listing_projects_style_2.php";
} else if ($appSettings->projects_style == 3) {
    require_once "listing_projects_style_3.php";
}

?>