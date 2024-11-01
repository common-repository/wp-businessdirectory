<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

$isProfile = true;
$showSteps =  JFactory::getApplication()->input->get("showSteps",false);

$menuItemId = JBusinessUtil::getActiveMenuItem();
if ($this->appSettings->allow_user_creation==0 && $this->appSettings->user_login_position == 1) {
	JBusinessUtil::checkPermissions("directory.access.listings", "managecompany");
}

if($this->item->approved == COMPANY_STATUS_CLAIMED){
	$app = JFactory::getApplication();
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'));
}

if($this->item->id == 0 && $this->appSettings->enable_simple_form){
	JBusinessUtil::applySimpleFormConfiguration($this->item->defaultAtrributes);
}

?>
<div class="jbd-container jbd-front-end add-listing">
	<?php
		if($this->item->id == 0 && $this->appSettings->enable_simple_form){
			include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'company'.DS.'tmpl'.DS.'edit_simple.php');
		}else{ 
			if($this->appSettings->edit_form_mode == 3){
				include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'company'.DS.'tmpl'.DS.'edit_sections.php');
			}else{
				include(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'company'.DS.'tmpl'.DS.'edit.php');
			}
		}
	?>
</div>

<script>
	var isProfile = true;
</script>

<style>
#header-box, #control-panel-link{
	display: none;
}
</style>