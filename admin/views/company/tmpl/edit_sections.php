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
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once BD_CLASSES_PATH . '/attributes/attributeservice.php';

JBusinessUtil::enqueueScript('libraries/bootstrap/bootstrap-tagsinput.min.js');
JBusinessUtil::enqueueStyle('libraries/bootstrap/bootstrap-tagsinput.css');

JHtml::register('behavior.tooltip', function () {
    return null;
});

JBusinessUtil::loadJQueryChosen();

$attributeConfig = $this->item->defaultAtrributes;
$enablePackages  = $this->appSettings->enable_packages;

$app       = JFactory::getApplication();
$jinput    = $app->input;
$showSteps = $jinput->get("showSteps", false);
$showTabs  = true;
$isNew     = ($this->item->id == 0 && isset($isProfile)) ? true : false;

if (empty($this->item->id)) {
    $this->item->id = 0;
}

$options   = array(
    'onActive'     => 'function(title, description) {
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
    'onBackground' => 'function(title, description) {
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
    'startOffset'  => 0,  // 0 starts on the first tab, 1 starts the second, etc...
    'useCookie'    => true, // this must not be a string. Don't use quotes.
);

$jbdTabs = new JBDTabs();
$jbdTabs->setOptions($options);

$maxPictures = isset($this->item->package) ? $this->item->package->max_pictures : $this->appSettings->max_pictures;
$nrPictures = count($this->item->pictures);
$allowedNr = $maxPictures - $nrPictures;
$allowedNr = ($allowedNr < 0) ? 0 : $allowedNr;
$allowedNr = ($allowedNr == 0) ? $maxPictures : $allowedNr;

$totalContacts = 0;

$user = JBusinessUtil::getUser();
$presentTabs = array(1);
$session = JFactory::getSession();
$uniqueCode = uniqid();

