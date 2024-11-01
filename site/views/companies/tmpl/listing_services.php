<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<style>
    div.ui-datepicker{
        font-size:12px;
    }

    ul li{
        list-style: none;
        list-style-type: none;
        list-style-position: initial;
        list-style-image: initial;
    }
</style>
<div class="services-container">
    <div id="wizard">
        <h3> <span class="badge">1</span> <?php echo JText::_('LNG_SELECT_SERVICE'); ?></h3>
        <section>
            <?php foreach($this->services as $service) { ?>
                <div class="row">
                    <div class="col-md-6">
                        <b><a href="javascript:void(0)" class="service-link" onclick="jbdListings.selectService(<?php echo $service->id ?>)"><?php echo $this->escape($service->name) ?></a></b>
                    </div>
                    <div class="col-md text-right">
                        <?php if($service->show_duration) { ?>
                            <p><?php echo JBusinessUtil::formatTimePeriod($service->duration, 1) ?></p>
                        <?php } ?>
                    </div>
                    <?php if(!empty((float)$service->price)){ ?>
                        <div class="col-md-3">
                            <p><?php echo JBusinessUtil::getPriceFormat($service->price, $service->currency_id); ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="clear"></div>
                <hr/>
            <?php } ?>
        </section>
        <h3> <span class="badge">2</span> <?php echo JText::_('LNG_SELECT_PROVIDER'); ?></h3>
        <section>
            <div id="providers-content">
            </div>
    
            <form style="display:none;" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'); ?>" method="post" name="serviceForm" id="service-form">
                <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
                <input type="hidden" name="task" value="serviceguestdetails.checkBillingDetails" />
                <input type="hidden" name="companyId" id="companyId" value="<?php echo $this->company->id ?>" />
                <input type="hidden" name="serviceId" id="serviceId" value="" />
                <input type="hidden" name="providerId" id="providerId" value="" />
                <input type="hidden" name="currency_id" id="currency_id" value="<?php echo $service->currency_id ?>" />
                <input type="hidden" name="date" id="date" value="" />
                <input type="hidden" name="hour" id="hour" value="" />
                <?php echo JHtml::_('form.token'); ?>
            </form>
        </section>
        <h3> <span class="badge">3</span> <?php echo JText::_('LNG_DATE_AND_TIME'); ?></h3>
        <section>
            <div class="selected-service-details d-flex justify-content-start">
                <div class="step-selected selected-provider-service">

                </div>
                <div class="step-selected selected-provider">

                </div>
            </div>
            <div class="row">
                <div class="col-lg-6" id="datepicker"></div>
                <div class="col-lg-6 available-hours justify-content-end" id="available-hours">
                    <div class="row">
                        <div class="col-sm-4 text-center">
                            <div class="day-section"><?php echo JText::_('LNG_MORNING'); ?></div>
                            <ul id="morning" class="list-unstyled">
    
                            </ul>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="day-section"><?php echo JText::_('LNG_AFTERNOON'); ?></div>
                            <ul id="afternoon" class="list-unstyled">
    
                            </ul>
                        </div>
                        <div class="col-sm-4 text-center">
                            <div class="day-section"><?php echo JText::_('LNG_EVENING'); ?></div>
                            <ul id="evening" class="list-unstyled">
    
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    var todayDate = new Date();
    var maxDate = new Date();
    maxDate.setMonth(todayDate.getMonth() + 6);

    var serviceId = 0;
    var providerId = 0;
    var providerType = 1;
    var providerName;
    var serviceName;
    var selectedHour;
    var selectedDate;

    var wizard = jQuery("#wizard").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slide",
        titleTemplate: "#title#",
        enablePagination: false,
        onStepChanging: function (event, currentIndex, newIndex) {
            if (serviceId == 0) {
                alert("<?php echo JText::_('LNG_PLEASE_SELECT_SERVICE'); ?>");
                return false;
            }

            if (newIndex == 1) {
                jbdListings.renderProviders();
            } else if (newIndex == 2) {
                if (providerId == 0) {
                    alert("<?php echo JText::_('LNG_PLEASE_SELECT_PROVIDER'); ?>");
                    return false;
                } else {
                    jbdListings.createProviderCalendarFront();
                }
            }

            return true;
        },
        onStepChanged: function () {
            jQuery('.steps .current').nextAll().removeClass('done').addClass('disabled');
        },
        onFinishing: function (event, currentIndex) {
            jQuery('#serviceId').val(serviceId);
            jQuery('#providerId').val(providerId);
            jQuery('#date').val(selectedDate);
            jQuery('#hour').val(selectedHour);

            document.serviceForm.submit();
        }
    });
</script>