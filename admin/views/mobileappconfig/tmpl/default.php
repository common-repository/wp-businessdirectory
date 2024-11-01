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

;
JHtml::_('behavior.multiselect');
JBusinessUtil::initializeChosen();

use MVC\Factory;
use MVC\HTML\HTMLHelper;
use MVC\Language\Multilanguage;
use MVC\Language\Text;
use MVC\Layout\LayoutHelper;
use MVC\Router\Route;
use MVC\Session\Session;
?>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=mobileappconfig');?>" method="post" name="adminForm" id="adminForm">
        <div class="clr clearfix"></div>

        <fieldset class="boxed">
    		 <div class="row">
    		 	<div class="col-md-7">
                    <fieldset class="boxed">
                        <h3><?php echo JText::_('LNG_MOBILE_APP_CONFIG'); ?></h3>
                        <p><?php echo JText::_('LNG_MOBILE_APP_CONFIG_DESC'); ?></p>
                            <button type="submit" class="btn btn-info mr-2" onclick="configApp()">
                                    <span class="ui-button-text">
                                        <i class="la la-cog mr-1"></i><?php echo JText::_('LNG_CONFIGURE_APP'); ?>
                                    </span>
                            </button>

                    </fieldset>
                    <fieldset class="boxed">
                        <h3><?php echo JText::_('LNG_ANDROID_ORDER_DETAILS',true); ?></h3>
                        <div class="form-group">
                            <label for="name"><?php echo JText::_('LNG_ANDROID_ORDER_ID')?> </label>
                            <?php echo !empty($this->item->androidOrderId) ? $this->item->androidOrderId : '<b><span class="text-danger">'.JText::_('LNG_ORDER_NOTICE')."</span></b>"?>
                        </div>
                        <div class="form-group">
                            <label for="name"><?php echo JText::_('LNG_ANDROID_ORDER_EMAIL')?> </label>
                            <?php echo !empty($this->item->androidOrderEmail) ? $this->item->androidOrderEmail : '<b><span class="text-danger">'.JText::_('LNG_ORDER_EMAIL_NOTICE')."</span></b>"?>
                        </div>
                        <div class="form-group" id="buildCount" style="display:none" ><?php echo JText::_('LNG_BUILDS_LEFT') ?>: <span class="text-success" id="androidBuildCount"></span>
                            
                        </div>
                        <div id="androidMessage" class="text-danger"></div>
                        <button data-toggle="modal" onclick="event.preventDefault();jQuery( '#android-order-dtls' ).jbdModal({attachTo: 'adminForm'}); " class="btn btn-dark"><i class="la la-edit mr-2" style="color:#fff;" title="<?php echo JText::_('LNG_EDIT') ?>"></i><?php echo JText::_('LNG_EDIT_ORDER_DETAILS')?></button>

                        <?php if(!empty($this->item->androidOrderEmail) && !empty($this->item->androidOrderId)) { ?>
                                <button disabled type="submit" class="btn btn-success mr-2" id="buildAndroidBtn" onclick="processBuild('android')">
                                    <span class="ui-button-text">
                                        <i class="la la-android mr-1"></i><?php echo JText::_('LNG_BUILD_ANDROID'); ?>
                                    </span>
                                </button>
                            <?php } ?>
                           
                    </fieldset>
                    <fieldset class="boxed">
                        <h3><?php echo JText::_('LNG_IOS_ORDER_DETAILS',true); ?></h3>

                        <div class="form-group">
                            <label for="name"><?php echo JText::_('LNG_IOS_ORDER_ID')?> </label>
                            <?php echo !empty($this->item->iosOrderId) ? $this->item->iosOrderId : '<b><span class="text-danger">'.JText::_('LNG_ORDER_NOTICE')."</span></b>"?>
                        </div>
                        <div class="form-group">
                            <label for="name"><?php echo JText::_('LNG_IOS_ORDER_EMAIL')?> </label>
                            <?php echo !empty($this->item->iosOrderEmail) ? $this->item->iosOrderEmail : '<b><span class="text-danger">'.JText::_('LNG_ORDER_EMAIL_NOTICE')."</span></b>"?>
                        </div>
                        <div class="form-group" id="iosBuildC" style="display:none;" ><?php echo JText::_('LNG_BUILDS_LEFT') ?> <span class=" text-success" id="iosBuildCount"></span>
                            
                        </div>
                        <div id="iosMessage"></div>
                        <div class="form-group d-flex">
                        <button data-toggle="modal" onclick="event.preventDefault();jQuery( '#ios-order-dtls' ).jbdModal({attachTo: 'adminForm'}); " class="btn btn-dark mr-1"><i class="la la-edit mr-2" style="color:#fff;" title="<?php echo JText::_('LNG_EDIT') ?>"></i><?php echo JText::_('LNG_EDIT_ORDER_DETAILS')?></button>

                            <?php if(!empty($this->item->iosOrderEmail) && !empty($this->item->iosOrderId)) { ?>
                                <button disabled type="submit" class="btn btn-success" id="iosBuildBtn" onclick="processBuild('ios')">
                                    <span class="ui-button-text">
                                        <i class="la la-apple mr-1"></i><?php echo JText::_('LNG_BUILD_IOS'); ?>
                                    </span>
                                </button>
                            <?php } ?>
                        </div>
                    </fieldset>
    		 	</div>
    		 	<div class="col-md-5">
                    <?php require_once 'mobile_device_ui.php'; ?>
    		 	</div>
    		 </div>
    	</fieldset>

        <div id="android-order-dtls" class="jbd-container" style="display: none">    
            <div class="jmodal-sm">
                <div class="jmodal-header">
                    <p class="jmodal-header-title"><?php echo JText::_('LNG_ORDER_DETAILS') ?></p>
                    <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                </div>
                <div class="jmodal-body">
                    <div class="review-repsonse">
                        <fieldset>
                            <div class="form-item">
                                <label class="font-weight-bold"><?php echo JText::_('LNG_ANDROID_ORDER_ID') ?></label>
                                <div class="outer_input">
                                <input type="text" name="androidOrderId" id="androidOrderId">
                                    <?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                </div>
                            </div>

                            <div class="form-item">
                                <label class="font-weight-bold"><?php echo JText::_('LNG_ANDROID_ORDER_EMAIL') ?>:</label>
                                <div class="outer_input">
                                <input type="text" name="androidOrderEmail" id="androidOrderEmail">
                                <?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                </div>
                            </div>

                            <div class="clearfix clear-left">
                                <div class="button-row ">
                                    <button type="button" class="btn" onclick="updateOrderDetails('android');">
                                        <span class="ui-button-text"><?php echo JText::_("LNG_UPDATE") ?></span>
                                    </button>
                                    <button type="button" class="btn btn-dark" onclick="jQuery.jbdModal.close()">
                                        <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL") ?></span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>          
                </div>
            </div>
        </div>

        <div id="ios-order-dtls" class="jbd-container" style="display: none">    
            <div class="jmodal-sm">
                <div class="jmodal-header">
                    <p class="jmodal-header-title"><?php echo JText::_('LNG_ORDER_DETAILS') ?></p>
                    <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                </div>
                <div class="jmodal-body">
                    <div class="review-repsonse">
                        <fieldset>
                            <div class="form-item">
                                <label class="font-weight-bold"><?php echo JText::_('LNG_IOS_ORDER_ID') ?></label>
                                <div class="outer_input">
                                <input type="text" name="iosOrderId" id="iosOrderId">
                                    <?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                </div>
                            </div>

                            <div class="form-item">
                                <label class="font-weight-bold"><?php echo JText::_('LNG_IOS_ORDER_EMAIL') ?>:</label>
                                <div class="outer_input">
                                <input type="text" name="iosOrderEmail" id="iosOrderEmail">
                                <?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                </div>
                            </div>

                            <div class="clearfix clear-left">
                                <div class="button-row ">
                                    <button type="button" class="btn" onclick="updateOrderDetails('ios');">
                                        <span class="ui-button-text"><?php echo JText::_("LNG_UPDATE") ?></span>
                                    </button>
                                    <button type="button" class="btn btn-dark" onclick="jQuery.jbdModal.close()">
                                        <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL") ?></span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>          
                </div>
            </div>
        </div>

        <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="is_android" id="is_android" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>


