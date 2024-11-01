<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php')) {
	require_once(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php');
	
	$userId = isset($this->company->userId)?$this->company->userId:JBusinessUtil::getUser()->ID;
	$suser = Foundry::user($userId);
	$sconfig = Foundry::config();
	$avatarUrl = $suser->getAvatar('medium');
	$coverUrl = $suser->getCover();
	$name = $suser->name;
	$userPermalink = $suser->getPermalink();
	$options = array(
		'id' => $name
	);
	$link = FRoute::profile($options);
	$link = JBusinessUtil::getWebsiteURL(true) . substr($link, 1); ?>
<div class="jbd-user-profile easysocial">
	<div class="jbd-user-image" style="width: 64px;">
		<div style="background-image: url('<?php echo $coverUrl?>');"></div 
		
		
		<a href="<?php echo $userPermalink?>" target="_blank"><img
			src="<?php echo $avatarUrl?> " /></a>
	</div>
	<div class="jbd-user-info">
		<div>
			<a href="<?php echo $userPermalink?>" target="_blank"
				style="color: #717a8f; font-weight: bold;"><?php echo $name ; ?></a>
		</div>
		<a target="_blank" href="<?php echo $userPermalink?>"><?php echo JText::_("LNG_USER_PROFILE")?></a>
	</div>
</div>
<?php
} else { ?>
<div>EasySocial not installed!!</div>
<?php } ?>