$showLocation = $attributeConfig["street_number"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["address"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["area"] != ATTRIBUTE_NOT_SHOW
    || $attributeConfig["country"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["city"] != ATTRIBUTE_NOT_SHOW
    || $attributeConfig["province"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["region"] != ATTRIBUTE_NOT_SHOW
    || $attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["map"] != ATTRIBUTE_NOT_SHOW;


$mapKey  = JBusinessUtil::loadMapScripts(null, true);

//lock custom attributes if the listing is approved
$lockAttributes = false;
if ($this->appSettings->lock_custom_fields && isset($isProfile) && $this->item->approved == COMPANY_STATUS_APPROVED) {
    $lockAttributes = true;
}
$aliasStyle = (!$this->appSettings->show_alias && isset($isProfile)) ? "display:none" : "";

$maxDescriptionLength = !empty($this->item->package->max_description_length) ? $this->item->package->max_description_length : COMPANY_DESCRIPTIION_MAX_LENGHT;

$maxActivityCities = 0;//isset($this->item->package)?$this->item->package->max_activity_cities :$this->appSettings->max_activity_cities;
$maxActivityRegions = 0;//isset($this->item->package)?$this->item->package->max_activity_regions :$this->appSettings->max_activity_regions;
$maxActivityCountries = 0;//isset($this->item->package)?$this->item->package->max_activity_countries :$this->appSettings->max_activity_countries;

$packageId = $jinput->get("filter_package");
if (empty($packageId) && !empty($this->item->package)) {
    $packageId = $this->item->package->id;
}
$showActivityAreaSelect = false;
$splitForm = $this->appSettings->split_edit_form && $showSteps;

$backLink = isset($isProfile)? JBusinessUtil::processURL(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies', false)):JRoute::_('index.php?option=com_jbusinessdirectory&view=companies', false);
?>

<script type="text/javascript">
    window.addEventListener('load', function() {
        JBD.submitbutton = function(task) {

            jQuery("#item-form #task").val("");

            jQuery("#item-form").validationEngine('detach');
            if (jbdUtils.getProperty("isMultilingual")) {
                let tabgroup = jQuery(".tab-" + jbdUtils.getProperty("defaultLang"));
                if (tabgroup.length == 0){
                    tabgroup = jQuery("button[aria-controls='tab-" + jbdUtils.getProperty("defaultLang") + "']");
                }
                tabgroup.each(function () {
                    jQuery(this).click();
                });
            }

            if (task == 'company.cancel' || task == 'company.aproveClaim' || task == 'company.disaproveClaim' || jbdUtils.validateTabs(false, <?php echo $attributeConfig["description"] && isset($isProfile) == ATTRIBUTE_MANDATORY ? 'true' : 'false' ?>)) {
                JBD.submitform(task, document.getElementById('item-form'));
            }
            jQuery("#item-form").validationEngine('attach');
        }
    });
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container <?php echo $showTabs ? "tab-edit" : "one-page-edit" ?>">
    
    <div class="edit-header">
        <div>
            <a href="<?php echo $backLink?>" class="btn btn-outline-success btn-round btn-back"><i class="la la-angle-left"></i> <?php echo JText::_("LNG_BACK")?></a>
        </div>
        <div class="edit-page-title">
            <?php echo JText::_("LNG_PROFILE_EDITOR") ?>
        </div>
        <div class="text-right">
            <a target="_blank" href="<?php echo JBusinessUtil::getCompanyLink($this->item) ?>"><i class="icon link-external"></i> <?php echo JText::_("LNG_VIEW_LIVE_PAGE") ?></a>
            <?php if(isset($isProfile)){?>
                <a target="_blank" href="<?php echo JBusinessUtil::getCompanyLink($this->item)."?share=true"?>" class="btn btn-success btn-round ml-3"><?php echo JText::_("LNG_SHARE")?></a>
            <?php } ?>
        </div>
    </div>

    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal" enctype="multipart/form-data">
        <div id="jbd-sections">
            <div class="row">
                <div class="<?php echo (!isset($isProfile) || isset($isProfile) && $this->appSettings->front_end_meta_data && !$isNew) ? "col-lg-8" : "col-md-12" ?>">
                    <?php if ($enablePackages && !isset($isProfile)) { ?>
                        <div class="">
                            <div class="item-package-details">
                                <fieldset class="boxed package">
                                    <div class="package_content">
                                        <label><?php echo JText::_('LNG_SELECTED_PACKAGE') ?></label>
                                        <select name="filter_package" id="filter_package_select" class="form-control chosen-select" onchange="this.form.submit()">
                                            <?php echo JHtml::_('select.options', $this->packageOptions, 'value', 'text', $this->state->get('company.packageId')); ?>
                                        </select>
                                        <br><br>
                                        <div>
                                            <div class="">
                                                <h3><?php echo $this->item->package->name ?></h3>
                                                <p class="d-block m-0"><?php echo JText::_('LNG_CURRENT_PACKAGE') ?></p>
                                                <a id="show-package-details" href="javascript:void(0);" onclick='jQuery("#package-details").toggleClass("d-none")'><?php echo JText::_("LNG_SHOW_DETAILS") ?></a>
                                            </div>
                                            <br />
                                            <div id="package-details" class="d-none">
                                                <?php require_once 'package_details.php'; ?>
                                            </div>
                                            <?php ?>
                                            <?php
                                            if (isset($this->item->paidPackage)) { ?>
                                                <?php echo JText::_('LNG_STATUS') ?>: <?php echo !$this->item->paidPackage->expired ? JText::_("LNG_ACTIVE") : JText::_("LNG_EXPIRED") ?>
                                                <br />
                                                <?php echo JText::_('LNG_START_DATE') ?>: <?php echo JBusinessUtil::getDateGeneralFormat($this->item->paidPackage->start_date) ?> <br />
                                                <?php if (!empty($this->item->lastActivePackage) && $this->item->lastActivePackage->expiration_type != 1) { ?>
                                                    <?php echo JText::_('LNG_EXPIRATION_DATE') ?>: <?php echo JBusinessUtil::getDateGeneralFormat($this->item->paidPackage->expirationDate) ?><a href="javascript:jbdListings.extendPeriod(<?php echo $this->item->paidPackage->package_id ?>)"> <?php echo $this->item->lastActivePackage->expired ? JText::_("LNG_RENEW") : JText::_("LNG_EXTEND_PERIOD") ?></a>
                                                <?php } ?>
                                            <?php
                                            } elseif ($this->item->id > 0) { ?>
                                                <?php echo JText::_('LNG_STATUS') ?>: <?php echo $this->item->package->price == 0 ? JText::_("LNG_FREE") : JText::_("LNG_NOT_PAID") ?>
                                                <div class="d-flex align-items-center"><i class="la la-exclamation-circle text-warning la-2x"></i> <?php echo $this->item->statusTxt ?></div>
                                            <?php
                                            } ?>
                                        </div>
                                        <?php
                                        if (!isset($this->item->paidPackage) && isset($this->item->lastActivePackage)) { ?>
                                            <div class="package-info">
                                                <?php echo JText::_('LNG_LAST_PAID_PACKAGE') ?>: <?php echo $this->item->lastActivePackage->name ?><br />
                                                <?php echo JText::_('LNG_STATUS') ?>: <?php echo !$this->item->lastActivePackage->expired ? JText::_("LNG_VALID") : JText::_("LNG_EXPIRED") ?><br />
                                                <?php echo JText::_('LNG_START_DATE') ?>: <?php echo JBusinessUtil::getDateGeneralFormat($this->item->lastActivePackage->start_date) ?> <br />
                                                <?php if ($this->item->lastActivePackage->expiration_type != 1) { ?>
                                                    <?php echo JText::_('LNG_EXPIRATION_DATE') ?>: <?php echo JBusinessUtil::getDateGeneralFormat($this->item->lastActivePackage->expirationDate) ?> <a href="javascript:jbdListings.extendPeriod(<?php echo $this->item->lastActivePackage->package_id ?>)"> <?php echo $this->item->lastActivePackage->expired ? JText::_("LNG_RENEW") : JText::_("LNG_EXTEND_PERIOD") ?></a>
                                                <?php } ?>
                                            </div>
                                        <?php
                                        } ?>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?php
                        $packageId = $jinput->get("filter_package");
                        if (empty($packageId) && !empty($this->item->package)) {
                            $packageId = $this->item->package->id;
                        }
                        ?>
                        <input type="hidden" name="filter_package" id="filter_package" value="<?php echo $packageId ?>" />
                    <?php } ?>

                    <div id="validation-error" class="validation-error row" style="display:none">
                        <i class="la la-warning"></i> <?php echo JText::_("LNG_VALIDATION_ERROR") ?>
                    </div>

                    <?php if ($showTabs) { ?>
                        <div id="process-tabs" class="process-tabs">
                            <div id="tab1" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>" >
                                <div class="section-header" data-tab="1">
                                    <div>
                                        <div class="section-title"><?php echo JText::_("LNG_SECTION_1") ?></div>
                                        <div class="section-title-description"><?php echo JText::_("LNG_SECTION_1_DESCRIPTION") ?></div>
                                    </div>
                                    <div class="section-toolbar" style="display:none">
                                        <button type="button" class="btn btn-link jbtn-round" onclick="cancel(event, 1)">
                                            <?php echo JText::_("LNG_CANCEL") ?>
                                        </button>
                                        <button type="button" class="btn-action" onclick="saveFormAjax(event,'general');">
                                            <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                        </button>
                                    </div>
                                </div>
                                <div id="edit-tab1" class="edit-tab" style="display:none">
                                    <div class="form-container">
                                        <div class="edit-section">
                                            <div class="edit-section-limit">
                                                <div class="row align-items-end">
                                                    <div class="col-md">
                                                        <div class="form-group">
                                                            <label for="name"><?php echo JText::_('LNG_COMPANY_NAME') ?> <?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY) ?></label>
                                                            <?php
                                                            if ($this->appSettings->enable_multilingual) {
                                                                echo $jbdTabs->startTabSet('tab_group_name');
                                                                foreach ($this->languages as $k => $lng) {
                                                                    echo $jbdTabs->addTab('tab_group_name', 'tab-' . $lng, $k);
                                                                    $langContent = isset($this->translations[$lng . "_name"]) ? $this->translations[$lng . "_name"] : "";
                                                                    $required    = "";
                                                                    if ($lng == JBusinessUtil::getLanguageTag()) {
                                                                        $required = "validate[required]";
                                                                        if (empty($langContent)) {
                                                                            $langContent = $this->item->name;
                                                                        }
                                                                    }
                                                                    echo "<input type='text' name='name_$lng' id='name_$lng' class='form-item-name form-control $required' value=\"" . $this->escape($langContent) . "\"  maxLength='100'>";
                                                                    echo $jbdTabs->endTab();
                                                                }
                                                                echo $jbdTabs->endTabSet();
                                                            } else { ?>
                                                                <input type="text" name="name" id="name" class="form-item-name form-control validate[required]" value="<?php echo $this->escape($this->item->name) ?>" maxLength="100">
                                                            <?php } ?>
                                                            <span class="error_msg" id="company_exists_msg" style="display: none;"><?php echo JText::_('LNG_COMPANY_NAME_ALREADY_EXISTS') ?></span>
                                                            <span class="" id="claim_company_exists_msg" style="display: none;"><?php echo JText::_('LNG_CLAIM_COMPANY_EXISTS') ?> <a id="claim-link" href=""><?php echo JText::_("LNG_HERE") ?></a></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md" style="<?php echo $aliasStyle ?>">
                                                        <div class="form-group">
                                                            <label for="alias"><?php echo JText::_('LNG_ALIAS') ?> </label>
                                                            <input type="text" name="alias" id="alias" placeholder="<?php echo JText::_('LNG_AUTO_GENERATE_FROM_NAME') ?>" class="form-control text-input" value="<?php echo $this->escape($this->item->alias) ?>" maxlength="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if ($attributeConfig["website"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                    <?php if (!$enablePackages || isset($this->item->package->features) && in_array(WEBSITE_ADDRESS, $this->item->package->features)) { ?>
                                                        <div class="row">
                                                            <div class="col-md">
                                                                <div class="form-group">
                                                                    <label for="website"><?php echo JText::_('LNG_WEBSITE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["website"]) ?></label>
                                                                    <input type="text" name="website" id="website" value="<?php echo $this->escape($this->item->website) ?>" class="form-control <?php echo $attributeConfig["website"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" maxlength="250">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>

                                                <div class="row">
                                                    <?php if ($attributeConfig["email"] != ATTRIBUTE_NOT_SHOW || $this->appSettings->allow_user_creation == 1) { ?>
                                                        <div class="col-md">
                                                            <div class="form-group">
                                                                <label for="email"><?php echo JText::_('LNG_EMAIL')?> <?php echo JBusinessUtil::showMandatory($attributeConfig["email"] || $this->appSettings->allow_user_creation == 1) ?></label>
                                                                <div class="has-jicon-right">
                                                                    <input type="text" name="email" id="email" class="form-control <?php echo $attributeConfig["email"] == ATTRIBUTE_MANDATORY || $this->appSettings->allow_user_creation == 1 ?"validate[required,custom[email]]":"validate[custom[email]]"?> text-input" value="<?php echo $this->escape($this->item->email) ?>" maxlength="100">
                                                                    <i class="la la-envelope"></i>
                                                                </div>
                                                                <div class="description">e.g. office@site.com</div>
                                                                <span class="error_msg" id="frmEmail_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD')?></span>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if ($attributeConfig["phone"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(PHONE, $this->item->package->features))) { ?>
                                                        <div class="col-md">
                                                            <div class="form-group">
                                                                <label for="phone"><?php echo JText::_('LNG_TELEPHONE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["phone"]) ?></label>
                                                                <div class="has-jicon-right">
                                                                    <input type="text" name="phone" id="phone" class="form-control <?php echo $attributeConfig["phone"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" value="<?php echo $this->escape($this->item->phone) ?>" maxlength="20">
                                                                    <i class="la la-phone"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="edit-section">
                                            <div class="edit-section-limit">
                                                <?php if ($attributeConfig["logo"]!=ATTRIBUTE_NOT_SHOW) { ?>
                                                    <?php if (!$enablePackages || isset($this->item->package->features) && in_array(SHOW_COMPANY_LOGO,$this->item->package->features)) { ?>
                                                        <div class="form-group">
                                                            <label><?php echo JText::_('LNG_ADD_LOGO');?><?php echo JBusinessUtil::showMandatory($attributeConfig["logo"]) ?></label>
                                                            <div class="jupload logo-jupload">
                                                                <div class="jupload-body">
                                                                    <div class="jupload-files">
                                                                        <div class="jupload-files-img image-fit-contain" id="picture-preview">
                                                                            <?php
                                                                            if (!empty($this->item->logoLocation)) {
                                                                                echo "<img src='".BD_PICTURES_PATH.$this->item->logoLocation."'/>";
                                                                            }else{
                                                                                echo "<i class='la la-image'></i>";
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="jupload-options">
                                                                    <div class="jupload-options-btn jupload-actions">
                                                                        <label for="imageUploader" class="btn btn-outline-success jbtn-round"><?php echo JText::_("LNG_UPLOAD")?></label>
                                                                        <i class="la la-trash" onbclick="javascript:uploadInstance.removeImage()" role="button"></i>
                                                                    </div>
                                                                    <div class="">
                                                                        <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                                                    </div>
                                                                </div>
                                                                <input type="text" name="logoLocation" style="visibility:hidden;height:1px; width:1px;" id="imageLocation" class="form-control <?php echo $attributeConfig["logo"] == ATTRIBUTE_MANDATORY?"validate[required]":""?> "><br/>
                                                                <div class="jupload-footer">
                                                                    <fieldset>
                                                                        <input type="hidden" value="2097152" name="MAX_FILE_SIZE">
                                                                        <input  type="file" id="imageUploader" name="uploadLogo" size="50">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php if ($attributeConfig["cover_image"]!=ATTRIBUTE_NOT_SHOW) { ?>
                                                    <div class="form-group">
                                                        <label> <?php echo JText::_('LNG_ADD_BUSINESS_COVER_IMAGE');?> <?php $attributeConfig["cover_image"]=ATTRIBUTE_OPTIONAL?"(". JText::_('LNG_OPTIONAL').")":"" ?></label>
                                                        <div class="jupload cover-jupload">
                                                            <div class="jupload-body">
                                                                <div class="jupload-files">
                                                                    <div class="jupload-files-img image-fit-contain" id="cover-picture-preview">
                                                                        <?php if(!empty($this->item->business_cover_image)){
                                                                            echo "<img src='".BD_PICTURES_PATH.$this->item->business_cover_image."'/>";
                                                                        }else{
                                                                            echo "<i class='la la-image'></i>";
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="jupload-footer">
                                                                <div>
                                                                    <label for="cover-imageUploader" class="btn btn-success"><?php echo JText::_("LNG_CHOOSE_FILE")?></label>
                                                                    <a name="" id="" class="btn btn-link" href="javascript:uploadInstance.removeImage('cover-')" role="button"><?php echo JText::_("LNG_REMOVE")?></a>
                                                                </div>

                                                                <fieldset>
                                                                    <input type="hidden" name="business_cover_image" id="cover-imageLocation" value="<?php echo $this->item->business_cover_image ?>">
                                                                    <input type="file" id="cover-imageUploader" name="uploadfile" size="50">
                                                                    <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>. <?php echo JBusinessUtil::showMandatory($attributeConfig["cover_image"]) ?>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="edit-section">
                                            <div class="edit-section-limit">
                                                <?php if ($attributeConfig["slogan"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label for="slogan"><?php echo JText::_("LNG_COMPANY_SLOGAN") ?><?php echo JBusinessUtil::showMandatory($attributeConfig["slogan"]) ?></label>
                                                                <?php
                                                                if ($this->appSettings->enable_multilingual) {
                                                                    echo $jbdTabs->startTabSet('tab_group_slogan');
                                                                    foreach ($this->languages as $k => $lng) {
                                                                        echo $jbdTabs->addTab('tab_group_slogan', 'tab-' . $lng, $k);
                                                                        $langContent = isset($this->translationsSlogan[$lng]) ? $this->translationsSlogan[$lng] : "";
                                                                        $required    = "";
                                                                        if ($lng == JBusinessUtil::getLanguageTag()) {
                                                                            $required = $attributeConfig['slogan'] == ATTRIBUTE_MANDATORY ? 'validate[required]' : '';
                                                                            if (empty($langContent)) {
                                                                                $langContent = $this->item->slogan;
                                                                            }
                                                                        }
                                                                        echo "<textarea id='slogan_$lng' name='slogan_$lng' class='form-control $required' cols='75' rows='5' maxLength='" . COMPANY_SLOGAN_MAX_LENGHT . "'>" . $this->escape($langContent) . "</textarea>";
                                                                        echo $jbdTabs->endTab();
                                                                    }
                                                                    echo $jbdTabs->endTabSet();
                                                                } else { ?>
                                                                    <textarea name="slogan" id="slogan" class="form-control text-input <?php echo $attributeConfig["slogan"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" cols="75" rows="5" maxLength="<?php echo COMPANY_SLOGAN_MAX_LENGHT ?>" onkeyup="jbdUtils.calculateLengthSlogan();"><?php echo $this->item->slogan ?></textarea>
                                                                    <div class="description-counter">
                                                                        <input type="hidden" name="descriptionMaxLenghtSlogan" id="descriptionMaxLenghtSlogan" value="<?php echo COMPANY_SLOGAN_MAX_LENGHT ?>" />
                                                                        <label for="decriptionCounterSlogan">(Max. <?php echo COMPANY_SLOGAN_MAX_LENGHT ?> <?php JText::_('LNG_CHARACTRES') ?>).</label>
                                                                        <?php echo JText::_('LNG_REMAINING') ?> <input type="text" class="js-slogan" value="0" id="descriptionCounterSlogan" name="descriptionCounterSlogan">
                                                                    </div>
                                                                <?php
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($attributeConfig["description"] != ATTRIBUTE_NOT_SHOW  && (!$enablePackages || isset($this->item->package->features) && in_array(DESCRIPTION, $this->item->package->features))) { ?>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label for="description_id"><?php echo JText::_("LNG_COMPANY_DESCRIPTION") ?><?php echo JBusinessUtil::showMandatory($attributeConfig["description"]) ?></label>
                                                                <div class="quill-container">
                                                                    <textarea name="description" id="description" class="<?php echo $attributeConfig["description"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" cols="75" rows="10" maxLength="<?php echo $maxDescriptionLength ?>" onkeyup="calculateLenght();"><?php echo $this->item->description ?></textarea>
                                                                </div>
                                                                <div class="description-counter">
                                                                    <input type="hidden" name="descriptionMaxLenght" id="descriptionMaxLenght" value="<?php echo $maxDescriptionLength ?>" />
                                                                    <label for="descriptionCounter">(Max. <?php echo $maxDescriptionLength ?> characters).</label>
                                                                    <?php echo JText::_('LNG_REMAINING') ?><input type="text" value="0" id="descriptionCounter" name="descriptionCounter">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                } else if (!empty($this->item->description)) { ?>
                                                    <span style="display:none;">
                                                        <textarea style="display:none;" name="description">
                                                            <?php echo $this->item->description ?>
                                                        </textarea>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-footer" style="display:none">
                                    <div class="section-toolbar" style="display:none">
                                        <button type="button" class="btn btn-link" onclick="cancel(event, 1)">
                                            <?php echo JText::_("LNG_CANCEL") ?>
                                        </button>
                                        <button type="button" class="btn-action" onclick="saveFormAjax(event,'general');">
                                            <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <?php 
                                $presentTabs[] = 2;
                            ?>
                            <div id="tab2" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                <div class="section-header" data-tab="2">
                                    <div>
                                        <div class="section-title"><?php echo JText::_("LNG_SECTION_2") ?></div>
                                        <div class="section-title-description"><?php echo JText::_("LNG_SECTION_2_DESCRIPTION") ?></div>
                                    </div>
                                    <div class="section-toolbar" style="display:none">
                                        <button type="button" class="btn btn-link" onclick="cancel(event, 2)">
                                            <?php echo JText::_("LNG_CANCEL") ?>
                                        </button>
                                        <button type="button" class="btn-action" onclick="saveFormAjax(event,'location');">
                                            <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                        </button>
                                    </div>
                                </div>
                                <div id="edit-tab2" class="edit-tab">
                                    <div class="edit-section">
                                        <div class="edit-section-limit">
                                            <div class="address-container">
                                                <?php FormService::renderAddressFields($attributeConfig, $this->item); ?>
                                                <?php if (!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP, $this->item->package->features)) { ?>
                                                    <?php if ($attributeConfig["map"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                        <div class="row pb-2">
                                                            <div class="col-md">
                                                                <div>
                                                                    <label for="latitude"><?php echo JText::_('LNG_LATITUDE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["map"]) ?></label>
                                                                    <p class="small"><?php echo JText::_('LNG_MAP_INFO') ?></p>
                                                                    <input class="form-control validate[custom[number]] <?php echo $attributeConfig["map"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="latitude" id="latitude" value="<?php echo $this->item->latitude ?>" maxlength="45">
                                                                </div>
                                                            </div>
                                                            <div class="col-md">
                                                                <div>
                                                                    <label for="longitude"><?php echo JText::_('LNG_LONGITUDE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["map"]) ?></label>
                                                                    <p class="small"><?php echo JText::_('LNG_MAP_INFO') ?></p>
                                                                    <input class="form-control validate[custom[number]] <?php echo $attributeConfig["map"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="longitude" id="longitude" value="<?php echo $this->item->longitude ?>" maxlength="45">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            <?php if (!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP, $this->item->package->features)) { ?>
                                                <div id="map-container">
                                                    <div id="company-map">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-footer">
                                    <div class="section-toolbar">
                                        <button type="button" class="btn btn-link" onclick="cancel(event, 2)">
                                            <?php echo JText::_("LNG_CANCEL") ?>
                                        </button>
                                        <button type="button" class="btn-action" onclick="saveFormAjax(event,'location');">
                                            <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            

                            <?php if ($showLocation) {
                                    $presentTabs[] = 3;
                            ?>
                                <div id="tab3" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                    <div class="section-header" data-tab="3">
                                        <div>
                                            <div class="section-title"><?php echo JText::_("LNG_SECTION_3") ?></div>
                                            <div class="section-title-description"><?php echo JText::_("LNG_SECTION_3_DESCRIPTION") ?></div>
                                        </div>
                                        <div class="section-toolbar" style="display:none">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 3)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'services');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="edit-tab3" class="edit-tab">
                                        <div class="edit-section">
                                            <div class="edit-section-limit">
                                                <div class="form-container">
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <?php if ($attributeConfig["type"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                <?php $adminTypes = array(); ?>
                                                                <div class="col-md">
                                                                    <div class="form-group">
                                                                        <label for="companyTypes"><?php echo JText::_('LNG_COMPANY_TYPE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["type"]) ?></label>
                                                                        <select data-placeholder="<?php echo JText::_("LNG_SELECT_COMPANYTYPE") ?>" class="form-control <?php echo $attributeConfig["type"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> select" name="typeId[]" id="companyTypes">
                                                                            <option value=""><?php echo JText::_("LNG_SELECT_COMPANYTYPE") ?></option>
                                                                            <?php
                                                                            foreach ($this->item->types as $type) {
                                                                                if (($type->only_for_admin == 1) && isset($isProfile)) {
                                                                                    $selected = false;
                                                                                    foreach ($this->item->typeId as $sType) {
                                                                                        if ($sType == $type->id) {
                                                                                            $selected = true;
                                                                                        }
                                                                                    }
                                                                                    if ($selected) {
                                                                                        $adminTypes[] = $type->id;
                                                                                    }
                                                                                    continue;
                                                                                }
                                                                                $selected = false;
                                                                                foreach ($this->item->typeId as $sType) {
                                                                                    if ($sType == $type->id)
                                                                                        $selected = true;
                                                                                } ?>
                                                                                <option <?php echo $selected ? "selected" : "" ?> value='<?php echo $type->id ?>'>
                                                                                    <?php echo $type->name ?>
                                                                                </option>
                                                                            <?php
                                                                            } ?>
                                                                        </select>
                                                                        <input type="hidden" name="admin_types" value="<?php echo !empty($adminTypes) ? implode(",", $adminTypes) : "" ?>" />
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md">
                                                                <div id="category-container" class="category-container">
                                                                    <?php foreach($this->item->master_categories as $i=>$masterCategory){ ?>
                                                                        <div class="category-wrapper" style="display:none">
                                                                            <div class="form-group">
                                                                                <label for="category"><?php echo JText::_('LNG_MASTER_CATEGORY'); ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                                                                <select name="master-category[]" id="master-<?php echo $i?>"  data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="master-category" onchange="loadCategories(jQuery(this))">
                                                                                    <option value=""><?php echo JText::_("LNG_SELECT_CAT") ?></option>
                                                                                    <?php echo JHtml::_('select.options', $this->masterCategoriesOptions, 'value', 'text', $masterCategory); ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="category"><?php echo JText::_('LNG_CATEGORIES'); ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                                                                <select name="selectedSubcategories[]" id="category-<?php echo $i?>"  data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="subcategory" multiple>
                                                                                    <?php foreach($this->categoryOptions as $option){ ?>
                                                                                        <?php if($option->parent_id == $masterCategory){?>
                                                                                            <option <?php echo in_array($option->value, $this->item->selCats)?"selected":"" ?>  value="<?php echo $option->value ?>"><?php echo $option->text ?></option>
                                                                                        <?php } ?>
                                                                                    <?php } ?>    
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="category-wrapper">
                                                                            <div class="form-group">
                                                                                <label for="category"><?php echo JText::_('LNG_MASTER_CATEGORY'); ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                                                                <select name="master-category[]" id="master-<?php echo $i?>"  data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="master-category chosen-select" onchange="loadCategories(jQuery(this))">
                                                                                    <option value=""><?php echo JText::_("LNG_SELECT_CAT") ?></option>
                                                                                    <?php echo JHtml::_('select.options', $this->masterCategoriesOptions, 'value', 'text', $masterCategory); ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="category"><?php echo JText::_('LNG_CATEGORIES'); ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                                                                <select name="selectedSubcategories[]" id="category-<?php echo $i?>"  data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="subcategory chosen-select" multiple>
                                                                                    <?php foreach($this->categoryOptions as $option){ ?>
                                                                                        <?php if($option->parent_id == $masterCategory){?>
                                                                                            <option <?php echo in_array($option->value, $this->item->selCats)? "selected":"" ?>  value="<?php echo $option->value ?>"><?php echo $option->text ?></option>
                                                                                        <?php } ?>
                                                                                    <?php } ?>    
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    <?php } ?>
                                                                </div>
                                                                <input type="hidden" name="category-index" id="category-index" value="<?php echo count($this->item->master_categories) ?>" />
                                                                <a href="javascript:addCategory()">+ <?php echo JText::_("LNG_ADD_CATEGORY") ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-footer">
                                        <div class="section-toolbar">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 3)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'services');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ((($attributeConfig["mobile_phone"] != ATTRIBUTE_NOT_SHOW ||
                                    $attributeConfig["phone"] != ATTRIBUTE_NOT_SHOW ||
                                    $attributeConfig["fax"] != ATTRIBUTE_NOT_SHOW) && (!$enablePackages || isset($this->item->package->features) && in_array(PHONE, $this->item->package->features)))
                                || $attributeConfig["email"] != ATTRIBUTE_NOT_SHOW
                                || $attributeConfig["contact_person"] != ATTRIBUTE_NOT_SHOW
                                || ($attributeConfig["business_team"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(TEAM_FEATURE, $this->item->package->features)))
                                || ($attributeConfig["social_networks"] != ATTRIBUTE_NOT_SHOW  && (!$enablePackages || isset($this->item->package->features) && in_array(SOCIAL_NETWORKS, $this->item->package->features)))
                            ) {
                                $presentTabs[] = 4;
                            ?>
                                <div id="tab4" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                    <div class="section-header" data-tab="4">
                                        <div>
                                            <div class="section-title"><?php echo JText::_("LNG_SECTION_4") ?></div>
                                            <div class="section-title-description"><?php echo JText::_("LNG_SECTION_4_DESCRIPTION") ?></div>
                                        </div>
                                        <div class="section-toolbar" style="display:none">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 4)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'service_area');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="edit-tab4" class="edit-tab">
                                        <div class="edit-section-limit">
                                            <div class="edit-section">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-footer">
                                        <div class="section-toolbar">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 4)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'service_area');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="additional-options"><?php echo JText::_("LNG_ADDITIONAL_OPTIONS") ?></div>

                            <?php if (($attributeConfig["logo"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(SHOW_COMPANY_LOGO, $this->item->package->features)))
                                || ($attributeConfig["pictures"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(IMAGE_UPLOAD, $this->item->package->features)))
                                || ($attributeConfig["video"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(VIDEOS, $this->item->package->features)))
                                || ($attributeConfig["attachments"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(ATTACHMENTS, $this->item->package->features)))
                                || ($attributeConfig["testimonials"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(TESTIMONIALS, $this->item->package->features)))
                                || ($attributeConfig["attachments"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(SOUNDS_FEATURE, $this->item->package->features)))
                                || ($attributeConfig["ad_images"] != ATTRIBUTE_NOT_SHOW)
                            ) {
                                $presentTabs[] = 5;
                            ?>
                                <div id="tab5" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                    <div class="section-header" data-tab="5">
                                        <div>
                                            <div class="section-title"><?php echo JText::_("LNG_SECTION_5") ?></div>
                                            <div class="section-title-description"><?php echo JText::_("LNG_SECTION_5_DESCRIPTION") ?></div>
                                        </div>
                                        <div class="section-toolbar" style="display:none">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 5)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'media');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="edit-tab5" class="edit-tab">
                                        <?php if ($attributeConfig["pictures"] != ATTRIBUTE_NOT_SHOW) {
                                            if (!$enablePackages || isset($this->item->package->features) && in_array(IMAGE_UPLOAD, $this->item->package->features)) { ?>
                                                <div class="edit-section">
                                                    <label> <?php echo JText::_('LNG_COMPANY_PICTURES'); ?> </label>
                                                    <a class="btn btn-outline-danger" id="remove-pictures" href="javascript:void(0);" onclick="uploadInstance.removeAllPicture()"><?php echo JText::_('LNG_DELETE_ALL') ?></a>
                                                    <input type='button' name='btn_removefile' id='btn_removefile' value='x' style='display:none'>
                                                    <input type='hidden' name='crt_pos' id='crt_pos' value=''>
                                                    <input type='hidden' name='crt_path' id='crt_path' value=''>

                                                    <div class="jupload" id="pictures-list">
                                                        <div class="jupload-header">
                                                            <div class="jupload-header-title"></div>
                                                            <div class="jupload-header-desc"></div>
                                                        </div>
                                                        <div class="jupload-body">
                                                            <ul id="sortable" class="jbd-item-list">
                                                                <?php
                                                                if (!empty($this->item->pictures)) {
                                                                    foreach ($this->item->pictures as $picture) { ?>
                                                                        <li class="jbd-item" id="jbd-item-<?php echo $picture['id'] ?>">
                                                                            <div class="jupload-files">
                                                                                <div class="jupload-files-img">
                                                                                    <img src='<?php echo BD_PICTURES_PATH . $picture['picture_path'] ?>'>
                                                                                </div>
                                                                                <div class="jupload-files-info">
                                                                                    <div class="jupload-filename">
                                                                                        <p><?php echo substr(basename($picture['picture_path']), 0, 30) ?></p>
                                                                                        <input id="jupload-filename-<?php echo $picture['id'] ?>" type="text" name="picture_title[]" value="<?php echo $picture['picture_title'] ?>" maxlength="255" placeholder="<?php echo JText::_('LNG_TITLE') ?>">
                                                                                        <input id="jupload-filename-<?php echo $picture['id'] ?>" type="text" name="picture_info[]" value="<?php echo $picture['picture_info'] ?>" maxlength="255" placeholder="<?php echo JText::_('LNG_DESCRIPTION') ?>">
                                                                                    </div>
                                                                                    <div class="jupload-actions jbd-item-actions">
                                                                                        <label for="jupload-filename-<?php echo $picture['id'] ?>">
                                                                                            <i class="la la-pencil"></i>
                                                                                        </label>

                                                                                        <input type="hidden" name="picture_enable[]" id="picture_enable_<?php echo $picture['id'] ?>" value="<?php echo $picture['picture_enable'] ?>" />
                                                                                        <input type='hidden' name='picture_path[]' id='picture_path_<?php echo $picture['id'] ?>' value='<?php echo $this->escape($picture['picture_path']) ?>' />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </li>

                                                                <?php
                                                                    }
                                                                } ?>
                                                            </ul>

                                                            <div class="dropzone dropzone-previews container-fluid" id="file-upload">
                                                                <div id="actions" style="margin-left:-15px;" class="row">
                                                                    <div class="col d-flex justify-content-center">
                                                                        <!-- The fileinput-button span is used to style the file input field as button -->
                                                                        <span class="btn btn-success fileinput-button dz-clickable mr-1">
                                                                            <i class="glyphicon glyphicon-plus"></i>
                                                                            <span><?php echo JText::_('LNG_ADD_FILES'); ?></span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                        
                                        <?php if ($attributeConfig["video"] != ATTRIBUTE_NOT_SHOW) {
                                            if (!$enablePackages || isset($this->item->package->features) && in_array(VIDEOS, $this->item->package->features)) { ?>
                                                <div class="edit-section">
                                                    <div class="edit-section-limit">
                                                        <label><?php echo JText::_('LNG_COMPANY_VIDEOS'); ?></label>
                                                        <div class="form-box">
                                                            <div id="video-container">
                                                                <input type="hidden" name="videos-included" value="1" />
                                                                <?php
                                                                if (count($this->item->videos) == 0) { ?>
                                                                    <div class="video-item" id="detailBox0">
                                                                        <?php if ($attributeConfig["video"] == ATTRIBUTE_MANDATORY) { ?>
                                                                            <div class="form-detail req"> <?php echo JBusinessUtil::showMandatory($attributeConfig["video"]) ?></div>
                                                                        <?php } ?>
                                                                        <label for="video1"><?php echo JText::_('LNG_VIDEO') ?></label>
                                                                        <div class="input-group">
                                                                            <input type="text" maxlength="245" name="title[]" id="0" placeholder="<?php echo JText::_('LNG_VIDEO_TITLE'); ?>" class="form-control w-auto mr-2 <?php echo $attributeConfig["video"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> " value="" />
                                                                            <input type="text" maxlength="245" name="videos[]" id="0" placeholder="<?php echo JText::_('LNG_VIDEO_URL'); ?>" class="form-control w-auto <?php echo $attributeConfig["video"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> " value="" />
                                                                            <div class="input-group-append">
                                                                                <button class="btn btn-secondary" onclick="uploadInstance.removeRow('detailBox0');">
                                                                                    <i class="la la-lg la-remove"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                                } ?>

                                                                <?php $index = 0;
                                                                if (count($this->item->videos) > 0)
                                                                    foreach ($this->item->videos as $video) { ?>
                                                                    <div class="video-item" id="detailBox<?php echo $index ?>">
                                                                        <label for="<?php echo $video->id ?>"><?php echo JText::_('LNG_VIDEO') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["video"]) ?></label>
                                                                        <div class="input-group">
                                                                            <input type="text" maxlength="245" name="title[]" id="<?php echo $video->id ?>" placeholder="<?php echo JText::_('LNG_VIDEO_TITLE'); ?>" class="form-control w-auto mr-2 <?php echo $attributeConfig["video"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> " value="<?php echo $this->escape($video->title) ?>" />
                                                                            <input type="text" maxlength="245" name="videos[]" id="<?php echo $video->id ?>" placeholder="<?php echo JText::_('LNG_VIDEO_URL'); ?>" class="form-control w-auto <?php echo $attributeConfig["video"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> " value="<?php echo $this->escape($video->url) ?>" />
                                                                            <div class="input-group-append">
                                                                                <button class="input-group-text btn-secondary" onclick="uploadInstance.removeRow('detailBox<?php echo $index++; ?>');">
                                                                                    <i class="la la-lg la-remove"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                                    } ?>
                                                            </div>
                                                            <a id="add-video" href="javascript:void(0);" onclick="uploadInstance.addVideo('withTitle')"><?php echo JText::_('LNG_ADD_VIDEO') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if (
                                            $this->appSettings->enable_attachments && ((!$enablePackages || isset($this->item->package->features) && in_array(ATTACHMENTS, $this->item->package->features)))
                                            && $attributeConfig["attachments"] != ATTRIBUTE_NOT_SHOW
                                        ) { ?>
                                            <div class="edit-section">
                                                <div class="edit-section-limit">
                                                    <label> <?php echo JText::_('LNG_ATTACHMENTS'); ?></h2>
                                                    <div class="form-box">
                                                        <div class="form-group">
                                                            <input type='button' name='btn_removefile_at' id='btn_removefile_at' value='x' style='display:none'>
                                                            <input type='hidden' name='crt_pos_a' id='crt_pos_a' value=''>
                                                            <input type='hidden' name='crt_path_a' id='crt_path_a' value=''>
                                                            <input type="hidden" name="process-attachments" value="1">

                                                            <div class="jupload" id="attachment-list">
                                                                <div class="jupload-header">
                                                                    <div class="jupload-header-title"></div>
                                                                    <div class="jupload-header-desc"></div>
                                                                </div>
                                                                <div class="jupload-body">
                                                                    <ul id="sortable-attachment" class="jbd-item-list">
                                                                        <?php
                                                                        if (!empty($this->item->attachments)) {
                                                                            foreach ($this->item->attachments as $attachment) { ?>

                                                                                <li class="jbd-item" id="jbd-item-<?php echo $attachment->id ?>">
                                                                                    <div class="jupload-files">
                                                                                        <div class="jupload-files-img">
                                                                                            <i class="la la-file"></i>
                                                                                        </div>
                                                                                        <div class="jupload-files-info">
                                                                                            <div class="jupload-filename">
                                                                                                <p><?php echo basename($attachment->path) ?></p>
                                                                                                <input id="jupload-filename-<?php echo $attachment->id ?>" type="text" name="attachment_name[]" maxlength="100" value="<?php echo $attachment->name ?>">
                                                                                            </div>
                                                                                            <div class="jupload-actions jbd-item-actions">
                                                                                                <label for="jupload-filename-<?php echo $attachment->id ?>">
                                                                                                    <i class="la la-pencil"></i>
                                                                                                </label>

                                                                                                <input type="hidden" name="attachment_status[]" id="attachment_status_<?php echo $attachment->id ?>" value="<?php echo $attachment->status ?>" />
                                                                                                <input type='hidden' name='attachment_path[]' id='attachment_path_<?php echo $attachment->id ?>' value='<?php echo $this->escape($attachment->path) ?>' />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>

                                                                        <?php
                                                                            }
                                                                        } ?>
                                                                    </ul>

                                                                    <fieldset class="jupload-field">
                                                                        <p>
                                                                            <i class="la la-cloud-upload la-2x"></i>
                                                                            <label for="multiFileUploader"><?php echo JText::_('LNG_CHOOSE_FILE') ?></label>
                                                                        </p>
                                                                        <input class="jupload-field-input" type="file" name="uploadAttachment" id="multiFileUploader">
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="section-footer">
                                        <div class="section-toolbar">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 5)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'media');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            } ?>

                            <?php if ((!isset($isProfile) && $attributeConfig["related_listing"] != ATTRIBUTE_NOT_SHOW  && (!$enablePackages || isset($this->item->package->features) && in_array(RELATED_COMPANIES, $this->item->package->features)))
                                || ($attributeConfig["custom_tab"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(CUSTOM_TAB, $this->item->package->features)))
                            ) {
                                $presentTabs[] = 6;
                            ?>
                                <div id="tab6" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                    <div class="section-header" data-tab="6">
                                        <div>
                                            <div class="section-title"><?php echo JText::_("LNG_SECTION_6") ?></div>
                                            <div class="section-title-description"><?php echo JText::_("LNG_SECTION_6_DESCRIPTION") ?></div>
                                        </div>
                                        <div class="section-toolbar" style="display:none">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 6)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'projects');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="section-footer">
                                        <div class="section-toolbar">
                                            <div class="section-toolbar">
                                                <button type="button" class="btn btn-link" onclick="cancel(event, 6)">
                                                    <?php echo JText::_("LNG_CANCEL") ?>
                                                </button>
                                                <button type="button" class="btn-action" onclick="saveFormAjax(event,'projects');">
                                                    <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php
                                if ($attributeConfig["social_networks"] != ATTRIBUTE_NOT_SHOW) {
                                    if (!$enablePackages || isset($this->item->package->features) && in_array(SOCIAL_NETWORKS, $this->item->package->features)) { ?>

                                        <div id="tab7" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                            <div class="section-header" data-tab="7">
                                                <div>
                                                    <div class="section-title"><?php echo JText::_("LNG_SECTION_7") ?></div>
                                                    <div class="section-title-description"><?php echo JText::_("LNG_SECTION_7_DESCRIPTION") ?></div>
                                                </div>
                                                <div class="section-toolbar" style="display:none">
                                                    <button type="button" class="btn btn-link" onclick="cancel(event, 7)">
                                                        <?php echo JText::_("LNG_CANCEL") ?>
                                                    </button>
                                                    <button type="button" class="btn-action" onclick="saveFormAjax(event,'social');">
                                                        <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="edit-tab7" class="edit-tab">
                                                <div class="edit-section">
                                                    <div class="edit-section-limit">
                                                        <label> <?php echo JText::_('LNG_SOCIAL_NETWORKS'); ?></label>
                                                        <div class="form-container">
                                                            <div class="row">
                                                                <?php if ($attributeConfig["facebook"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="facebook"><?php echo JText::_('LNG_FACEBOOK') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="facebook" id="facebook" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->facebook); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($attributeConfig["twitter"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="twitter"><?php echo JText::_('LNG_TWITTER') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="twitter" id="twitter" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->twitter); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="row">
                                                                <?php if ($attributeConfig["linkedin"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="linkedin"><?php echo JText::_('LNG_LINKEDIN') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="linkedin" id="linkedin" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->linkedin); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($attributeConfig["skype"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="skype"><?php echo JText::_('LNG_SKYPE_ID') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="skype" id="skype" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->skype); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="row">
                                                                <?php if ($attributeConfig["youtube"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="youtube"><?php echo JText::_('LNG_YOUTUBE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="youtube" id="youtube" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->youtube); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($attributeConfig["instagram"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="youtube"><?php echo JText::_('LNG_INSTAGRAM') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="instagram" id="instagram" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->instagram); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="row">
                                                                <?php if ($attributeConfig["pinterest"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="pinterest"><?php echo JText::_('LNG_PINTEREST') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="pinterest" id="pinterest" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->pinterest); ?>" maxlength="150">
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($attributeConfig["whatsapp"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                                    <div class="col-md">
                                                                        <div class="form-group">
                                                                            <label for="whatsapp"><?php echo JText::_('LNG_WHATSAPP') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                                            <input type="text" name="whatsapp" id="whatsapp" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->item->whatsapp ?>" maxlength="150">
                                                                            <span><?php echo JText::_('LNG_WHATSAPP_TEXT'); ?></span>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="section-footer">
                                                <div class="section-toolbar">
                                                    <button type="button" class="btn btn-link" onclick="cancel(event, 7)">
                                                        <?php echo JText::_("LNG_CANCEL") ?>
                                                    </button>
                                                    <button type="button" class="btn-action" onclick="saveFormAjax(event,'social');">
                                                        <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                <?php } ?>
                            <?php } ?>

                            <?php if (!empty($this->membershipOptions) && (!$enablePackages || isset($this->item->package->features) && in_array(MEMBERSHIPS, $this->item->package->features))) { ?>
                                <div id="tab8" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>">
                                    <div class="section-header" data-tab="8">
                                        <div>
                                            <div class="section-title"><?php echo JText::_("LNG_SECTION_8") ?></div>
                                            <div class="section-title-description"><?php echo JText::_("LNG_SECTION_8_DESCRIPTION") ?></div>
                                        </div>
                                        <div class="section-toolbar" style="display:none">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 8)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'memberships');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="edit-tab8" class="edit-tab">
                                        <div class="edit-section">
                                            <div class="edit-section-limit">
                                                <div class="form-container">
                                                    <div class="form-group">
                                                        <label> <?php echo JText::_('LNG_MEMBERSHIPS'); ?></label>
                                                        <select name="selectedMemberships[]" id="selectedMemberships" data-placeholder="<?php echo JText::_("LNG_SELECT_MEMBERSHIP") ?>" class="form-control input-medium chosen-select-membership" multiple>
                                                            <?php echo JHtml::_('select.options', $this->membershipOptions, 'value', 'text', $this->item->selMembership); ?>
                                                        </select>
                                                        <a href="javascript:jbdUtils.uncheckAll('selectedMemberships')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-footer">
                                        <div class="section-toolbar">
                                            <button type="button" class="btn btn-link" onclick="cancel(event, 8)">
                                                <?php echo JText::_("LNG_CANCEL") ?>
                                            </button>
                                            <button type="button" class="btn-action" onclick="saveFormAjax(event,'memberships');">
                                                <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-12 col-lg-4 ml-lg-auto">
                    <?php if (!isset($isProfile)) { ?>
                        <fieldset class="boxed">
                            <div class="form-box">
                                <div class="form-group" style="line-height: 22px;margin: 0">
                                    <span class="badge badge-success"><i class="la la-exclamation"></i></span> <?php echo JText::_('LNG_MANDATORY_NOTICE', true) ?>
                                </div>
                            </div>
                        </fieldset>
                    <?php } ?>

                    <div class="metainfo-container">
                        <?php if (!isset($isProfile)) { ?>
                            <fieldset class="boxed approved-label">
                                <div class="form-container">
                                    <div><label id="approved" for="approved" title=""><?php echo JText::_('LNG_APPROVED'); ?></label></div>
                                    <div class="form-group">
                                        <fieldset id="approved" class="radio btn-group btn-group-yesno">
                                            <label class="btn" id="label_approved1" for="approved1" onclick="jbdListings.hideDisapprovalBox()"><?php echo JTEXT::_("LNG_APPROVED") ?></label>
                                            <input type="radio" class="" onclick="" name="approved" id="approved1" value="<?php echo COMPANY_STATUS_APPROVED  ?>" <?php echo $this->item->approved == COMPANY_STATUS_APPROVED ? 'checked="checked"' : "" ?> />
                                            <input type="radio" class="" onclick="" name="approved" id="approved2" value="<?php echo COMPANY_STATUS_DISAPPROVED  ?>" <?php echo $this->item->approved == COMPANY_STATUS_DISAPPROVED ? 'checked="checked"' : "" ?> />
                                            <label class="btn <?php 'btn-danger' ?>" id="label_approved2" for="approved2" onclick="jbdListings.showDisapprovalBox()"><?php echo JText::_('LNG_DISAPPROVED') ?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <?php if ($this->appSettings->listing_auto_save) { ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <p><strong><?php echo JText::_('LNG_LAST_AUTO_SAVED'); ?></strong> <span id="last_saved"><?php echo JText::_("LNG_NOT_SAVED") ?></span> </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </fieldset>

                            <fieldset class="boxed" style="display: none" id="disapprovalBox">
                                <h2> <?php echo JText::_('LNG_DISAPPROVAL_REASON'); ?></h2>
                                <p> <?php echo JText::_('LNG_DISAPPROVAL_REASON_TEXT'); ?>.</p>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="disapproval_text"><?php echo JText::_('LNG_DISAPPROVAL_REASON') ?></label>
                                        <textarea class="form-control" name="disapproval_text" id="disapproval_text" rows="4" maxLength="255"><?php echo $this->item->disapproval_text ?></textarea>
                                    </div>
                                </div>
                            </fieldset>

                        <?php } ?>

                        <?php if (isset($this->claimDetails) && !isset($isProfile)) {
                            if ($this->claimDetails->status != 1) { ?>
                                <fieldset class="boxed">
                                    <div id="claim-details" class="claim-details-wrapper">
                                        <div class="claim-details">
                                            <h3><?php echo JText::_("LNG_CLAIM_PENDING") ?> <span class="badge badge-pill badge-warning"><i class="la la-exclamation"></i></span> </h3>
                                        </div>
                                    </div>
                                    <div id="claim-details" class="claim-details-wrapper">
                                        <div class="claim-details">
                                            <p><?php echo JText::_("LNG_CLAIM_DETAILS_TEXT") ?></p>
                                            <table>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_FIRST_NAME') ?></th>
                                                    <td><?php echo $this->claimDetails->firstName ?></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_LAST_NAME') ?></th>
                                                    <td><?php echo $this->claimDetails->lastName ?></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_PHONE') ?></th>
                                                    <td><?php echo $this->claimDetails->phone ?></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></th>
                                                    <td><?php echo $this->claimDetails->email ?></td>
                                                </tr>
                                                <?php $claimUser = JBusinessUtil::getUser($this->item->userId); ?>
                                                <tr>
                                                    <td colspan="2">
                                                        <p><?php echo JText::_("LNG_USER_DETAILS_TXT") ?></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_FIRST_NAME') ?></th>
                                                    <td><?php echo $claimUser->name ?></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_USERNAME') ?></th>
                                                    <td><?php echo $claimUser->display_name ?></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo JText::_('LNG_EMAIL') ?></th>
                                                    <td><?php echo $claimUser->email ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($attributeConfig["metadata_information"] != ATTRIBUTE_NOT_SHOW  && (!isset($isProfile) || isset($isProfile) && $this->appSettings->front_end_meta_data && !$isNew)) { ?>
                            <fieldset class="boxed">
                                <h3> <?php echo JText::_('LNG_METADATA_INFORMATION'); ?></h3>
                                <p> <?php echo JText::_('LNG_METADATA_INFORMATION_TEXT'); ?>.</p>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="meta_title"><?php echo JText::_('LNG_META_TITLE') ?></label>
                                        <?php
                                        if ($this->appSettings->enable_multilingual) {
                                            echo $jbdTabs->startTabSet('tab_group_meta_title');
                                            foreach ($this->languages as $k => $lng) {
                                                echo $jbdTabs->addTab('tab_group_meta_title', 'tab-' . $lng, $k);
                                                $langContent = isset($this->translationsMeta[$lng . "_name"]) ? $this->translationsMeta[$lng . "_name"] : "";
                                                if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                                    $langContent = $this->item->meta_title;
                                                }
                                                $langContent = $this->escape($langContent);
                                                echo "<input type='text' name='meta_title_$lng' id='meta_title_$lng' class='form-control' value=\"" . stripslashes($langContent) . "\"  maxLength='100'>";
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        } else { ?>
                                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php echo $this->escape($this->item->meta_title) ?>" maxLength="255">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="meta_description"><?php echo JText::_('LNG_META_DESCRIPTION') ?></label>
                                        <?php
                                        if ($this->appSettings->enable_multilingual) {
                                            echo $jbdTabs->startTabSet('tab_group_meta_description');
                                            foreach ($this->languages as $k => $lng) {
                                                echo $jbdTabs->addTab('tab_group_meta_description', 'tab-' . $lng, $k);
                                                $langContent = isset($this->translationsMeta[$lng]) ? $this->translationsMeta[$lng] : "";
                                                if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                                    $langContent = $this->item->meta_description;
                                                }
                                                $langContent = $this->escape($langContent);
                                                echo "<textarea name='meta_description_$lng' id='meta_description_$lng' rows='4' maxLength='255'>$langContent</textarea>";
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        } else { ?>
                                            <textarea class="form-control" name="meta_description" id="meta_description" rows="4" maxLength="255"><?php echo $this->item->meta_description ?></textarea>
                                        <?php } ?>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } elseif (isset($isProfile) && !$this->appSettings->front_end_meta_data) { ?>
                            <input type="hidden" name="meta_title" id="meta_title" value="<?php echo $this->item->meta_title ?>" />
                            <input type="hidden" name="meta_description" id="meta_description" value="<?php echo $this->item->meta_description ?>" />
                        <?php }  ?>

                        <?php if (!isset($isProfile)) {
                            if (!isset($this->item->userId)) {
                                $this->item->userId = 0;
                            }
                        ?>
                            <fieldset class="boxed">
                                <h3><?php echo JText::_('LNG_COMPANY_USER'); ?></h3>
                                <p>User information</p>
                                <div class="form-box">
                                    <div class="form-group">
                                        <?php JBusinessUtil::renderUserSelection($this->item->userId, "userId"); ?>
                                    </div>
                                </div>
                            </fieldset>

                            <?php if ($attributeConfig["publish_dates"] != ATTRIBUTE_NOT_SHOW) { ?>
                                <fieldset class="boxed">
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="publish_start_date"><?php echo JText::_('LNG_PUBLISH_START_DATE') ?> </label>
                                                <?php echo JHTML::_('calendar', $this->item->publish_start_date, 'publish_start_date', 'publish_start_date', $this->appSettings->calendarFormat, array('style' => 'display:inline;', 'class' => 'form-control calendar-date', 'size' => '10', 'maxlength' => '10')); ?>
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="publish_end_date"><?php echo JText::_('LNG_PUBLISH_END_DATE') ?> </label>
                                                <?php echo JHTML::_('calendar', $this->item->publish_end_date, 'publish_end_date', 'publish_end_date', $this->appSettings->calendarFormat, array('style' => 'display:inline;', 'class' => 'form-control calendar-date', 'size' => '10', 'maxlength' => '10')); ?>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php } ?>

                            <fieldset class="boxed">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="companyEditors"><?php echo JText::_('LNG_COMPANY_EDITORS') ?></label>
                                            <select data-placeholder="<?php echo JText::_("LNG_SELECT_COMPANY_EDITOR") ?>" class="form-control select chosen-select" multiple name="companyEditors[]" id="companyEditors">
                                                <?php echo JHtml::_('select.options', $this->item->editorOptions, 'value', 'name', $this->item->editors); ?>
                                            </select>
                                            <a href="javascript:jbdUtils.checkAll('companyEditors')"><?php echo JText::_("LNG_CHECK_ALL") ?></a> &nbsp;&nbsp;&nbsp; <a href="javascript:jbdUtils.uncheckAll('companyEditors')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <?php if(!empty($this->item->logs)){ ?>
                                <fieldset class="boxed">
                                    <div class="row item-log">
                                        <div class="col-md-12">
                                            <h3><?php echo JText::_('LNG_ITEM_LOGS'); ?></h3>
                                            <table class="table">
                                                <tr>
                                                    <th><?php echo JText::_("LNG_USER") ?></th>
                                                    <th><?php echo JText::_("LNG_DATE") ?></th>
                                                    <th><?php echo JText::_("LNG_ACTION") ?></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <?php foreach($this->item->logs as $log){ ?>
                                                    <tr>
                                                        <td><?php echo $log->name ?></td>
                                                        <td><?php echo JBusinessUtil::getDateGeneralFormatWithTime($log->date) ?></td>
                                                        <td><?php echo JBusinessUtil::renderLogAction($log->action) ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="id" id="company_id" value="<?php echo $this->item->id ?>" />
        <input type="hidden" name="exists" id="exists" value="" />
        <input type="hidden" name="deleted" id="deleted" value="" />
        <input type="hidden" name="extend_package_id" id="extend_package_id" value="" />
        <input type="hidden" name="identifier" id="identifier" value="<?php echo $uniqueCode; ?>" />
        <input type="hidden" name="last_updates" id="last_updated" value="" />
        <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo $this->item->userId ?>" />
        <input type="hidden" name="edit_attributes" id="edit_attributes" value="<?php echo $lockAttributes ?>" />
        <input type="hidden" name="user-timezone" id="user-timezone" value="" />
        <input type="hidden" name="only_contribute" id="only_contribute" value="0" />
        <input type="hidden" name="contribute_editor" id="contribute_editor" value="" />
        <input type="hidden" name="save_section" id="save_section" value="" />
        <input type="hidden" name="field_modified" id="field_modified" value="" />
        
        <?php if (isset($isProfile)) { ?>
            <?php $userId = $isNew ? $user->ID : $this->item->userId; ?>
            <input type="hidden" id="userId" name="userId" value="<?php echo $this->item->userId ? $this->item->userId : $userId ?>" />
            <input type="hidden" name="view" id="view" value="managecompany" />
        <?php } else { ?>
            <input type="hidden" name="view" id="view" value="company" />
        <?php  } ?>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>

<div id="location-dialog" style="display:none" class="jbd-container">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_COMPANY_LOCATION') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <a id="locationD" name="locationD"></a>
            <iframe id="location-frame" height="650" width="600" src="about:blank"></iframe>
        </div>
    </div>
</div>

<div id="form-container-hd" style="display:none">
</div>

<!-- Modal -->
<div id="business-owner-dlg" class="jbd-container" style="display: none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_OWNER_ASSOCIATION'); ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p>
                <?php echo JText::_('LNG_OWNER_ASSOCIATION_TEXT'); ?>
            </p>
            <div class="row-fluid">
                <button type="button" class="btn btn-success owner-confirm-btn" onclick="saveListing('save');">
                    <span class="ui-button-text"><i class="la la-user"></i> <?php echo JText::_("LNG_I_AM_OWNER") ?></span>
                </button>
                <button type="button" class="btn btn-dark" onclick="resetOwner()">
                    <span class="ui-button-text"><i class="la la-thumbs-up"></i> <?php echo JText::_("LNG_CONTRIBUTE") ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="zipTemplate" style="display: none">
    <div class="mb-2 d-flex justify-content-between" >
        <input class="mr-2" type="text" id="zip-autocomplete-${id}" placeholder="Enter ZIP code (autocomplete)" onFocus=""></input>
        <input class="mr-2" type="text" name="zip_code[]" id="zip_input_${id}"  placeholder="Zip code"></input>
        <input class="mr-2" type="text" name="latitudes[]" id="lat_input_${id}"  placeholder="Latitude"></input>
        <input class="mr-2" type="text" name="longitudes[]" id="long_input_${id}"   placeholder="Longitude"></input>
        <input type="hidden" name="zip_code_id[]"  ></input>
        <a href="javascript:void(0);" onclick="jQuery(this).parent().remove()" class="btn btn-danger ml-2" ><i class="la la-trash"></i></a>
    </div>
</div>

<?php require "edit_utils.php" ?>