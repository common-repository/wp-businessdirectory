<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
window.addEventListener('load', function(){
	JBD.submitbutton = function(task) {
		jQuery(".btn").each(function(){
			jQuery(this).prop("disabled",true);
		});
		JBD.submitform(task, document.getElementById('adminForm'));
		if (task == 'language.cancel'){
			parent.jQuery.jbdModal.close();
			
		}
	}
});
</script>
<style>
	.language-content-header{
		align-items: center;
		flex-wrap: wrap;
		width: 95%;
		display: flex;
		justify-content: space-between;
	}

	.language-content-button{
		margin-right: 25%;
		margin-bottom: 8px;
		border: 1px solid #ced4da;
	}

	.language-content-button .la {
        color: #5d656b;
        font-size: 180%; 
	}
</style>

<div id="language-content">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post" name="adminForm"  id="adminForm" autocomplete="off">
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('LNG_FILE',true).' : '.$this->file->name;?></legend>
			<div class="language-content-header">
				<p><?php echo JText::_('LNG_ADD_LANGUAGE_OVERRIDE_TEXT');?></p>
				<button type="button" class="language-content-button" onclick="editMainLanguage()" title="<?php echo JText::_('LNG_EDIT_MAIN_LANGUAGE'); ?>"><i class="la la-pencil"></i></button>
			</div>
			<textarea rows="30" name="content" id="main-language-editor" style="width:100%;max-width:95%;" readonly><?php echo $this->file->content;?></textarea>
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

<!-- Modal -->
<div id="language_edit_warning" class="jbd-container" style="display: none">    
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_MESSAGE') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
			<span><?php echo JText::_('LNG_EDIT_MAIN_LANGUAGE_MESSAGE') ?></span>
		</div>           
    </div>
</div>

<script>
	function editMainLanguage() {
		jQuery('#language_edit_warning').jbdModal();
		jQuery('#main-language-editor').removeAttr('readonly');
	}
</script>
