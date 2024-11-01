<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
?>
<style>
.jbd-container.jbd-edit-container label {
	margin-left: 1.45px;
}
</style>
<fieldset class="acyheaderarea">
	<div class="toolbar" id="toolbar" style="float:right;">
		<table>
			<tr>
				<td>
					<button class="btn btn-small btn-success" id="languageSaveButton" onclick="JBD.submitbutton('language.create');" title="<?php echo JText::_('LNG_NEW_LANGUAGE',true); ?>">
						<span class="icon-apply icon-white"></span>
						<?php echo JText::_('LNG_NEW',true); ?>
					</button>
					<button class="btn btn-danger btn-small" id="languageSaveButton" onclick="JBD.submitbutton('language.remove');" title="<?php echo JText::_('LNG_DELETE_LANGUAGES',true); ?>">
						<span class="icon-cancel"></span>
						<?php echo JText::_('LNG_DELETE',true); ?>
					</button>
				</td>
			</tr>
		</table>
	</div>
</fieldset>
<div id="jbd-container" class="jbd-container">
	<fieldset class='adminform'>
		<legend><?php echo JText::_('LNG_LANGUAGES',true) ?></legend>
		<table class="jtable" id="itemList">
			<thead class="jtable-head">
				<tr class="jtable-head-row">
					<th width="1%" class="jtable-head-row-data hidden-phone">
						<div class="d-flex justify-content-center align-items-center">
							<div class="jradio">
								<input id="jradio-2" type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="JBD.checkAll(this)" />
								<label for="jradio-2"></label>
							</div>
						</div>
					</th>
					<th width="1%" class="jtable-head-row-data-title">#</th>
					<th width="5%" class=""><?php echo JText::_('LNG_EDIT',true); ?></th>
					<th  width=""  class="text-left"><?php echo JText::_('LNG_NAME',true); ?></th>
					<th width="5%" class="hidden-phone text-left pl-2"><?php echo JText::_('LNG_CODE',true); ?></th>
				</tr>
			</thead>
			<tbody class="jtable-body">
				<?php
				$k = 0;
				for($i = 0,$a = count($this->languages);$i<$a;$i++) {
					$row = $this->languages[$i]; ?>
					<tr class="<?php echo "row$k"; ?> jtable-body-row">
						<td class="hidden-phone jtable-body-row-data text-center">
							<?php echo JHtml::_('jbdgrid.id', $i, $row->language); ?>
						</td>
						<td  class="jtable-body-row-data text-center"><?php echo $i + 1; ?></td>

						<td  class="jtable-body-row-data text-center">
						<a class="jtable-btn ml-3" title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true) ?>" href=<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&tmpl=component&view=language&task=language.editLanguage&code=".$row->language,false); ?>>
								<i class="la la-pencil"></i>
							</a>
						</td>
						<td  class="jtable-body-row-data text-left">
							<a class="" title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true) ?>" href=<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&tmpl=component&view=language&task=language.editLanguage&code=".$row->language,false); ?>>
								<?php echo $row->name; ?></a>
						</td>
						<td class="jtable-body-row-data text-left"><?php echo $row->language; ?></td>
					</tr>
					<?php
					$k = 1 - $k;
				} ?>
			</tbody>
		</table>
	</fieldset>
</div>
