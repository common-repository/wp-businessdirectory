
<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::checkPermissions("directory.access.orders", "managesubscriptions");
$isProfile = true;

?>
<script>
	var isProfile = true;
</script>
<style>
#header-box, #control-panel-link {
	display: none;
}
</style>

<?php
if(empty($this->items)){
    echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_SUBSCRIPTION"),JText::_("LNG_SUBSCRIPTIONS"));
    return;
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managesubscriptions');?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="dir-panel-table responsive-simple">
			<!-- <thead>
				<tr>
					<th align="center" width='25%'><?php echo JText::_('LNG_COUPON'); ?></th>
					<th class="hidden-xs hidden-phone" width="20%" align="center"><?php echo JText::_('LNG_OFFER'); ?></th>
					<th class="hidden-xs hidden-phone" width="15%" align="center"><?php echo JText::_('LNG_COMPANY'); ?></th>
					<th class="hidden-xs hidden-phone" width="20%" align="center"><?php echo JText::_('LNG_GENERATED_TIME'); ?></th>
					<th class="hidden-xs hidden-phone" width="15%" align="center"><?php echo JText::_('LNG_EXPIRATION_TIME'); ?></th>
					<th width='10%'><?php echo JText::_("LNG_PDF"); ?></th>
				</tr>
			</thead> -->
			<tbody>
				<?php 
				$nrcrt = 1;
				if(!empty($this->items)){
					foreach($this->items as $item) { ?>
						<tr class="jtable-body-row row<?php echo $nrcrt%2 ?>">
							<td class="jtable-body-row-data" align="left" nowrap="nowrap">
								<div class="item-name">
									<div class="item-title">
										<?php echo strtoupper($item->code); ?>
									</div>								
								</div>								
							</td>
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_SUBSCRIPTION_ID") ?></div>
								<div class="item-value"><?php echo $item->subscription_id; ?></div>
                        	</td>	
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_COMPANY") ?></div>
								<div class="item-value"><?php echo $item->company_name; ?></div>
                        	</td>
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_CREATED") ?></div>
								<div class="item-value"><?php echo JBusinessUtil::getDateGeneralShortFormat($item->created) ?></div>
                        	</td>	
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_START_DATE") ?></div>
								<div class="item-value"><?php echo JBusinessUtil::getDateGeneralShortFormat($item->start_date) ?></div>
                        	</td>	
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_END_DATE") ?></div>
								<div class="item-value"><?php echo JBusinessUtil::getDateGeneralShortFormat($item->end_date) ?></div>
                        	</td>	
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_AMOUNT") ?></div>
								<div class="item-value"><?php echo $item->amount ?></div>
                        	</td>	
							<td class="jtable-body-row-data">
								<div class="item-label"><?php echo JText::_("LNG_STATE") ?></div>
								<?php
								switch ($item->status) {
									case SUBSCRIPTION_STATUS_ACTIVE:
										echo '<div class="status-badge badge-success">'.JText::_("LNG_ACTIVE").'</div>';
										break;
									case SUBSCRIPTION_STATUS_CANCELED:
										echo '<div class="status-badge badge-warning">'.JText::_("LNG_CANCELED").'</div>';
										break;
									case SUBSCRIPTION_STATUS_INACTIVE:
										echo '<div class="status-badge badge-danger">'.JText::_("LNG_INACTIVE").'</div>';
										break;
								} ?>
                        	</td>	
							
							<td class="jtable-body-row-data">
								<?php echo SubscriptionService::getSubscriptionButton($item, 'managesubscriptions', true); ?>
							</td>
						</tr>
				<?php } 
					}
				?>
			</tbody>
		</table>
        <?php
        if(!empty($this->items)){ ?>
        <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
            <?php echo $this->pagination->getListFooter(); ?>
            <div class="clear"></div>
        </div>
        <?php } ?>
	</div>
	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="id" id="id" value="" />
	<input type="hidden" name="Itemid" id="Itemid" />
	<?php echo JHTML::_('form.token'); ?> 
</form>
<div class="clear"></div>