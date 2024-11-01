<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.modal');
?>
<div id="language-content">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post" name="adminForm"  id="adminForm" autocomplete="off">

		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_FILE',true).' : '.$this->file->name;?></legend>
			<textarea rows="30" name="content" id="translation" style="width:100%;max-width:95%;"><?php echo $this->file->content;?></textarea>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_FILE_CUSTOM',true); ?></legend>
			<textarea rows="18" name="custom_content" id="translation" style="width:100%;max-width:95%;"><?php echo $this->file->custom_content;?></textarea>
		</fieldset>

		<div class="clr"></div>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName(); ?>" />
		<input type="hidden" id="task" name="task" value=""/>
		<input type="hidden" id="view" name="view" value="language"/>
		<input type="hidden" id="code" name="code" value="<?php echo $this->file->name?>"/>
		<input type="hidden" name="ctrl" value="file" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
<script language="javascript" type="text/javascript">
	jQuery(document).ready(function () {
		var iframeButton = jQuery("#sbox-window #sbox-content>iframe").find("#languageSaveButton");

		console.log(iframeButton);
		jQuery(iframeButton).mouseup(function () {
			jQuery('#sbox-overlay').remove();
			jQuery('#sbox-window').remove();
		});
	});
</script>