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
JHtml::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'ratings.delete' || confirm('<?php echo JText::_("COM_JBUSINESSDIRECTORY_RATING_CONFIRM_DELETE", true);?>')) {
			JBD.submitform(task);
		}
	}
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=ratings');?>" method="post" name="adminForm" id="adminForm">
    	<div id="j-main-container">
    		<div id="filter-bar" class="btn-toolbar">
    			<div class="filter-search btn-group pull-left fltlft">
    				<label class="filter-search-lbl element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
    				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
    			</div>
    			<div class="btn-group pull-left hidden-phone">
    				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
    				<button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
    			</div>
    			<div class="btn-group pull-right hidden-phone">
    				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
    				<?php echo $this->pagination->getLimitBox(); ?>
    			</div>
    		</div>
    	</div>
    	<div class="clr clearfix"></div>
    	<table class="table table-striped adminlist" id="itemList">
    		<thead>
    			<tr>
    				<th width="1%">#</th>
    				<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="JBD.checkAll(this)" /></th>
    				<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'LNG_COMPANY', 'name', $listDirn, $listOrder); ?></th>
    				<th><?php echo JHtml::_('grid.sort', 'LNG_RATING', 'cr.rating', $listDirn, $listOrder); ?></th>
    				<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'LNG_IP_ADDRESS', 'cr.ipAddress', $listDirn, $listOrder); ?></th>
    				<th class="hidden-phone"><?php echo JHtml::_('grid.sort', 'LNG_ID', 'cr.id', $listDirn, $listOrder); ?></th>
    			</tr>
    		</thead>
    		<tfoot>
    			<tr>
    				<td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
    			</tr>
    		</tfoot>
    		<tbody>
    			<?php $nrcrt = 1;
    			foreach($this->items as $rating) { ?>
    				<tr class="row<?php echo $nrcrt%2?>"  >
    					<td align='center'><?php echo $nrcrt++?></td>
    					<td align='center'><?php echo JHtml::_('grid.id', $nrcrt, $rating->id); ?></td>
    					<td align='left'>
    						<a href='<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=rating.edit&id='.$rating->id) ?>' title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
    							<b><?php echo $rating->name ?></b>
    						</a>	
    					</td>
    					<td><?php echo $rating->rating ?></td>
    					<td class="hidden-phone"><?php echo $rating->ipAddress ?></td>
    					<td class="hidden-phone"><?php echo $rating->id ?></td>
    				</tr>
    			<?php } ?>
    		</tbody>
    	</table>
    
    	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
    	<input type="hidden" name="task" value="" /> 
    	<input type="hidden" name="id" value="" />
    	<input type="hidden" name="boxchecked" value="0" />
    	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    	<?php echo JHTML::_('form.token'); ?> 
    </form>
</div>