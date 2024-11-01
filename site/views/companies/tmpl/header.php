<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

$user = JBusinessUtil::getUser();

$company = $this->company;

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

//set metainfo
$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();

$appSettings = JBusinessUtil::getApplicationSettings();

$title = stripslashes($company->name)." - ".$config->sitename;
if(!empty($company->meta_title))
	$title = stripslashes($company->meta_title);

$description = $appSettings->meta_description;
if(!empty($company->short_description)){
	$description = htmlspecialchars(strip_tags($company->short_description), ENT_QUOTES);	
}else if(!empty($company->description)){
	$description = htmlspecialchars(JBusinessUtil::truncate(strip_tags($company->description),150,"..."), ENT_QUOTES);
}
if(!empty($company->meta_description))
	$description = $company->meta_description;

$keywords = $appSettings->meta_keywords;
if(!empty($company->keywords))
	$keywords = $company->keywords;

JBusinessUtil::setMetaData($title, $description, $keywords, false);
JBusinessUtil::setFacebookMetaData($title, $description, $this->company->logoLocation, $url);

$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
$showNotice = ($appSettings->enable_reviews_users && $user->ID ==0)?1:0;

$menuItemId = JBusinessUtil::getActiveMenuItem();

$listingUrl = JBusinessUtil::getCompanyLink($this->company);
JBusinessUtil::setCanonicalURL($listingUrl);

?>