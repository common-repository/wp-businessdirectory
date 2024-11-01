<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_community/libraries/core.php')) {
	include_once JPATH_ROOT.'/components/com_community/libraries/core.php';
	include_once JPATH_ROOT.'/components/com_community/libraries/messaging.php';
	// Add a onclick action to any link to send a message
	// Here, we assume $usrid contain the id of the user we want to send message to

	$userId = isset($this->company->userId)?$this->company->userId:JBusinessUtil::getUser()->ID;
	$cuser = CFactory::getUser($userId);
	$avatarUrl = $cuser->getThumbAvatar();
	//$avatarUrl = $user->getAvatar();
	$coverUrl = $cuser->getCover();
	$name = $cuser->getDisplayName();
	$onclick = CMessaging::getPopup($userId);
	$addbuddy = "joms.api.friendAdd($userId)";
	$link = CRoute::_('index.php?option=com_community&view=profile&userid='.$userId); ?>
	<div class="jbd-user-profile jomsocial">
		<div class="joms-hcard__cover" style="height: 150px;">
			<img src="<?php echo $coverUrl; ?>" alt="<?php echo $this->escape($name); ?>">
	             <div class="joms-hcard__info">
	                    <div class="joms-avatar">
	                     <a style="color: #fff;" title="<?php echo $this->escape($name); ?>" target="_blank" href="<?php echo $link?>"><img src="<?php echo $this->escape($avatarUrl); ?>" alt="<?php echo $this->escape($name); ?>"></a></h3>
	                    </div>
	                    <div class="joms-hcard__info-content">
	                        <h3 class="reset-gap"><a style="color: #fff;" title="<?php echo $this->escape($name); ?>" target="_blank" href="<?php echo $link?>"><?php echo $this->escape($name); ?></a></h3>
	                        
	                  	  <div class="jbd-user-info">		
				          	  <a class="hidden-sm hidden-xs btn" href="#" onclick="<?php echo $addbuddy?>"><?php echo JText::_("COM_COMMUNITY_FRIENDS_ADD_BUTTON")?></a>				
			                    <a style="color: #fff;float: right;" title="<?php echo $this->escape($name); ?>" target="_blank" href="<?php echo $link?>"><?php echo JText::_("LNG_USER_PROFILE")?></a>
				            </div>
	                    </div>
	             </div>
		</div>
		<div class="jbd-user-info" style="margin-top:5px;">
			<a class="btn" style="width: 100%;"  href="#" onclick="<?php echo $onclick?>"><?php echo JText::_("LNG_SEND_MESSAGE")?></a>
		</div>
	</div>
<?php
} else { ?>
  <div>Jomsocial not installed!!</div>
<?php } ?>