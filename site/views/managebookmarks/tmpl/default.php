<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$menuItemId = JBusinessUtil::getActiveMenuItem();

JBusinessUtil::checkPermissions("directory.access.bookmarks", "managebookmarks");
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');

$filterType = $this->state->get('filter.type');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$usrDashboard = JFactory::getApplication()->input->get("user_dashboard");
$usrDashboardParam = '';
if(isset($usrDashboard) && $usrDashboard != 0) {
	$usrDashboardParam = '&user_dashboard=1';
}
?>

<?php
if(empty($this->items) && empty($filterType)){
    echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_BOOKMARK"),JText::_("LNG_BOOKMARKS"));
    return;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks'.$usrDashboardParam.$menuItemId);?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-3">
            <select name="filter_type" id="filter_type" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('LNG_ALL_BOOKMARKS');?></option>
                <?php echo JHtml::_('select.options', $this->bookmarkTypes, 'id', 'name', $filterType);?>
            </select>
        </div>
        <div class="col-md-9">
		
            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managebookmarks.exportListingsBookmarks&'.JSession::getFormToken().'=1') ?>"
               title="<?php echo JText::_('LNG_CLICK_TO_EXPORT'); ?>" class="export_csv btn btn-sm btn-primary btn-panel right" style="margin:5px;">
                <?php echo JText::_("LNG_EXPORT_LISTINGS")?>
            </a>

            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managebookmarks.exportBookmarks&'.JSession::getFormToken().'=1') ?>"
               title="<?php echo JText::_('LNG_CLICK_TO_EXPORT'); ?>" class="export_csv btn btn-sm btn-primary btn-panel right" style="margin:5px;">
                <?php echo JText::_("LNG_EXPORT")?>
            </a>
			
			<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks&task=managebookmarks.generateBookmarkListringsPDF') ?>" target="_blank" class="export_pdf btn btn-sm btn-success btn-panel right"
			title="<?php echo JText::_('LNG_CLICK_TO_GENERATE_PDF'); ?>" style="margin:5px;">
				<?php echo JText::_("LNG_SAVE_AS_PDF_LISTINGS")?>
			</a>

			<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks&task=managebookmarks.generateBookmarkPDF') ?>" target="_blank" class="export_pdf btn btn-sm btn-success btn-panel right"
			title="<?php echo JText::_('LNG_CLICK_TO_GENERATE_PDF'); ?>" style="margin:5px;">
				<?php echo JText::_("LNG_SAVE_AS_PDF")?>
			</a>			

        </div>
    </div>

	<table class="dir-panel-table responsive-simple" id="itemList">
		<thead>
			<tr>
                <th width="1%" class="hidden-xs hidden-phone" align="left">#</th>                
                <th align="left" width=""><?php echo JText::_("LNG_BOOKMARK")?></th>
				<th align="left" width="10%"><?php echo JText::_("LNG_TYPE")?></th>
				<th align="left" width="35%"><?php echo JText::_("LNG_NOTE")?></th>
				<th></th>
			</tr>
		</thead>
		<tbody class="" id="tableBody">
			<?php foreach( $this->items as $bookmark){?>
               <tr id="<?php echo $bookmark->bookmarkId ?>" class="jtable-body-row orderedRow">
                    <td class="hidden-xs hidden-phone jtable-body-row-data" style="cursor: move;">
                        <span class="sortable-handler" title="<?php echo JText::_('LNG_REORDER_BOOKMARKS'); ?>">
							<i class="icon-menu"></i>
                        </span>
                    </td>					
					<td class="jtable-body-row-data" align="left">
						<div class="item-name text-left">
							<div class="item-title">
								<a target="_blank" href="<?php echo $bookmark->link?>">
									<?php echo $bookmark->itemName ?>
								</a>
							</div>							
						</div>
					</td>
                    <td class="jtable-body-row-data" data-title="<?php echo JText::_('LNG_TYPE'); ?>" class="has-title">
                        <?php  echo $bookmark->item_type ?>
                    </td>
					<td class="jtable-body-row-data" data-title="<?php echo JText::_('LNG_NOTE'); ?>" class="has-title">
						<span><?php echo $bookmark->note ?></span>
					</td>
					<td class="jtable-body-row-data">
						<div class="item-actions">
							<a href="javascript:deleteBookmark(<?php echo $bookmark->bookmarkId ?>)"
								title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="jtable-btn">
								<i class="la la-trash"></i>
							</a>
						</div>
                    </td>
				</tr>
			<?php }	?>
		</tbody>
	</table>
	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
		<?php echo $this->pagination->getListFooter(); ?>
		<div class="clear"></div>
	</div>

	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="bookmarkId" value="" />
	<input type="hidden" id="cid" name="cid" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form>

<script>
	function deleteBookmark(id) {
		if(confirm("<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>")){
			jQuery("#cid").val(id);
			jQuery("#adminForm #task").val("managebookmarks.delete");
			jQuery("#adminForm").submit();
		}
	}
</script>