<script>
    function updateOrderDetails(platform)
    {
        let updateOrderDetails = jbdUtils.getAjaxUrl('updateOrderDetailsAjax', 'mobileappconfig');
        let androidOrderEmail = jQuery('#androidOrderEmail').val();
        let androidOrderId = jQuery('#androidOrderId').val();
        let iosOrderEmail = jQuery('#iosOrderEmail').val();
        let iosOrderId = jQuery('#iosOrderId').val();
        let data

        if(platform == 'android') {
            data = {
                androidOrderEmail: androidOrderEmail,
                androidOrderId: androidOrderId,
                isAndroid: 1,
            };
        } else {
            data = {
               iosOrderEmail:iosOrderEmail,
               iosOrderId:iosOrderId,
                isAndroid: 0,
            };
        }

        jQuery.ajax({
            type: "POST",
            url: updateOrderDetails,
            data: data,
            dataType: 'json',
            success: function (data) {
                jQuery.jbdModal.close();
                if (data) {
                   location.reload();
                } else {
                    alert("<?php echo JText::_('LNG_UPDATE_ERROR') ?>");
                }
            },
            error: function(err) {
                console.log(err)
            }
        });
    }


    function processBuild(type) {
        if(type == 'android') {
            jQuery('#is_android').val(1);
        } else {
            jQuery('#is_android').val(0);
        }
        jQuery('#task').val('mobileappconfig.build');

        let form = document.adminForm;
            form.submit();
    }
    function configApp(type) {
        jQuery('#task').val('mobileappconfig.edit');
        let form = document.adminForm;
        form.submit();
    }

    window.addEventListener('load', function () {
        let buildCountUrl = jbdUtils.getAjaxUrl('getBuildCountAjax', 'mobileappconfig');
    	jQuery.ajax({
    		url: buildCountUrl,
    		dataType: 'json',
            cache: false,
    		type: 'GET',
            data: {'is_android': 1},
    		success: function(data){
                if(data.id == 0) {
                    jQuery('#buildAndroidBtn').removeAttr('disabled')
                    jQuery('#buildCount').show();
                    jQuery('#androidBuildCount').html(data.buildCount)
                } else {
                    jQuery('#androidMessage').html(data.message)
                }

            }
    	});

        jQuery.ajax({
    		url: buildCountUrl,
    		dataType: 'json',
            cache: false,
    		type: 'GET',
            data: {'is_android': 0},
    		success: function(data){
                if(data.id == 0) {
                    jQuery('#iosBuildBtn').removeAttr('disabled')
                    jQuery('#iosBuildC').show();
                    jQuery('#iosBuildCount').html(data.buildCount)

                } else {
                    jQuery('#iosMessage').html(data.message)
                }
            }
    	});
    });

</script>
