
<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

$menuItemId = JBusinessUtil::getActiveMenuItem();
JBusinessUtil::checkPermissions("directory.access.listing.registrations", "managelistingregistrations");

$isProfile = true;
?>
<script>
	var isProfile = true;
</script>
<style>
#header-box, #control-panel-link {
	display: none;
}

.tooltip {
    border-style:none !important;
}

  .tooltip-inner {
        background-color: rgba(0,0,0,0.55);
        max-width:600px;
        padding:2px 2px;
        text-align:center;
        border-radius:4px;
    }
</style>

<?php

if(empty($this->items)){
	echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_LISTING_JOIN_REQUESTS"), JText::_("LNG_LISTING_JOIN_REQUESTS"));
	return;
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managelistingregistrations'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="dir-panel-table responsive-simple">			
			<tbody>
				<?php 
				$nrcrt = 1;
				if(!empty($this->items)){
					foreach($this->items as $item) { ?>
						<tr class="jtable-body-row row<?php echo $nrcrt%2 ?>">
                            <td class="jtable-body-row-data" data-title="<?php echo JText::_('LNG_MAIN_COMPANY'); ?>">
								<div class="item-title">                                        
									<?php echo $item->mainCompanyName?>
								</div>
							</td>
                            <td class="jtable-body-row-data" data-title="<?php echo JText::_('LNG_JOINING_COMPANY'); ?>">
								<div class="item-label">
									<?php echo JText::_('LNG_JOINING_COMPANY'); ?>
								</div>
								<div class="item-value">
									<?php echo $item->joinedCompanyName ?>
								</div>
                            </td>

							<td class="jtable-body-row-data" data-title="<?php echo JText::_('LNG_JOINING_OWNER'); ?>">
								<div class="item-label">
									<?php echo JText::_('LNG_JOINING_OWNER'); ?>
								</div>
								<div class="item-value">
									<?php echo $item->userName?>
								</div>
							</td>

							<td class="jtable-body-row-data" data-title="<?php echo JText::_('LNG_STATUS'); ?>">
								<div class="item-status"> 
									<?php
									switch($item->approved) {
										case LISTING_JOIN_STATUS_APPROVED:
											echo '<span class="status-badge badge-success">'.JText::_("LNG_APPROVED").'</span>';
											break;
										case LISTING_JOIN_STATUS_DISAPPROVED:
											echo '<span class="status-badge badge-danger">'.JText::_("LNG_DISAPPROVED").'</span>';
											break;
									}
									?>
								
									<div class="item-actions">
										<?php if ($item->approved == LISTING_JOIN_STATUS_APPROVED){ ?>
											<a onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managelistingregistrations.disaprove&front=true&cid='. $item->id )?> '"
												title="<?php echo JText::_('LNG_CLICK_TO_CHANGE_STATE'); ?>" class="jtable-btn">
												<i class="la la-ban text-warning"></i>
											</a>
										<?php } else { ?>
											<a onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managelistingregistrations.aprove&front=true&cid='. $item->id )?> '"
												title="<?php echo JText::_('LNG_CLICK_TO_CHANGE_STATE'); ?>" class="jtable-btn">
												<i class="la la-check text-success"></i>
											</a>
										<?php } ?>
									</div>
								</div>
                            </td>
						</tr>
				<?php }
				}
				?>
			</tbody>
		</table>
		<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>
	</div>
	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="id" id="id" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<?php require_once JPATH_COMPONENT_SITE . "/include/status_legend.php" ?>