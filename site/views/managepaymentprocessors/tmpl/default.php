
<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

JBusinessUtil::checkPermissions("directory.access.payment.config", "managepaymentprocessors");
$isProfile = true;
$filterCompany = $this->state->get('filter.company_id');
?>
<script>
    var isProfile = true;
</script>
<style>
	#header-box, #control-panel-link {
		display: none;
	}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managepaymentprocessors');?>" method="post" name="adminForm" id="adminForm">
	<div id="editcell">

		<?php
		if (empty($this->items) && empty($filterCompany)) {
		$actionURL = JRoute::_('index.php?option=com_jbusinessdirectory&task=managepaymentprocessor.add');
		echo JBusinessUtil::getNewItemMessageBlock(JText::_("LNG_PAYMENT_PROCESSOR"), JText::_("LNG_PAYMENT_PROCESSORS"), $actionURL);
		?>
	</div>
	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="id" id="id" value="" />
	<input type="hidden" name="Itemid" id="Itemid" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<?php
return;
}
?>


<div class="row align-items-center">
	<div class="col-md-4">
		<p><strong><?php echo JText::_('LNG_SELECT_COMPANY')?></strong></p>
		<select name="filter_company_id" id="filter_company_id" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_COMPANY');?></option>
			<?php echo JHtml::_('select.options', $this->companies, 'id', 'name', $filterCompany);?>
		</select>
	</div>
	<div class="col-md-8">
		<div class="button-row  justify-content-end">
			<button type="submit" class="btn btn-success" onclick="jbdUtils.addPaymentProcessor()">
				<span class="ui-button-text"><i class="la la-plus-sign"></i> <?php echo JText::_("LNG_ADD_NEW_PAYMENT_PROCESSOR")?></span>
			</button>
		</div>
	</div>
</div>

<?php if (empty($this->items)) { ?>
    <div style="margin: 20px 0;" class="alert alert-warning">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    </div>
<?php } else { ?>
    <table class="dir-panel-table responsive-simple">        
        <tbody>
        <?php
        $nrcrt = 1;
        if(!empty($this->items)){
            foreach($this->items as $item) { ?>
                <tr class="jtable-body-row row<?php echo $nrcrt%2 ?>">
                    <td class="jtable-body-row-data"  align="left" nowrap="nowrap">
                        <div class="item-name text-left">
                            <div class="item-title">
                                <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managepaymentprocessor.edit&'.JSession::getFormToken().'=1&id='. $item->id) ?>'>
                                    <strong><?php echo $item->name; ?></strong>
                                </a>
                            </div>                            
                        </div>
                    </td>
                    <td class="jtable-body-row-data">
                        <div class="item-label"><?php echo JText::_("LNG_COMPANY") ?></div>
						<div class="item-value"><?php echo $item->companyName ?></div>	
                    </td>
                    <td class="jtable-body-row-data" valign="top" align="center">
                        <div class="item-status"> 
                            <?php
                            switch($item->status) {
                                case 1:
                                    echo '<span class="status-badge badge-success">'.JText::_("LNG_PUBLISHED").'</span>';
                                    break;
                                case 0:
                                    echo '<span class="status-badge badge-warning warn">'.JText::_("LNG_UNPUBLISHED").'</span>';
                                    break;
                            }
                            ?>
                                            
                            <div class="item-actions">
                                <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managepaymentprocessor.edit&'.JSession::getFormToken().'=1&id='. $item->id)?>'
                                title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>" class="jtable-btn">
                                <i class="la la-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" onclick="jbdUtils.deletePaymentProcessor(<?php echo $item->id ?>)"
                                title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="jtable-btn">
                                <i class="la la-trash"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php }
        }
        ?>
        </tbody>
    </table>
<?php } ?>
<div class="pagination" <?php echo $this->pagination->total == 0 ? 'style="display:none"':''?>>
	<?php echo $this->pagination->getListFooter(); ?>
	<div class="clear"></div>
</div>
</div>
<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="Itemid" id="Itemid" />
<?php echo JHTML::_('form.token'); ?>
</form>
<div class="clear"></div>
<?php require_once JPATH_COMPONENT_SITE . "/include/status_legend.php" ?>