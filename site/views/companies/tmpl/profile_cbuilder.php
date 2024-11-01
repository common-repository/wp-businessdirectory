<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

if(JFile::exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')){
	require_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
	
	$userId = $this->company->userId;
	$cbUser    = CBuser::getInstance( $userId );
	$avatarUrl = "";
	if ( $cbUser ) {
		$avatarUrl = $cbUser->getField( 'avatar', null, 'csv', 'none', 'list' );
		$xhtml ='';
		$link = JRoute::_("index.php?option=com_comprofiler&task=userProfile&user=".$userId,-1);
		
	}
	
	$name = $cbUser->getField( 'firstname' )." ".$cbUser->getField( 'lastname' );
?>
<div class="jbd-user-profile jomsocial">
	<div class="jbd-user-image">
		<img src="<?php echo $avatarUrl?> "/>
	</div>
	<div class="jbd-user-info">
		<div class="user-name"><?php echo $name ;?></div>
		<a target="_blank" href="<?php echo $link?>"><?php echo JText::_("LNG_USER_PROFILE")?></a>
		
	</div>
</div>

<?php }else{ ?>
  <div>CB not installed!!</div>
<?php } ?>