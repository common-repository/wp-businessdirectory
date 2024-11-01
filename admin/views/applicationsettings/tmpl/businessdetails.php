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

<div class="app_tab" id="panel_2">

<div class="row panel_2_content">
	<div class="col-md-6 general-settings">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_COMPANY_DETAILS'); ?></legend>
            <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="company_name-lbl" for="company_name" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_NAME');?></strong><br/><?php echo JText::_('LNG_NAME_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_NAME'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input class="validate[required]" name="company_name" id="company_name" value="<?php echo $this->item->company_name?>" size="50" maxlength="255" type="text">
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label id="company_email-lbl" for="company_email" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EMAIL');?></strong><br/><?php echo JText::_('LNG_EMAIL_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_EMAIL'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input  name="company_email" id="company_email" class="validate[custom[email]] validate[required]" value="<?php echo $this->item->company_email?>" size="50" maxlength="255" type="text">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="cc_email-lbl" for="cc_email" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CC_EMAIL');?></strong><br/><?php echo JText::_('LNG_CC_EMAIL_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_CC_EMAIL'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input  name="cc_email" id="cc_email" value="<?php echo $this->item->cc_email?>" size="50" maxlength="255" type="text">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="facebook-lbl" for="facebook" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_FACEBOOK');?></strong><br/><?php echo JText::_('LNG_FACEBOOK_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_FACEBOOK'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input name="facebook" id="facebook" value="<?php echo $this->item->facebook?>" size="50" maxlength="100" type="text">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="twitter-lbl" for="twitter" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TWITTER');?></strong><br/><?php echo JText::_('LNG_TWITTER_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_TWITTER'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input name="twitter" id="twitter" value="<?php echo $this->item->twitter?>" size="50" maxlength="100" type="text">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="linkedin-lbl" for="linkedin" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LINKEDIN');?></strong><br/><?php echo JText::_('LNG_LINKEDIN_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_LINKEDIN'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input name="linkedin" id="linkedin" value="<?php echo $this->item->linkedin?>" size="50" maxlength="100" type="text">
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label"><label id="youtube-lbl" for="youtube" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_YOUTUBE');?></strong><br/><?php echo JText::_('LNG_YOUTUBE_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_YOUTUBE'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <input name="youtube" id="youtube" value="<?php echo $this->item->youtube?>" size="50" maxlength="100" type="text">
                    </div>
                </div>


                <div class="control-group">
                    <div class="control-label"><label id="image-uploader-lbl" for="image-uploader" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_LOGO');?></strong><br/><?php echo JText::_('LNG_LOGO_BUSINESS_DETAILS');?>" title=""><?php echo JText::_('LNG_LOGO'); ?><span class="star">&nbsp;</span></label></div>
                    <div class="controls">
                        <div class="jupload logo-jupload">
                            <div class="jupload-body">
                                <div class="jupload-files">
                                    <div class="jupload-files-img image-fit-contain" id="picture-preview">
                                        <?php
                                        if (!empty($this->item->logo)) {
                                            echo "<img  id='logoImg' src='".BD_PICTURES_PATH.$this->item->logo."'/>";
                                        }else{
                                            echo "<i class='la la-image'></i>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="jupload-options">
                                <div class="jupload-options-btn jupload-actions">
                                    <label for="imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                    <a name="" id="" class="" href="javascript:uploadInstance.removeImage()" role="button"><i class="la la-trash"></i></a>
                                </div>
                                <div class="">
                                    <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                </div>
                            </div>
                            <input type="text" name="logo" style="visibility:hidden;height:1px; width:1px;" id="imageLocation" class="form-control validate[required]" value="<?php echo $this->item->logo?>" >
                            <div class="jupload-footer">
                                <fieldset>
                                 <!--<input type="hidden" name="logo" id="imageLocation" value="--><?php //echo $this->item->logo?><!--">-->
                                    <input type="file" id="imageUploader" name="uploadfile" size="50">
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</fieldset>
	</div>

    <div class="col-md-6 general-settings">
        <fieldset class="form-horizontal">
            <legend><?php echo JText::_('LNG_INVOICE_INFORMATION'); ?></legend>
            <div class="form-container">
            <div class="control-group">
                <div class="control-label"><label id="invoice_company_name-lbl" for="invoice_company_name" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_NAME');?></strong><br/><?php echo JText::_('LNG_NAME_INVOICE');?>" title=""><?php echo JText::_('LNG_NAME'); ?><span class="star">&nbsp;</span></label></div>
                <div class="controls">
                    <input name="invoice_company_name" id="invoice_company_name" value="<?php echo $this->item->invoice_company_name?>" size="50" maxlength="100" type="text">
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="invoice_company_address-lbl" for="invoice_company_address" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ADDRESS');?></strong><br/><?php echo JText::_('LNG_ADDRESS_INVOICE');?>" title=""><?php echo JText::_('LNG_ADDRESS'); ?><span class="star">&nbsp;</span></label></div>
                <div class="controls">
                    <input name="invoice_company_address" id="invoice_company_address" value="<?php echo $this->item->invoice_company_address?>" size="50" maxlength="75" type="text">
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="invoice_company_phone-lbl" for="invoice_company_phone" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_TELEPHONE_NUMBER');?></strong><br/><?php echo JText::_('LNG_TELEPHONE_NUMBER_INVOICE');?>" title=""><?php echo JText::_('LNG_TELEPHONE_NUMBER'); ?><span class="star">&nbsp;</span></label></div>
                <div class="controls">
                    <input name="invoice_company_phone" id="invoice_company_phone" value="<?php echo $this->item->invoice_company_phone?>" size="50" maxlength="75" type="text">
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="invoice_company_email-lbl" for="invoice_company_email" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EMAIL');?></strong><br/><?php echo JText::_('LNG_EMAIL_NUMBER_INVOICE');?>" title=""><?php echo JText::_('LNG_EMAIL'); ?><span class="star">&nbsp;</span></label></div>
                <div class="controls">
                    <input name="invoice_company_email" id="invoice_company_email" class="validate[custom[email]]" value="<?php echo $this->item->invoice_company_email?>" size="50" maxlength="75" type="text">
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><label id="invoice_vat-lbl" for="invoice_vat" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_VAT_NUMBER');?></strong><br/><?php echo JText::_('LNG_VAT_NUMBER_NUMBER_INVOICE');?>" title=""><?php echo JText::_('LNG_VAT_NUMBER'); ?><span class="star">&nbsp;</span></label></div>
                <div class="controls">
                    <input name="invoice_vat" id="invoice_vat" value="<?php echo $this->item->invoice_vat?>" size="50" maxlength="75" type="text">
                </div>
            </div>

			<div class="control-group">
				<div class="control-label"><label id="invoice_prefix-lbl" for="invoice_prefixt" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_PREFIX_NUMBER');?></strong><br/><?php echo JText::_('LNG_PREFIX_NUMBER_NUMBER_INVOICE');?>" title=""><?php echo JText::_('LNG_PREFIX_NUMBER'); ?><span class="star">&nbsp;</span></label></div>
				<div class="controls">
                    <input name="invoice_prefix" id="invoice_prefix" value="<?php echo $this->item->invoice_prefix?>" size="50" maxlength="75" type="text">
                </div>
			</div>
		
			<div class="control-group">
				<div class="control-label"><label id="vat-lbl" for="vat" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_VAT');?></strong><br/><?php echo JText::_('LNG_VAT_NUMBER_INVOICE');?>" title=""><?php echo JText::_('LNG_VAT'); ?> (%)</label></div>
				<div class="controls">
                    <input type="text" size=40 maxlength=20  id="vat" name = "vat" value="<?php echo $this->item->vat?>">
				</div>
			</div>
            <div class="control-group">
                <div class="control-label"><label id="packages_vat_apply-lbl" for="packages_vat_apply" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_APPLY_VAT_DIRECTLY');?></strong><br/><?php echo JText::_('LNG_APPLY_VAT_DIRECTLY_DESC');?>" title=""><?php echo JText::_('LNG_APPLY_VAT_DIRECTLY'); ?></label></div>
                <div class="controls">
                    <fieldset id="add_url_id_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="packages_vat_apply" id="packages_vat_apply1" value="1" <?php echo $this->item->packages_vat_apply==true? 'checked="checked"' :""?> />
                        <label class="btn" for="packages_vat_apply1"><?php echo JText::_('LNG_YES')?></label>
                        <input type="radio"  name="packages_vat_apply" id="packages_vat_apply0" value="0" <?php echo $this->item->packages_vat_apply==false? 'checked="checked"' :""?> />
                        <label class="btn" for="packages_vat_apply0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label"><label id="invoice_details-lbl" for="invoice_details" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_INVOICE_DETAILS');?></strong><br/><?php echo JText::_('LNG_INVOICE_DETAILS_NUMBER_INVOICE');?>" title=""><?php echo JText::_('LNG_INVOICE_DETAILS'); ?><span class="star">&nbsp;</span></label></div>
                <div class="controls">
                    <textarea rows="5" cols="300" name="invoice_details" id="invoice_details" class="h-auto" name="meta_keywords"><?php echo $this->item->invoice_details ?></textarea>
                </div>
            </div>

             <div class="control-group d-flex">
                 <div class="control-label" style="align-self: flex-start;"><label id="vat_config-lbl" for="vat_config" class="hasTooltip required" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_VAT_CONFIGURATION');?></strong><br/><?php echo JText::_('LNG_VAT_CONFIGURATION_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_VAT_CONFIGURATION'); ?><span class="star">&nbsp;</span></label></div>
                 <div class="controls">
                    <div id="vat_config">
                        <?php foreach ($this->item->vat_configuration as $config) { ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <select class="vat-chosen" name="vat_config_country[]">
                                        <?php foreach ($this->item->countries as $country) {
                                            $selected = "";
                                            if ($country->id == $config->country_id) {
                                                $selected = "selected";
                                            } ?>
                                            <option value="<?php echo $country->id ?>" <?php echo $selected ?>><?php echo $country->country_name ?></option>
                                        <?php } ?>
                                    </select>

                                    <input type="text" name="vat_config_value[]" class="ml-2 validate[required]" value="<?php echo $config->value ?>" placeholder="%">
                                    <a href="javascript:void(0);" onclick="jQuery(this).parent().remove()" class="m-auto"><i class="la la-times"></i></a>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="javascript:void(0);" id="add_vat_config" class="btn btn-primary"><i class="la la-plus"></i></a>
                    </div>
                 </div>
             </div>
            <?php if (JBusinessUtil::isAppInstalled(JBD_APP_APPOINTMENTS)) { ?>
                <div class="control-group">
                    <div class="control-label"><label id="appointments_commission-lbl" for="appointments_commission" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_APPOINTMENTS_COMMISSION');?></strong><br/><?php echo JText::_('LNG_APPOINTMENTS_COMMISSION_DESC');?>" title=""><?php echo JText::_('LNG_APPOINTMENTS_COMMISSION'); ?> (%)</label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=20  id="appointments_commission" name="appointments_commission" value="<?php echo $this->item->appointments_commission?>"> 
                    </div>
                </div>
            <?php } ?>

            <?php if (JBusinessUtil::isAppInstalled(JBD_APP_SELL_OFFERS)) { ?>
            <div class="control-group">
                <div class="control-label"><label id="offer_selling_commission-lbl" for="offer_selling_commission" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_SELLING_COMMISSION');?></strong><br/><?php echo JText::_('LNG_OFFER_SELLING_COMMISSION_DESC');?>" title=""><?php echo JText::_('LNG_OFFER_SELLING_COMMISSION'); ?> (%)</label></div>
                <div class="controls">
                    <input type="text" size=40 maxlength=20  id="offer_selling_commission" name="offer_selling_commission" value="<?php echo $this->item->offer_selling_commission?>">
                </div>
            </div>
            <?php } ?>

            <?php if (JBusinessUtil::isAppInstalled(JBD_APP_EVENT_BOOKINGS)) { ?>
                <div class="control-group">
                    <div class="control-label"><label id="event_tickets_commission-lbl" for="offer_selling_commission" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_EVENT_TICKETS_COMMISSION');?></strong><br/><?php echo JText::_('LNG_EVENT_TICKETS_COMMISSION_DESC');?>" title=""><?php echo JText::_('LNG_EVENT_TICKETS_COMMISSION'); ?> (%)</label></div>
                    <div class="controls">
                        <input type="text" size=40 maxlength=20  id="event_tickets_commission" name="event_tickets_commission" value="<?php echo $this->item->event_tickets_commission?>">
                    </div>
                </div>
            <?php } ?>
            </div>
        </fieldset>
    </div>
</div>

</div>

<script>
    let countries = <?php echo json_encode($this->item->countries) ?>;
	window.addEventListener('load', function() {
        uploadInstance.imageUploader(appImgFolder, appImgFolderPath);
        uploadInstance.imageUploader(appImgFolder, appImgFolderPath, 'default_bg_listing-');
        jQuery('.vat-chosen').chosen();
        jQuery('#add_vat_config').click(addVatEntry);
    });

	function addVatEntry() {
	    let selectedCountries = [];
        jQuery('.vat-chosen').each(function() {
            selectedCountries.push(jQuery(this).find(':selected').val());
        });

	    let html = `
            <div class="d-flex justify-content-between mb-2">
               <select class="vat-chosen" name="vat_config_country[]">
                ${countries.map((country) => {
                    if (!selectedCountries.includes(country.id)) {
                        return `<option value="${country.id}">${country.country_name}</option>`
                    }
                })}
                </select>

                <input type="text" name="vat_config_value[]" class="ml-2 validate[required]" placeholder="%">
                <a href="javascript:void(0);" onclick="jQuery(this).parent().remove()" class="m-auto"><i class="la la-times"></i></a>
            </div>
	    `;

	    jQuery('#vat_config').append(html);
        jQuery('.vat-chosen').chosen();
    }
</script>