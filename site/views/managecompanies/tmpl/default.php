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
JBusinessUtil::checkPermissions("directory.access.listings", "managecompanies");

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidator');

$user = JBusinessUtil::getUser();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$newListingAction = $this->appSettings->enable_packages?'packages.displayPackages':'managecompany.add';

?>

<style>
    .tooltip.in{
        opacity: 1;
    }
    .tooltip {
        border-style:none !important;
        opacity: 1;
    }

    .tooltip-inner {
        background: #888;
        max-width:600px;
        padding:2px 2px;
        text-align:center;
        border-radius:4px;
        box-shadow: 2px 3px 6px -3px rgba(0, 0, 0, 0.35);
    }
</style>

<?php
if(empty($this->items)){
    $actionURL = JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.add');
    echo JBusinessUtil::getNewItemMessageBlock(JText::_("LNG_LISTING"),JText::_("LNG_LISTINGS"),$actionURL);
    return;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">

    <div class="button-row justify-content-end">
        <?php
            if($this->appSettings->max_business > $this->total || empty($this->appSettings->max_business)) { ?>
                <button type="submit" class="btn btn-success button-add" onclick="JBD.submitform('<?php echo $newListingAction ?>')">
                    <i class="la la-plus-sign"></i> <?php echo JText::_("LNG_ADD_NEW")?>
                </button>
            <?php } else {
            //JFactory::getApplication()->enqueueMessage(JText::_('LNG_MAX_BUSINESS_LISTINGS_REACHED'),"notice");
            } 
        ?>
    </div>

	<div class="dir-table dir-panel-table responsive-simple" id="itemList">
		<div class="dir-table-body">
			<?php
			$nrcrt = 1;
			$i=0;
			foreach( $this->items as $company) { ?>
                <div class="dir-table-row jtable-body-row row<?php echo $i % 2; ?>">
                    <div class="row align-items-center">
                        <div class="col-lg-4 dir-table-cell jtable-body-row-data">
                            <div class="item-head">
                                <div class="item-image text-center">
                                    <?php 
                                        if (!empty($company->logoLocation)) { ?>
                                            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id ) ?>">
                                                <img src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" 
                                                    class=""/>
                                            </a>
                                    <?php } else { ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id ) ?>">
                                            <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" 
                                                class=""/>
                                        </a>
                                    <?php } ?>
                                </div>

                                <div class="item-name text-left">
                                    <div class="item-title">
                                        <?php if($company->approved != COMPANY_STATUS_CLAIMED) { ?>
                                            <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id )?>'
                                                title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> 
                                                <strong><?php echo $company->name ?></strong>
                                            </a>
                                        <?php } else { ?>
                                            <strong><?php echo $company->name ?></strong>
                                        <?php } ?>  
                                        <div class="item-alias">
                                            <?php echo $company->alias ?>
                                            <?php if($user->ID != $company->userId){?>
                                                <label class="badge badge-warning"><?php echo JText::_("LNG_EDITOR")?></label>
                                            <?php } ?>
                                        </div>                                      
                                    </div>
                                    
                                    <div>
                                        <?php if (count($company->checklist) > 0) { ?>
                                            <div id="<?php echo $company->id ?>"
                                                rel="tooltip" data-toggle="tooltip"
                                                data-trigger="click" data-placement="left" data-html="true" data-title=
                                                "
                                                <div>
                                                    <table class='checklist'>
                                                        <tbody>
                                                        <?php foreach ($company->checklist as $key => $val) { ?>
                                                            <tr>
                                                                <td >
                                                                    <?php echo $val->name ?>
                                                                </div>
                                                                <td class='status <?php echo $val->status ? 'status_done' : ''; ?>'>
                                                                    <i class='la la-<?php echo $val->status ? 'check' : 'exclamation'; ?>'></i>
                                                                </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            ">
                                                <div class="item-label"><?php echo JText::_("LNG_COMPLETED") ?></div>
                                                <div class="progress ">
                                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $company->progress * 100 ?>%" aria-valuenow="<?php echo $company->progress * 100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        <?php }else{ ?>
                                            <span><?php echo $company->alias ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 dir-table-cell">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="cnt-hr">
                                        <div class="item-label"><?php echo JText::_("LNG_WEBSITE_CLICKS") ?></div>
                                        <div class="item-value"><?php echo intval($company->websiteCounts) ?></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="cnt-hr">
                                        <div class="item-label"><?php echo JText::_("LNG_VIEW_NUMBER") ?></div>
                                        <div class="item-value"><?php echo intval($company->viewCount) ?></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="cnt-hr">
                                        <div class="item-label"><?php echo JText::_("LNG_CONTACT_NUMBER") ?></div>
                                        <div class="item-value"><?php echo intval($company->contactCount) ?></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="cnt-hr">
                                        <div class="item-label"><?php echo JText::_("LNG_BOOKMARKS") ?></div>
                                        <div class="item-value"><?php echo intval($company->nr_bookmarks) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4 dir-table-cell jtable-body-row-data center" nowrap="nowrap">
                            <div class="item-status"> 
                                <div>
                                    <?php
                                        if (($company->state == 1) && ($company->approved == COMPANY_STATUS_APPROVED)) {
                                            if (!$company->active)
                                                echo '<span class="status-badge badge-warning">' . JText::_("LNG_EXPIRED") . '</span>';
                                            else
                                                echo '<span class="status-badge badge-success">' . JText::_("LNG_PUBLISHED") . '</span>';
                                        }
                                        else {
                                            switch ($company->approved) {
                                                case COMPANY_STATUS_DISAPPROVED:
                                                    echo '<span class="status-badge badge-danger">' . JText::_("LNG_DISAPPROVED") . '</span>';
                                                    break;
                                                case COMPANY_STATUS_CLAIMED:
                                                    echo '<span class="status-badge badge-primary">' . JText::_("LNG_CLAIM_PENDING") . '</span>';
                                                    break;
                                                case COMPANY_STATUS_CREATED:
                                                    echo '<span class="status-badge badge-info">' . JText::_("LNG_PENDING") . '</span>';
                                                    break;
                                                case COMPANY_STATUS_APPROVED:
                                                    echo '<span class="status-badge badge-warning">' . JText::_("LNG_UNPUBLISHED") . '</span>';
                                                    break;
                                            }
                                        } 
                                    ?>
                                </div>
                        
                                <div class="item-actions">
                                    <?php if($company->approved != COMPANY_STATUS_CLAIMED) { ?>
                                        <?php if($company->approved == COMPANY_STATUS_APPROVED) { ?>
                                            <a onclick="document.location.href = '<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.changeState&id='. $company->id )?> '"
                                                title="<?php echo JText::_('LNG_CLICK_TO_CHANGE_STATE'); ?>" class="jtable-btn">
                                                <i class="<?php echo $company->state==0?"la la-check text-success":"la la-ban text-warning"?>"></i>
                                            </a>
                                        <?php } ?>

                                        <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $company->id )?>"
                                            title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>" class="jtable-btn">
                                            <i class="la la-pencil"></i>
                                        </a>

                                        <a target="_blank" href="<?php echo JBusinessUtil::getCompanyLink($company) ?>" 
                                            title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="jtable-btn"> 
                                            <i class="la la-eye"></i>
                                        </a>

                                        <a href="javascript:jbdListings.deleteDirListing(<?php echo $company->id ?>)"
                                            title="<?php echo JText::_('LNG_CLICK_TO_DELETE'); ?>" class="jtable-btn">
                                            <i class="la la-trash"></i>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($this->appSettings->enable_packages){?>
                        <div class="row">
                            <div class="col-lg-12 dir-table-cell jtable-body-row-data">
                                <?php if(!empty($company->packgeInfo)){?>
                                    <div class="listing-package-info row">
                                        <?php $showExtend = true;?>
                                        <div class="col-lg-8">
                                            <div class="item-label"><?php echo JText::_("LNG_CURRENT_PACKAGE") ?></div>
                                            <div class="package-info">
                                                <div class="row">
                                                    <div class="col-lg">
                                                        <div class="d-flex justify-content-between">
                                                            <?php $package = $company->currentPackage; ?>
                                                            <strong class="package-name"><?php echo $package->name ?></strong>
                                                            <?php  if (!(($package->expiration_type == 3 || $package->expiration_type == 4) && $package->state == 1 && $company->subscription->processor_type == "paypalsubscriptions")) {  ?>
                                                                <a class="btn btn-light text-info btn-sm flex-end" href="javascript:upgradePackage(<?php echo $company->id ?>)">
                                                                    <?php echo JText::_("LNG_CHANGE")?>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="package-info-box">
                                                            <div>
                                                                <?php echo JText::_("LNG_STATUS")?>: 
                                                                <?php if ($package->active==1){?>
                                                                    <?php if ($package->price == 0) {?>
                                                                        <?php echo JText::_("LNG_ACTIVE") ?>
                                                                    <?php }else if ($package->price!=0 && $package->state==1){ ?>
                                                                        <?php echo JText::_("LNG_ACTIVE") ?>
                                                                    <?php }else{ ?>
                                                                        <?php echo JText::_("LNG_NOT_STARTED") ?>
                                                                    <?php } ?>
                                                                <?php }else if($package->active==0){
                                                                    if(!$package->future){
                                                                            echo JText::_("LNG_EXPIRED");
                                                                        }else {
                                                                            echo JText::_("LNG_NOT_STARTED");
                                                                            $showExtend = false;
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                            <div>
                                                                <?php echo JText::_("LNG_PAYMENT_STATUS")?>: 
                                                                <?php echo $package->price == 0?JText::_("LNG_FREE"):"" ?>
                                                                <?php echo $package->price!=0 && $package->state==1?JText::_("LNG_PAID"):"" ?>
                                                                <?php if ($package->price!=0 && $package->state==='0') {?>
                                                                    <?php echo JText::_("LNG_NOT_PAID") ?>
                                                                <?php } ?>
                                                            </div>

                                                            <?php if ($package->price != 0 ){ ?>
                                                                <div class="pt-2"><?php echo JText::_("LNG_START_DATE").": ". JBusinessUtil::getDateGeneralShortFormat($package->start_date) ?></div>
                                                            <?php }?>
                                                            <?php if($package->expiration_type==2 && $package->price!=0){ ?>
                                                                <div><?php echo JText::_("LNG_EXPIRATION_DATE").": ". $package->expirationDate ?></div>
                                                            <?php }?>

                                                            <?php if($package->expiration_type>3 && $package->price!=0){ ?>
                                                                <div><?php echo JText::_("LNG_RENEW_DATE").": ". $package->expirationDate ?></div>
                                                            <?php }?>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                        if (($package->price != 0 || $package->expiration_type>2)
                                                            || ($package->state == 1 && $package->active==0 && !$package->future  && $package->expiration_type == 2 )
                                                        ) {
                                                        ?>
                                                            <div class="col-md-5">
                                                                <div class="listing-payment">
                                                                    <div class="text-right">
                                                                        <?php if (($package->price != 0 || $package->expiration_type==4) && $package->state ==='0') {?>
                                                                            <?php if ($package->expiration_type==4) {?>
                                                                                <div class="order-info">
                                                                                    <div class="order-label">
                                                                                        <?php echo JText::_("LNG_SUBSCRIBE") ?>
                                                                                    </div>
                                                                                    <div class="order-amount smaller">
                                                                                        <?php echo JBusinessUtil::getTrialText($package) ?>
                                                                                    </div>
                                                                                </div>
                                                                                <a class="btn btn-info btn-sm px-4 mt-3" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.$menuItemId.'&orderId='.$package->order_id.'&companyId='.$package->company_id) ?>"><i class="la la-credit-card"></i> <?php echo JBusinessUtil::getTrialText($package,false) ?></a>

                                                                            <?php }else{ ?>        
                                                                                <div class="order-info">
                                                                                    <div class="order-label">
                                                                                        <?php echo JText::_("LNG_AMOUNT_DUE") ?>
                                                                                    </div>
                                                                                    <div class="order-amount">
                                                                                        <?php echo JBusinessUtil::getPriceFormat($package->amount) ?>
                                                                                    </div>
                                                                                </div>
                                                                            
                                                                                <a class="btn btn-info btn-sm px-4 mt-3" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=billingdetails.checkBillingDetails'.$menuItemId.'&orderId='.$package->order_id.'&companyId='.$package->company_id) ?>"><i class="la la-credit-card"></i> <?php echo JText::_("LNG_PAY") ?></a>
                                                                            <?php } ?>
                                                                        <?php } ?>

                                                                        <?php if($package->state == 1 && $package->active==0 && !$package->future  && $package->expiration_type == 2 ) { ?>
                                                                            <div class="order-info">
                                                                                <div class="order-label">
                                                                                    <?php echo JText::_("LNG_RENEW_PRICE") ?>
                                                                                </div>
                                                                                <div class="order-amount">
                                                                                    <?php
                                                                                        if (!empty(floatval($package->renewal_price))){
                                                                                            echo JBusinessUtil::getPriceFormat($package->renewal_price);
                                                                                        }else{
                                                                                            echo JBusinessUtil::getPriceFormat($package->amount);
                                                                                        }
                                                                                 ?>
                                                                                </div>
                                                                            </div>

                                                                            <a class="btn btn-info btn-sm px-4 mt-3" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=managecompanies.extendPeriod&id='.$company->id.'&extend_package_id='.$package->package_id); ?>">
                                                                                <i class="la la-credit-card"></i> <?php echo JText::_("LNG_RENEW")?>
                                                                            </a>
                                                                        <?php } ?>

                                                                        <?php if (($package->expiration_type == 3 || $package->expiration_type == 4) && $package->state == 1){?>
                                                                            <?php echo JText::_("LNG_SUBSCRIPTION_STATUS") ?>: <?php echo SubscriptionService::getStatusText($company->subscription); ?>
                                                                            <?php echo SubscriptionService::getSubscriptionButton($company->subscription, 'managecompanies'); ?>
                                                                        <?php } ?>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if(isset($company->lastPaidPackage)) { ?>
                                            <div class="col-lg-4">
                                                <div class="item-label"><?php echo JText::_("LNG_LAST_PAID_PACKAGE") ?></div>
                                                <div class="package-info">
                                                    <?php $package = $company->lastPaidPackage ?>
                                                    <div class="py-2"><strong class="package-name"><?php echo $package->name ?></strong></div>
                                                    <div>
                                                        <?php echo JText::_("LNG_STATUS")?>: 
                                                        <?php echo $package->active==1?JText::_("LNG_ACTIVE"):"" ?>
                                                        <?php if($package->active==0){
                                                                if(!$package->future){
                                                                        echo JText::_("LNG_EXPIRED"); 
                                                                    }else {
                                                                        echo JText::_("LNG_NOT_STARTED");
                                                                        $showExtend = false;
                                                                    }
                                                                }
                                                        ?>
                                                    </div>
                                                    <div>
                                                        <?php echo JText::_("LNG_PAYMENT_STATUS")?>: 
                                                        <?php echo $package->price==0?JText::_("LNG_FREE"):"" ?>
                                                        <?php echo $package->price!=0 && $package->state==1?JText::_("LNG_PAID"):"" ?>
                                                        <?php if ($package->price!=0 && $package->state==='0') {?>
                                                            <?php echo JText::_("LNG_NOT_PAID") ?> 
                                                        <?php }?>
                                                    </div>

                                                    <div class="pt-2"><?php echo JText::_("LNG_START_DATE").": ". JBusinessUtil::getDateGeneralShortFormat($package->start_date) ?></div>
                                                    <?php if($package->expiration_type==2 && $package->price!=0){ ?>
                                                        <div><?php echo JText::_("LNG_EXPIRATION_DATE").": ". $package->expirationDate ?></div>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>      
                                <?php }?>
                            </div>
                        </div>    
                    <?php } ?>
                </div>
			<?php $i++; } ?>
		</div>		
    </div>

	<div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
		<?php echo $this->pagination->getListFooter(); ?>
		<div class="clear"></div>
	</div>

	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" id="task" value="" /> 
	<input type="hidden" name="companyId" value="" />
	<input type="hidden" id="cid" name="cid" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_('form.token'); ?> 
</form>

<?php require_once JPATH_COMPONENT_SITE . "/include/status_legend.php" ?>

<!-- Modal -->
<div id="upgrade-package-modal" class="jbd-container" style="display: none">    
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_CHANGE_PACKAGE'); ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close"></i></a>
        </div>
        <div class="jmodal-body">
            <div class="dialogContent">
                <div class="upgrade-package-details">               
                    <div class="body" id="upgrade-package-step" style="display:none;">
                        <div class="package-list">

                        </div>
                        <div class="d-flex justify-content-between footer">
                            <div class="">
                            </div>
                            <div>
                                <a href="javascript:void(0);" onclick="changePackage()" class="btn btn-info" id="change-action-btn"><?php echo JText::_('LNG_CHANGE') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="package-item-template" style="display:none;">
    <div class="item">
        <div class="main">
            <span style="display:none;" class="package-id">{package_id}</span>
            <input type="radio" name="package-radio" class="package-radio"><span class="name">{package_name}</span>
        </div>
        <div class="details">
            {package_details}
        </div>
    </div>
</div>

<script>
	window.addEventListener('load', function() {
        jQuery('[rel="tooltip"]').tooltip();
	});

    jQuery(".button-add").click(function() {
        jQuery(this).addClass("loader")
    })

	function hideTooltip() {
        jQuery('[rel="tooltip"]').tooltip('hide');
    }

    var selectedPackageName = null;
    var selectedCompanyId = null;

    function upgradePackage(companyId) {

        jQuery('#upgrade-package-modal .dialogContent .body').hide();
        jQuery('#upgrade-package-step').find('.package-list').empty();
        jQuery('#upgrade-package-step').show();
        jQuery('#change-action-btn').hide();
        jQuery('#upgrade-package-step').append('<span id="package-loading">LOADING...</span>');
        jQuery('#upgrade-package-modal').jbdModal();

        let getPackageUrl = jbdUtils.getAjaxUrl('getActivePackageAjax', 'managecompanies', 'managecompanies');
        jQuery.ajax({
            url: getPackageUrl,
            data: {companyId: companyId},
            dataType: 'json',
            type: 'GET',
            cache:false,
            success: function (data) {
                if (data != null) {
                    selectedPackageName = data.name;
                }
                
                selectedCompanyId = companyId;
                listPackages();
                
                jQuery('#upgrade-package-step #package-loading').text('ERROR');
            }
        });
    }

    function listPackages() {

        let getPackageListUrl = jbdUtils.getAjaxUrl('getPackageListAjax', 'managecompanies', 'managecompanies');
        jQuery.ajax({
            url: getPackageListUrl,
            dataType: 'json',
            type: 'GET',
            cache:false,
            success: function (data) {
                if (data != null) {
                    jQuery('#upgrade-package-step #package-loading').remove();
                    jQuery('#change-action-btn').show();

                    var list = '';
                    for (var i = 0; i < data.length; i++) {
                        var tmpl = jQuery('#package-item-template').html();

                        var item = data[i];
                        var details = item.price_text + " | " + item.details_text;

                        tmpl = tmpl.replaceAll('{package_name}', item.name);
                        tmpl = tmpl.replaceAll('{package_id}', item.id);
                        tmpl = tmpl.replaceAll('{package_details}', details);

                        list += tmpl;
                    }
                    jQuery('#upgrade-package-step').find('.package-list').append(list);

                    jQuery('#upgrade-package-step .package-list .item').each(function () {
                        var tmpName = jQuery(this).find('.main').find('.name').text();

                        if (tmpName === selectedPackageName) {
                            jQuery(this).find('.package-radio').prop("checked", true);
                            return;
                        }
                    });
                } else {
                    jQuery('#upgrade-package-step #package-loading').text('ERROR');
                }
            }
        });
    }

    function changePackage() {
        var selectedRadio = jQuery('#upgrade-package-step .package-list .item .main input[name=package-radio]:checked');

        var pckgName = selectedRadio.parent('.main').find('.name').text();
        var pckgId = selectedRadio.parent('.main').find('.package-id').text();

        if (pckgName == selectedPackageName) {
            alert(JBD.JText._('LNG_SELECT_OTHER_PACKAGE'));
            return;
        }

        let changePackageUrl = jbdUtils.getAjaxUrl('changePackageAjax', 'managecompanies', 'managecompanies');
        jQuery.ajax({
            url: changePackageUrl,
            data: {companyId: selectedCompanyId, packageId: pckgId},
            dataType: 'json',
            type: 'GET',
            cache:false,
            success: function (data) {
                jQuery.jbdModal.close();
                selectedCompanyId = null;
                selectedPackageName = null;
                document.location.href = '<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=billingoverview'); ?>';
            }
        });
    }
</script>