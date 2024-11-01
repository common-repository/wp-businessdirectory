<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior.
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		JBD.submitform(task, document.getElementById('item-form'));
	}
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
		<div class="clr mandatory oh">
			<p><?php echo JText::_("LNG_REQUIRED_INFO")?></p>
		</div>

		<fieldset class="boxed">

			<h2> <?php echo JText::_('LNG_EDIT_RATING');?></h2>
			<div class="form-box">

				<div class="detail_box">
					<label for="subject"><?php echo JText::_('LNG_COMPANY')?> </label>
					<input type="text"
					       name="control-label" id="company_fld" class="control-label hasTooltip" data-toggle="tooltip" value="<?php echo $this->item->company->name ?>" maxlength="4" disabled>
					<div class="clear"></div>
				</div>

				<div class="control-group">
					<label for="name"><?php echo JText::_('LNG_RATING')?> </label>
					<select id="rating" name="rating" class="inputbox input">
						<?php for($i=0;$i<=5;$i=$i+0.5){?>
							<option value="<?php echo $i?>" <?php echo $i == $this->item->rating ?"selected":""?>><?php echo $i?></option>
						<?php } ?>
					</select>
					<div class="clear"></div>
				</div>

				<div class="detail_box">
					<label for="address"><?php echo JText::_('LNG_IP_ADDRESS')?> </label>
					<input type="text"
					       name="control-label" id="ip_address_flb" class="control-label hasTooltip" data-toggle="tooltip" value="<?php echo $this->item->ipAddress ?>" maxlength="4" disabled>
					<div class="clear"></div>
				</div>

				<div class="detail_box">
					<label for="id"><?php echo JText::_('LNG_ID')?> </label>
					<input type="text"
					       name="control-label" id="id_flb" class="control-label hasTooltip" data-toggle="tooltip" value="<?php echo $this->item->id ?>" maxlength="4" disabled>
					<div class="clear"></div>
				</div>
			</div>
		</fieldset>

		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="companyId" value="<?php echo $this->item->companyId ?>" />
		
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
