<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$app = JFactory::getApplication();
$user = JBusinessUtil::getUser();

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$menuId="";
if(isset($activeMenu)){
    $menuId = $activeMenu->id;
}

if($user->ID == 0){    
	$app->redirect(JBusinessUtil::getLoginUrl(null, false));
}

if(!empty($this->item->id) && $user->ID!=$this->item->user_id){
	$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=userdetails'));
}
JBusinessUtil::loadJQueryChosen();
JBusinessUtil::loadMapScripts();
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task)
	{	
		JBD.submitform(task, document.getElementById('item-form'));
    }
});
</script>

<div class="jbd-container jbd-edit-container">
    <?php if (!empty($this->orderId)) { ?>	
        <?php echo JBusinessUtil::renderProcessSteps(4)?>
    <?php } ?>

    <fieldset class="boxed">
        <div class="page-header">
            <h1> <?php echo JText::_('LNG_BILLING_DETAILS');?></h2>
            <p><?php echo JText::_('LNG_BILLING_DETAILS_TXT');?></p>
        </div>
        
        <div class="<?php echo !empty($this->order) ? 'row' : '' ?>">
            <div class="<?php echo !empty($this->order) ? 'col-lg-7  order-2 order-lg-1' : '' ?>">
                <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id."&Itemid=".$menuId); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
                    
                    <?php require_once JPATH_COMPONENT_SITE.'/include/billing_details_fields.php'; ?>
        
                    <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
                    <input type="hidden" name="task" id="task" value="billingdetails.save" />
                    <input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
                    <input type="hidden" name="user_id" value="<?php echo $user->ID ?>" />
                    <input type="hidden" name="orderId" id="orderId" value="<?php echo $this->orderId ?>" />
                    <?php echo JHTML::_( 'form.token' ); ?>
        
                    
                    <div class="row">
                            <div class="col-12">
                                <div class="">
                                    <?php if (empty($this->orderId)) { ?>
                                        <div class="button-row">
                                            <button type="button" class="btn btn-success jbd-commit " onClick="jbdUtils.saveForm()">
                                                <i class="la la-edit"></i> <?php echo JText::_("LNG_SAVE")?>
                                            </button>
                                            <button type="button" class="btn btn-dark button-cancel" onClick="JBD.submitbutton('billingdetails.cancel')">
                                                <i class="la la-close"></i> <?php echo JText::_("LNG_CANCEL")?>
                                            </button>
                                        </div>
                                    <?php } else { ?>
                                        <div class="button-row">
                                            <button type="button" class="btn btn-success jbd-commit"  onClick="jbdUtils.saveForm()">
                                                <?php echo JText::_("LNG_CONTINUE")?>  <i class="la la-angle-double-right"></i>
                                            </button>
                                        </div>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php if (!empty($this->order)) { ?>
                <div class="col-lg-5 order-1 order-lg-2">
                    <div class="jitem-card card-plain card-round horizontal h-auto">
                        <?php echo OrderService::getOrderSummary($this->order); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </fieldset>
</div>

<?php if (!empty($this->userCreated)) { ?>
    <div class="jbd-container" id="user-success-modal" style="display: none">
        <div class="jmodal-sm">
            <div class="jmodal-header">
            <i class="successful-icon la la-check-circle d-flex m-auto" style="justify-content:center"></i>
                <p class="jmodal-header-title text-center"><?php echo JText::_('LNG_LISTING_COMPLETED_SUCCESSFULLY') ?></p>
            </div>
            <div class="jmodal-body">
                <div class="successful-text text-center"><?php echo JText::_('LNG_USER_REGISTERED_MESSAGE'); ?></div>
                <br/>
                <div class="successful-text text-center"><?php echo JText::_('LNG_ORDER_USER_REGISTERED_DIALOG'); ?></div>
            </div>
            <div class="jmodal-footer">
				<div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CLOSE")?></button>
				</div>
			</div>
        </div>
    </div>
<?php } ?>

<script>
    window.addEventListener('load', function() {
        jQuery(".chosen-select").chosen({width: "95%", disable_search_threshold: 5, search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
        jbdUtils.initializeAdminAutocomplete(true);

        jQuery(".button-cancel").click(function() {
            jQuery(this).addClass("loader")
        })

        jQuery('#user-success-modal').jbdModal();
    });
</script>