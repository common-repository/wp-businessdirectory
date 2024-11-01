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
$showTabs  = $this->appSettings->edit_form_mode == 1 ? true : false;
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

$maxDescriptionLength = !empty($this->item->package->max_description_length) ? $this->item->package->max_description_length : $this->appSettings->max_description_length;
$maxShortDescriptionLength = $this->appSettings->max_short_description_length;
$maxSloganLength = $this->appSettings->max_slogan_length;

$maxActivityCities = 0; //isset($this->item->package)?$this->item->package->max_activity_cities :$this->appSettings->max_activity_cities;
$maxActivityRegions = 0; //isset($this->item->package)?$this->item->package->max_activity_regions :$this->appSettings->max_activity_regions;
$maxActivityCountries = 0; //isset($this->item->package)?$this->item->package->max_activity_countries :$this->appSettings->max_activity_countries;

$splitForm = $this->appSettings->split_edit_form && $showSteps;
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

            if (task == 'company.cancel' || task == 'company.aproveClaim' || task == 'company.disaproveClaim' || jbdUtils.validateTabs(true, <?php echo $attributeConfig["description"] && isset($isProfile) == ATTRIBUTE_MANDATORY ? 'true' : 'false' ?>)) {
                JBD.submitform(task, document.getElementById('item-form'));
            }
            jQuery("#item-form").validationEngine('attach');
        }
    });
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container <?php echo $showTabs ? "tab-edit" : "one-page-edit" ?>">
    <?php if (isset($isProfile) && !$isNew) { ?>
        <div class="d-flex justify-content-between">
            <div class="button-row">
                <button type="button" class="btn btn-success button-save" onclick="saveCompanyInformation('apply');">
                    <span class="ui-button-text"><i class="la la-edit"></i> <?php echo JText::_("LNG_SAVE") ?></span>
                </button>
                <button type="button" class="btn btn-success button-close" onclick="saveCompanyInformation('save');">
                    <span class="ui-button-text"><i class="la la-check"></i> <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?></span>
                </button>
                <?php if (!$isNew) { ?>
                    <button id="duplicate-btn" type="button" class="btn btn-primary button-save" onclick="saveCompanyInformation('duplicate')">
                        <i class="icon-save-copy"></i> <?php echo JText::_("LNG_DUPLICATE") ?>
                    </button>
                <?php } ?>
                <button type="button" class="btn btn-dark button-cancel" onclick="cancel()">
                    <span class="ui-button-text"><i class="la la la-close"></i> <?php echo JText::_("LNG_CANCEL") ?></span>
                </button>
            </div>
            <?php if ($this->appSettings->listing_auto_save) { ?>
                <div class="justify-content-end">
                    <p><strong><?php echo JText::_('LNG_LAST_AUTO_SAVED'); ?></strong> <span id="last_saved"><?php echo JText::_("LNG_NOT_SAVED") ?></span></p>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal" enctype="multipart/form-data">
        <div id="form-mode-container" class="<?php echo $splitForm ? "basic":"advanced" ?>">
            <?php if($splitForm){ ?>
                <a class="form-switch advanced-switch" href="javascript:switchFormView('advanced')"><?php echo JText::_("LNG_SWITCH_TO_ADVANCED") ?></a>
                <a class="form-switch basic-switch" href="javascript:switchFormView('basic')" style="<?php echo !$showSteps ? 'display:none;' : '' ?>"><?php echo JText::_("LNG_SWITCH_TO_BASIC") ?></a>
            <?php } ?>
            <div class="row">
                <div class="<?php echo (!isset($isProfile) || isset($isProfile) && $this->appSettings->front_end_meta_data && !$isNew) ? "col-lg-8" : "col-md-12" ?>">
                    <?php if ($enablePackages && !$showSteps) { ?>
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
                            <div id="tab1" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?> col" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(1)"' : '' ?>>
                                <div class="process-tab-content">
                                    <i class="la la-info"></i>
                                    <span><?php echo JText::_("LNG_TAB_1") ?></span>
                                </div>
                            </div>

                            <?php if (
                                $attributeConfig["category"] != ATTRIBUTE_NOT_SHOW
                                || (!empty($this->membershipOptions) &&
                                    (!$enablePackages || isset($this->item->package->features) && in_array(MEMBERSHIPS, $this->item->package->features)))
                                || (!empty($this->item->customFields) && $this->item->containsCustomFields)
                                || (($attributeConfig["opening_hours"] != ATTRIBUTE_NOT_SHOW)
                                    && (!$enablePackages || isset($this->item->package->features) && in_array(OPENING_HOURS, $this->item->package->features)))
                            ) {
                                $presentTabs[] = 2;
                            ?>
                                <div id="tab2" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?> col" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(2)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <i class="la la-database"></i>
                                        <span><?php echo JText::_("LNG_TAB_2") ?></span>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($showLocation) {
                                $presentTabs[] = 3;
                            ?>
                                <div id="tab3" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?> col" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(3)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <i class="la la-map-marker"></i>
                                        <span><?php echo JText::_("LNG_TAB_3") ?></span>
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
                                <div id="tab4" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?> col" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(4)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <i class="la la-phone"></i>
                                        <span><?php echo JText::_("LNG_TAB_4") ?></span>
                                    </div>
                                </div>
                            <?php } ?>

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
                                <div id="tab5" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?> col" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(5)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <i class="la la-rss"></i>
                                        <span><?php echo JText::_("LNG_TAB_5") ?></span>
                                    </div>
                                </div>
                            <?php
                            } ?>

                            <?php if ((!isset($isProfile) && $attributeConfig["related_listing"] != ATTRIBUTE_NOT_SHOW  && (!$enablePackages || isset($this->item->package->features) && in_array(RELATED_COMPANIES, $this->item->package->features)))
                                || ($attributeConfig["custom_tab"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(CUSTOM_TAB, $this->item->package->features)))
                            ) {
                                $presentTabs[] = 6;
                            ?>
                                <div id="tab6" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?> col" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(6)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <i class="la la-list-alt"></i>
                                        <span><?php echo JText::_("LNG_TAB_6") ?></span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div id="edit-tab1" class="edit-tab">
                        <fieldset class="boxed">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <h2> <?php echo JText::_('LNG_COMPANY_DETAILS'); ?></h2>
                                        <p>
                                            <?php echo JText::_('LNG_DISPLAY_INFO_TXT'); ?><br />
                                            <span class="small"><?php echo JText::_('LNG_MANDATORY_FIELDS_INFO'); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-container">
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

                                <?php if ($attributeConfig["comercial_name"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="comercialName"><?php echo JText::_('LNG_COMPANY_COMERCIAL_NAME') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["comercial_name"]) ?></label>
                                                <input type="text" name="comercialName" id="comercialName" class="form-control <?php echo $attributeConfig["comercial_name"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->comercialName) ?>" maxlength="120">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($attributeConfig["tax_code"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["registration_code"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="row">
                                        <?php
                                        if ($attributeConfig["tax_code"] != ATTRIBUTE_NOT_SHOW) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="taxCode"><?php echo JText::_('LNG_TAX_CODE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["tax_code"]) ?></label>
                                                    <input type="text" name="taxCode" id="taxCode" class="form-control <?php echo $attributeConfig["tax_code"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->taxCode) ?>" maxlength="45">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($attributeConfig["registration_code"] != ATTRIBUTE_NOT_SHOW) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="registrationCode"><?php echo JText::_('LNG_REGISTRATION_CODE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["registration_code"]) ?></label>
                                                    <input type="text" name="registrationCode" id="registrationCode" class="form-control <?php echo $attributeConfig["registration_code"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->registrationCode) ?>" maxlength="20">
                                                    <span class="error_msg" id="frmRegistrationCode_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <?php if ($attributeConfig["website"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["type"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="row">
                                        <?php if ($attributeConfig["website"] != ATTRIBUTE_NOT_SHOW) { ?>
                                            <?php if (!$enablePackages || isset($this->item->package->features) && in_array(WEBSITE_ADDRESS, $this->item->package->features)) { ?>
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <label for="website"><?php echo JText::_('LNG_WEBSITE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["website"]) ?></label>
                                                        <?php
                                                        if($this->appSettings->enable_multilingual) {
                                                            echo $jbdTabs->startTabSet('tab_group_website');
                                                            foreach( $this->languages  as $k=>$lng ) {
                                                                echo $jbdTabs->addTab('tab_group_website', 'tab-'.$lng, $k);
                                                                $langContent = isset($this->translations[$lng . "_additional"])?$this->translations[$lng . "_additional"]:"";
                                                                if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                                                    $langContent = $this->item->website;
                                                                }
                                                                $required = ($attributeConfig['website'] == ATTRIBUTE_MANDATORY) ? 'validate[required]' : '';
                                                                echo "<input type='text' id='additional_description_$lng' name='additional_description_$lng' class='form-control $required' value=\"" . $this->escape($langContent) . "\" maxLength='250'>";
                                                                echo $jbdTabs->endTab();
                                                            }
                                                            echo $jbdTabs->endTabSet();
                                                        } else { ?>
                                                            <input type="text" name="website" id="website" value="<?php echo $this->escape($this->item->website) ?>" class="form-control <?php echo $attributeConfig["website"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" maxlength="250">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

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
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="admin_types" value="<?php echo !empty($adminTypes) ? implode(",", $adminTypes) : "" ?>" />
                                                <a href="javascript:jbdUtils.uncheckAll('companyTypes')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($attributeConfig["establishment_year"] != ATTRIBUTE_NOT_SHOW) { ?>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="establishment_year"><?php echo JText::_('LNG_ESTABLISHMENT_YEAR') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["establishment_year"]) ?></label>
                                                <select name="establishment_year" class="form-control select <?php echo $attributeConfig["establishment_year"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>">
                                                    <option value=""><?php echo JText::_("LNG_SELECT_OPTION") ?></option>
                                                    <?php
                                                    foreach (range(date('Y'), 1800) as $x) {
                                                        echo '<option value="' . $x . '"' . ($x == $this->item->establishment_year ? ' selected="selected"' : '') . '>' . $x . '</option>';
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php
                                    } ?>

                                    <?php
                                    if ($attributeConfig["employees"] != ATTRIBUTE_NOT_SHOW) { ?>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="employees"><?php echo JText::_('LNG_EMPLOYEES') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["employees"]) ?></label>
                                                <input type="text" name="employees" id="employees" class="form-control <?php echo $attributeConfig["employees"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->item->employees ?>" maxlength="20">
                                            </div>
                                        </div>
                                    <?php
                                    } ?>
                                    <?php if ($attributeConfig["min_project_size"] != ATTRIBUTE_NOT_SHOW) { ?>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="min_project_size"><?php echo JText::_('LNG_MIN_PROJECT_SIZE') ?> (<?php echo $this->appSettings->currency_name ?>)<?php echo JBusinessUtil::showMandatory($attributeConfig["min_project_size"]) ?></label>
                                                <input type="text" name="min_project_size" id="min_project_size" class="form-control <?php echo $attributeConfig["min_project_size"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->item->min_project_size ?>" maxlength="20">
                                            </div>
                                        </div>
                                    <?php
                                    } ?>
                                    <?php if ($attributeConfig["hourly_rate"] != ATTRIBUTE_NOT_SHOW) { ?>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="hourly_rate"><?php echo JText::_('LNG_HOURLY_RATE') ?> (<?php echo $this->appSettings->currency_symbol ?>) <?php echo JBusinessUtil::showMandatory($attributeConfig["hourly_rate"]) ?></label>
                                                <select name="hourly_rate" class="form-control select <?php echo $attributeConfig["hourly_rate"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>">
                                                    <option value=""><?php echo JText::_("LNG_SELECT_OPTION") ?></option>
                                                    <option value="0 - 10" <?php echo $this->item->hourly_rate=="0 - 10"? "selected" : ""?>>0 - 10</option>
                                                    <option value="10 - 20" <?php echo $this->item->hourly_rate=="10 - 20"? "selected" : ""?>>10 - 20</option>
                                                    <option value="20 - 50" <?php echo $this->item->hourly_rate=="20 - 50"? "selected" : ""?>>20 - 50</option>
                                                    <option value="50 - 99" <?php echo $this->item->hourly_rate=="50 - 99"? "selected" : ""?>>50 - 99</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php
                                    } ?>

                                </div>

                                <?php if (GET_DATA_FROM_YELP) { ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="yelp_id"><?php echo JText::_('LNG_YELP_ID') ?> </label>
                                                <input type="text" name="yelp_id" id="yelp_id" value="<?php echo $this->escape($this->item->yelp_id) ?>" class="form-control" maxlength="100">
                                            </div>
                                        </div>
                                <?php } ?>
                            
                                <?php if ($attributeConfig["keywords"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="bootstrap-tags form-group" id="keywords-container">                                    	
                                                <label for="keywords"><?php echo JText::_('LNG_KEYWORDS')?> <?php echo JBusinessUtil::showMandatory($attributeConfig["keywords"]) ?></label>
                                                <p class="small"><?php echo JText::_('LNG_COMPANY_KEYWORD_INFO')?></p>
                                                <input type="text" data-role="tagsinput" style="display: none" name="keywords" class="form-control <?php echo $attributeConfig["keywords"] == ATTRIBUTE_MANDATORY?"validate[required]":""?>" id="keywords" value="<?php echo $this->item->keywords ?>" maxlength="250" />
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($this->appSettings->allow_business_view_style_change) { ?>
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="form-group">
                                                <label id="company_view-lbl" for="company_view" title=""><?php echo JText::_('LNG_COMPANY_VIEW'); ?></label>
                                                <select name="company_view" id="company_view_fld" class="chosen-select">
                                                    <?php foreach ($this->item->companyViews as $key => $companyView) { ?>
                                                        <option value="<?php echo $key ?>" <?php echo $key == $this->item->company_view ? "selected" : ""; ?>><?php echo JText::_($companyView)  ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php
                                if ($attributeConfig["slogan"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="slogan"><?php echo JText::_("LNG_COMPANY_SLOGAN") ?> &nbsp;&nbsp;&nbsp;<?php echo JBusinessUtil::showMandatory($attributeConfig["slogan"]) ?></label>
                                                <p class="small"><?php echo JText::_("LNG_COMPANY_SLOGAN_INFO") ?></p>
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
                                                        echo "<textarea id='slogan_$lng' name='slogan_$lng' class='form-control $required' cols='75' rows='5' maxLength='" . $maxSloganLength . "'>" . $this->escape($langContent) . "</textarea>";
                                                        echo $jbdTabs->endTab();
                                                    }
                                                    echo $jbdTabs->endTabSet();
                                                } else { ?>
                                                    <textarea name="slogan" id="slogan" class="form-control text-input <?php echo $attributeConfig["slogan"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" cols="75" rows="5" maxLength="<?php echo $maxSloganLength ?>" onkeyup="jbdUtils.calculateLengthSlogan();"><?php echo $this->item->slogan ?></textarea>
                                                    <div class="description-counter">
                                                        <input type="hidden" name="descriptionMaxLenghtSlogan" id="descriptionMaxLenghtSlogan" value="<?php echo $maxSloganLength ?>" />
                                                        <label for="decriptionCounterSlogan">(Max. <?php echo $maxSloganLength ?> <?php JText::_('LNG_CHARACTRES') ?>).</label>
                                                        <?php echo JText::_('LNG_REMAINING') ?> <input type="text" class="js-slogan" value="0" id="descriptionCounterSlogan" name="descriptionCounterSlogan">
                                                    </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($attributeConfig["short_description"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="description_id"><?php echo JText::_('LNG_SHORT_DESCRIPTION') ?> &nbsp;&nbsp;&nbsp;<?php echo JBusinessUtil::showMandatory($attributeConfig["short_description"]) ?></label>
                                                <p class="small"><?php echo JText::_("LNG_COMPANY_SHORT_DESCR_INFO") ?></p>
                                                <?php
                                                if ($this->appSettings->enable_multilingual) {
                                                    echo $jbdTabs->startTabSet('tab_group_short_description');
                                                    foreach ($this->languages as $k => $lng) {
                                                        echo $jbdTabs->addTab('tab_group_short_description', 'tab-' . $lng, $k);
                                                        $langContent = isset($this->translations[$lng . "_short"]) ? $this->translations[$lng . "_short"] : "";
                                                        $required    = "";
                                                        if ($lng == JBusinessUtil::getLanguageTag()) {
                                                            $required = $attributeConfig['short_description'] == ATTRIBUTE_MANDATORY ? 'validate[required]' : '';
                                                            if (empty($langContent)) {
                                                                $langContent = $this->item->short_description;
                                                            }
                                                        }

                                                        echo "<textarea id='short_description_$lng' name='short_description_$lng' class='form-control $required' cols='75' rows='5' maxLength='" . $maxShortDescriptionLength . "'>$langContent</textarea>";
                                                        echo $jbdTabs->endTab();
                                                    }
                                                    echo $jbdTabs->endTabSet();
                                                } else { ?>
                                                    <textarea name="short_description" id="short_description" class="form-control <?php echo $attributeConfig["short_description"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" cols="75" rows="5" maxLength="<?php echo $maxShortDescriptionLength ?>" onkeyup="jbdUtils.calculateLengthShort();"><?php echo $this->item->short_description ?></textarea>
                                                    <span class="error_msg" id="frmDescription_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                                    <div class="description-counter">
                                                        <input type="hidden" name="descriptionMaxLenghtShort" id="descriptionMaxLenghtShort" value="<?php echo $maxShortDescriptionLength ?>" />
                                                        <label for="decriptionCounterShort">(Max. <?php echo $maxShortDescriptionLength ?> <?php JText::_('LNG_CHARACTRES') ?>).</label>
                                                        <?php echo JText::_('LNG_REMAINING') ?> <input type="text" class="js-short-description" value="0" id="descriptionCounterShort" name="descriptionCounterShort">
                                                    </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="advanced-info">
                                    <?php if ($attributeConfig["description"] != ATTRIBUTE_NOT_SHOW  && (!$enablePackages || isset($this->item->package->features) && in_array(DESCRIPTION, $this->item->package->features))) { ?>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="description_id"><?php echo JText::_("LNG_COMPANY_DESCRIPTION") ?><?php echo JBusinessUtil::showMandatory($attributeConfig["description"]) ?></label>
                                                    <p class="small"><?php echo JText::_("LNG_COMPANY_DESCR_INFO") ?></p>
                                                    <?php
                                                    if ($this->appSettings->enable_multilingual) {
                                                        echo $jbdTabs->startTabSet('tab_group_description');
                                                        foreach ($this->languages as $k => $lng) {
                                                            echo $jbdTabs->addTab('tab_group_description', 'tabdesc-' . $lng, $k);
                                                            $langContent = isset($this->translations[$lng]) ? $this->translations[$lng] : "";
                                                            if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                                                $langContent = $this->item->description;
                                                            }
                                                            if (!$enablePackages || isset($this->item->package->features) && in_array(HTML_DESCRIPTION, $this->item->package->features)) {
                                                                echo "<div class='d-flex justify-content-between'><div>Remaining Characters: <span class='count' id='count_$lng'></span></div><span class='count-warning' id='count-warning_$lng' style='color:red;'></span></div>";
                                                                echo JBusinessUtil::getEditor()->display('description_' . $lng, $langContent, '100%', '450', '70', '10', false);
                                                                echo $jbdTabs->endTab();
                                                            } else {
                                                                echo "<textarea id='description_$lng' name='description_$lng' class='form-control' cols='75' rows='10' maxLength='" . $maxDescriptionLength . "'>$langContent</textarea>";
                                                                echo $jbdTabs->endTab();
                                                            }
                                                        }
                                                        echo $jbdTabs->endTabSet();
                                                    } else {
                                                        if (!$enablePackages || isset($this->item->package->features) && in_array(HTML_DESCRIPTION, $this->item->package->features)) {
                                                            echo '<div class="d-flex justify-content-between"><div>Remaining Characters: <span id="count"></span></div><span id="count-warning" style="color:red;"></span></div>';
                                                            echo JBusinessUtil::getEditor()->display('description', $this->item->description, '100%', '450', '20', '5', false);
                                                        } else { ?>
                                                            <textarea name="description" id="description" class="form-control <?php echo $attributeConfig["description"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" cols="75" rows="10" maxLength="<?php echo $maxDescriptionLength ?>" onkeyup="calculateLenght();"><?php echo $this->item->description ?></textarea>
                                                            <span class="error_msg" id="frmDescription_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                                            <div class="description-counter">
                                                                <input type="hidden" name="descriptionMaxLenght" id="descriptionMaxLenght" value="<?php echo $maxDescriptionLength ?>" />
                                                                <label for="descriptionCounter">(Max. <?php echo $maxDescriptionLength ?> characters).</label>
                                                                <?php echo JText::_('LNG_REMAINING') ?><input type="text" value="0" id="descriptionCounter" name="descriptionCounter">
                                                            </div>
                                                    <?php }
                                                    } ?>
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
                        </fieldset>
                    </div>

                    <div id="edit-tab2" class="edit-tab">
                        <?php if (($attributeConfig["category"] != ATTRIBUTE_NOT_SHOW) && (!$enablePackages || isset($this->item->package) && $this->item->package->max_categories > 0)) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_COMPANY_CLASSIFICATION'); ?></h2>
                                <p><?php echo JText::_('LNG_SELECT_CATEGORY'); ?></p>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="category"><?php echo JText::_('LNG_CATEGORY'); ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                        <select name="selectedSubcategories[]" id="selectedSubcategories" data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="form-control input-medium chosen-select-categories" multiple>
                                            <?php echo JHtml::_('select.options', $this->categoryOptions, 'value', 'text', $this->item->selCats); ?>
                                        </select>
                                        <a href="javascript:jbdUtils.uncheckAllCategories('mainSubcategory', 'selectedSubcategories')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                    </div>
                                    <div class="form-group">
                                        <?php if ($attributeConfig["category"] == ATTRIBUTE_MANDATORY) { ?>
                                            <div class="form-detail req"></div>
                                        <?php } ?>
                                        <label for="subcat_main_id"><?php echo JText::_('LNG_MAIN_SUBCATEGORY'); ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                        <select data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="form-control select <?php echo $attributeConfig["category"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" name="mainSubcategory" id="mainSubcategory" <?php echo $this->appSettings->enable_attribute_category ? 'onchange="jbdListings.updateAttributes(this.value, ' . $this->item->id . ')"' : '' ?>>
                                            <?php foreach ($this->item->selectedCategories as $selectedCategory) { ?>
                                                <option value="<?php echo $selectedCategory->id ?>" <?php echo $selectedCategory->id == $this->item->mainSubcategory ? "selected" : ""; ?>><?php echo $selectedCategory->name ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="error_msg" id="frmMainSubcategory_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php if (((!empty($this->item->customFields) && $this->item->containsCustomFields) || $this->appSettings->enable_attribute_category) && (!$enablePackages || isset($this->item->package) && $this->item->package->max_categories > 0)) { ?>
                            <?php
                            $packageFeatures = !empty($this->item->package->features) ? $this->item->package->features : null;

                            $renderedContent = AttributeService::renderAttributes($this->item->customFields, $enablePackages, $packageFeatures);
                            ?>
                            <fieldset class="boxed" id="custom-attributes-section" style="<?php echo empty($renderedContent) ? "display:none;" : "" ?>">
                                <h2> <?php echo JText::_('LNG_ADDITIONAL_INFO'); ?></h2>
                                <p><?php echo JText::_('LNG_ADDITIONAL_INFO_TEXT'); ?></p>
                                <div class="form-container" id="customFieldsContent">
                                    <?php
                                    if ($this->appSettings->enable_attribute_category) {
                                        if (!empty($this->item->mainSubcategory)) {
                                            echo $renderedContent;
                                        } else {
                                            echo JText::_("LNG_NO_ATTRIBUTES_FOR_CATEGORY");
                                        }
                                    } else {
                                        echo $renderedContent;
                                    }
                                    ?>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php if (!empty($this->membershipOptions) && (!$enablePackages || isset($this->item->package->features) && in_array(MEMBERSHIPS, $this->item->package->features))) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_COMPANY_MEMBERSHIPS'); ?></h2>
                                <p><?php echo JText::_('LNG_SELECT_MEMBERSHIP'); ?></p>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="memberships"><?php echo JText::_('LNG_MEMBERSHIP'); ?></label>
                                        <select name="selectedMemberships[]" id="selectedMemberships" data-placeholder="<?php echo JText::_("LNG_SELECT_MEMBERSHIP") ?>" class="form-control input-medium chosen-select-membership" multiple>
                                            <?php echo JHtml::_('select.options', $this->membershipOptions, 'value', 'text', $this->item->selMembership); ?>
                                        </select>
                                        <a href="javascript:jbdUtils.uncheckAll('selectedMemberships')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php
                        if ($attributeConfig["opening_hours"] != ATTRIBUTE_NOT_SHOW) { ?>
                            <?php
                            if (!$enablePackages || isset($this->item->package->features) && in_array(OPENING_HOURS, $this->item->package->features)) { ?>
                                <!-- Work Hours Section -->
                                <fieldset class="boxed w-100">
                                    <h2><?php echo JText::_('LNG_OPENING_HOURS'); ?></h2>

                                    <div class="">
                                        <p> <?php echo JText::_('LNG_TIME_ZONE_INFORMATION_TEXT'); ?>.</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group" id="time_zoneContainer">
                                                    <select class="form-control input-medium chosen-select" id="time_zone" name="time_zone">
                                                        <?php $timeZones = JBusinessUtil::timeZonesList();
                                                        foreach ($timeZones as $key => $zone) {
                                                            $selected = ($key == $this->item->time_zone) ? " selected" : "";
                                                            echo "<option value='" . $key . "'" . $selected . ">" . $zone . "</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <p> <?php echo JText::_('LNG_OPENING_STATUS'); ?></p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group" id="openingStatus">
                                                    <select class="form-control input-medium chosen-select" id="opening_status" name="opening_status">
                                                        <?php foreach ($this->openingStatusOptions as $option) {
                                                            $selected = ($option->value == $this->item->opening_status) ? " selected" : "";
                                                            echo "<option value='" . $option->value . "'" . $selected . ">" . $option->text . "</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="business-schedule" style="<?php echo $this->item->opening_status != 0 ? "display:none" : "" ?>">
                                        <p><strong><?php echo JText::_('LNG_WORKING_HOURS_TEXT'); ?></strong></p>
                                        <div class="business-hour-setup">
                                            <div class="form-group">
                                                <div class="">
                                                    <?php foreach ($this->weekDays as $key => $val) { ?>
                                                        <div class="row align-items-center ">
                                                            <div class="col-12 col-md-2 text-left">
                                                                <div class="day-name"> <?php echo $val->name ?></div>
                                                            </div>
                                                            <div class="col-12 col-md-10">
                                                                <div class="row">
                                                                    <div class="col-lg-6 py-0" id="js_workhours_<?php echo $key ?>">
                                                                        <div class="row align-items-center">
                                                                            <div class="col-1 p-1">
                                                                                <div>
                                                                                    <input type="checkbox" class="left" id="work_status_check_<?php echo $key ?>" onclick="jbdListings.disableWorkPeriod(<?php echo $key ?>, true, false)" <?php echo $val->workHours['status'] ? 'checked' : ''; ?> />
                                                                                    <input type="hidden" value="<?php echo $val->workHours['status'] ?>" name="work_status[]" id="work_status_<?php echo $key ?>" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4 p-1">
                                                                                <input type="text" name="work_start_hour[]" id="work_start_hour_<?php echo $key ?>" class="form-control p-1 text-center timepicker work-hours-input <?php echo $val->workHours['status'] ? 'validate[required]' : '' ?>" value="<?php echo $val->workHours['start_time'] ?>" <?php echo $val->workHours['status'] ? '' : 'readonly'; ?> />
                                                                            </div>
                                                                            <div class="col-4 p-1">
                                                                                <input type="text" name="work_end_hour[]" id="work_end_hour_<?php echo $key ?>" class="form-control p-1 text-center timepicker work-hours-input <?php echo $val->workHours['status'] ? 'validate[required]' : '' ?>" value="<?php echo $val->workHours['end_time'] ?>" <?php echo $val->workHours['status'] ? '' : 'readonly'; ?> />
                                                                            </div>
                                                                            <div class="col-3 p-0 js_action_buttons">
                                                                                <?php if ($val->workHours['status']) { ?>
                                                                                    <a href="javascript:void(0)" class="btn btn-sm btn-success btn-panel break-button px-2" <?php echo (!empty($val->breakHours)) ? 'disabled' : ''; ?> onclick="jbdListings.addBreak('<?php echo $key ?>', false)">
                                                                                        <?php echo JText::_('LNG_ADD_BREAK'); ?>
                                                                                    </a>
                                                                                <?php } else { ?>
                                                                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger btn-panel closed-button px-2" disabled>
                                                                                        <?php echo JText::_('LNG_CLOSED'); ?>
                                                                                    </a>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-6 py-0 dflex align-items-center">
                                                                        <div id="break_hours_day_<?php echo $key ?>" class="dflex">
                                                                            <?php
                                                                            $i = 0;
                                                                            if (!empty($val->breakHours) && $val->workHours['status']) {
                                                                                foreach ($val->breakHours['start_time'] as $k => $v) { ?>
                                                                                    <div class="row align-items-center" id="break_hour_period_<?php echo $key . '_' . $i ?>">
                                                                                        <div class="col-1 p-1">
                                                                                        </div>
                                                                                        <div class="col-4 p-1">
                                                                                            <input type="text" name="break_start_hour[]" class="form-control p-1 text-center timepicker" value="<?php echo $val->breakHours['start_time'][$k] ?>" />
                                                                                        </div>
                                                                                        <div class="col-4 p-1">
                                                                                            <input type="text" name="break_end_hour[]" class="form-control p-1 text-center timepicker" value="<?php echo $val->breakHours['end_time'][$k] ?>" />
                                                                                        </div>
                                                                                        <div class="col-3 p-0">
                                                                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-panel" onclick="jbdListings.deleteCompanyBreak(<?php echo $key ?>, <?php echo $i ?>)">
                                                                                                <i class="la la-trash"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                        <input type="hidden" name="break_ids[]" id="break_ids" value="<?php echo $val->breakHours['id'][$k] ?>" />
                                                                                    </div>
                                                                            <?php $i++;
                                                                                }
                                                                            } ?>
                                                                        </div>
                                                                        <input type="hidden" name="breaks_count[]" id="break_periods_count_<?php echo $key ?>" value="<?php echo $i ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="work_ids[]" id="work_ids-<?php echo $key ?>" value="<?php echo $val->workHours['id'] ?>" />
                                                        </div>
                                                    <?php } ?>
                                                    <div class="row">
                                                        <div class="d-flex justify-content-end col-md-6">
                                                            <a class="btn btn-outline py-1" onclick="resetHours()" href="javascript:void(0)"><?php echo JText::_('LNG_RESET') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="country"><?php echo JText::_('LNG_NOTES') ?> </label>
                                                <textarea class="form-control" name="notes_hours" id="notes_hours" rows="3" maxlength="255"><?php echo $this->item->notes_hours ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <div id="edit-tab3" class="edit-tab">
                        <?php if ($showLocation) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_COMPANY_LOCATION'); ?></h2>
                                <p><?php echo JText::_('LNG_COMPANY_LOCATION_TXT'); ?></p>
                                <div class="form-container">
                                    
                                    <?php FormService::renderAddressFields($attributeConfig, $this->item); ?>

                                    <div class="row">
                                        <?php if ($attributeConfig["radius"] != ATTRIBUTE_NOT_SHOW) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="district_id"><?php echo JText::_('LNG_ACTIVITY_RADIUS') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["radius"]) ?> </label>
                                                    <p class="small"><?php echo JText::_('LNG_ACTIVITY_RADIUS_INFO') ?></p>
                                                    <div class="activity-radius-container">
                                                        <input class="form-control <?php echo $attributeConfig["radius"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="activity_radius" id="activity_radius" value="<?php echo $this->item->activity_radius ?>" />
                                                        (<?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?>) -&nbsp;<span id="converted-value"></span>&nbsp;(<?php echo $this->appSettings->metric!=1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?>) 
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if ($attributeConfig["publish_only_city"] != ATTRIBUTE_NOT_SHOW) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label id="edit_form_mode-lbl" for="edit_form_mode"><?php echo JText::_('LNG_PUBLISH_ONLY_CITY'); ?></label>
                                                    <p class="small"><?php echo JText::_('LNG_PUBLISH_ONLY_CITY_DESCRIPTION') ?></p>
                                                    <fieldset id="publish_only_city_fld" class="radio btn-group btn-group-yesno">
                                                        <input type="radio" class="validate[required]" name="publish_only_city" id="publish_only_city1" value="1" <?php echo $this->item->publish_only_city == 1 ? 'checked="checked"' : "" ?> />
                                                        <label class="btn" for="publish_only_city1"><?php echo JText::_('LNG_YES') ?></label>
                                                        <input type="radio" class="validate[required]" name="publish_only_city" id="publish_only_city2" value="0" <?php echo $this->item->publish_only_city == 0 ? 'checked="checked"' : "" ?> />
                                                        <label class="btn" for="publish_only_city2"><?php echo JText::_('LNG_NO') ?></label>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>                                            

                                    <?php if (!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP, $this->item->package->features)) { ?>
                                        <?php if ($attributeConfig["map"] != ATTRIBUTE_NOT_SHOW) { ?>
                                            <div class="row pb-2">
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <label for="latitude"><?php echo JText::_('LNG_LATITUDE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["map"]) ?></label>
                                                        <p class="small"><?php echo JText::_('LNG_MAP_INFO') ?></p>
                                                        <input class="form-control validate[custom[number]] <?php echo $attributeConfig["map"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="latitude" id="latitude" value="<?php echo $this->item->latitude ?>" maxlength="45">
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <label for="longitude"><?php echo JText::_('LNG_LONGITUDE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["map"]) ?></label>
                                                        <p class="small"><?php echo JText::_('LNG_MAP_INFO') ?></p>
                                                        <input class="form-control validate[custom[number]] <?php echo $attributeConfig["map"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="longitude" id="longitude" value="<?php echo $this->item->longitude ?>" maxlength="45">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if (!$enablePackages || isset($this->item->package->features) && in_array(GOOGLE_MAP, $this->item->package->features)) { ?>
                                        <div id="map-container">
                                            <div id="company-map">
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </fieldset>
                        <?php } ?>
                        <?php
                        if (
                            $showLocation && $this->appSettings->show_secondary_locations == 1 &&
                            (!$enablePackages || isset($this->item->package->features) && in_array(SECONDARY_LOCATIONS, $this->item->package->features) && $this->item->package->max_locations > 0)
                        ) { ?>
                            <fieldset class="boxed">
                                <div class="row">
                                    <div class="col-12">
                                        <h2> <?php echo JText::_('LNG_COMPANY_SECONDARY_LOCATIONS'); ?></h2>
                                        <p> <?php echo JText::_('LNG_COMPANY_SECONDARY_LOCATIONS_TXT'); ?>.</p>
                                        <div class="form-container" id="company-locations">
                                            <?php foreach ($this->item->locations as $location) { ?>
                                                <div class="form-group" id="location-box-<?php echo $location->id ?>">
                                                    <div id="location-<?php echo $location->id ?>"><?php echo $location->name . " - " . $location->street_number . ", " . $location->address . ", " . $location->city . ", " . $location->county . ", " . $location->country ?></div>
                                                    <a href="javascript:jbdListings.editLocation(<?php echo $location->id ?>,'<?php echo $uniqueCode ?>')"><?php echo JText::_("LNG_EDIT") ?></a> | <a href="javascript:jbdListings.deleteLocation(<?php echo $location->id ?>)"><?php echo JText::_("LNG_DELETE") ?></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div id="add-location">
                                            <a class="new-item-btn" href="javascript:jbdListings.editLocation(0,'<?php echo $uniqueCode ?>')"><?php echo JText::_("LNG_ADD_NEW_LOCATION") ?> + </a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php if ($attributeConfig["areas_served"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(AREAS_SERVED, $this->item->package->features))) { ?>
                            <fieldset class="boxed">
                                <div class="row">
                                    <div class="col-12">
                                        <h2> <?php echo JText::_('LNG_AREAS_SERVED'); ?></h2>
                                        <p> <?php echo JText::_('LNG_AREAS_SERVED_TEXT'); ?>.</p>
                                        <div class="form-container">
                                            <div class="row">
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <!-- label for="areas_served"><?php echo JText::_('LNG_AREAS_SERVED') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["areas_served"]) ?></label -->
                                                        <input type="text" name="areas_served" id="areas_served" class="form-control text-input <?php echo $attributeConfig["areas_served"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->areas_served) ?>" maxlength="455">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php if (false && !$enablePackages && $attributeConfig["zip_codes"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["zip_codes"] != ATTRIBUTE_NOT_SHOW && isset($this->item->package->features) && in_array(ZIP_CODES, $this->item->package->features)) { ?>
                            <fieldset class="boxed">
                                <div class="row">
                                    <div class="col-12">
                                        <h2> <?php echo JText::_('LNG_COMPANY_ZIPCODES'); ?></h2>
                                        <p> <?php echo JText::_('LNG_COMPANY_ZIPCODES_TXT'); ?>.</p>
                                        <div class="form-group">
                                            <?php echo JText::_('LNG_ADD_ZIPCODE') ?>
                                            <a href="javascript:void(0);" id="add-zip-field" class="btn btn-success ml-3"><i class="la la-plus"></i></a>

                                            <div class="form-container mt-3">
                                                <div class="form-group" id="zip-box-container">
                                                    <?php foreach ($this->item->zipcodes as $zipcode) { ?>
                                                        <?php if (!empty($zipcode->id)) { ?>
                                                            <div class="mb-2">
                                                                <div class="d-flex">
                                                                    <input type="text" readonly value="<?php echo $zipcode->zip_code ?>"></input>
                                                                    <input type="hidden" name="zip_code[]" value="<?php echo $zipcode->zip_code ?>"></input>
                                                                    <input type="hidden" name="latitudes[]" value="<?php echo $zipcode->latitude ?>"></input>
                                                                    <input type="hidden" name="longitudes[]" value="<?php echo $zipcode->longitude ?>"></input>
                                                                    <input type="hidden" name="zip_code_id[]" value="<?php echo $zipcode->id ?>"></input>
                                                                    <a href="javascript:void(0);" onclick="jQuery(this).parent().parent().remove()" class="btn btn-danger ml-2"><i class="la la-trash"></i></a>
                                                                </div>
                                                                <p class="small"><?php echo JText::_('LNG_LATITUDE') ?> <?php echo $zipcode->latitude ?> , <?php echo JText::_('LNG_LONGITUDE') ?> <?php echo $zipcode->longitude ?></p>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>
                    </div>
                    <div id="edit-tab4" class="edit-tab">
                        <?php
                        if (
                            $attributeConfig["mobile_phone"] != ATTRIBUTE_NOT_SHOW ||
                            $attributeConfig["phone"] != ATTRIBUTE_NOT_SHOW ||
                            $attributeConfig["email"] != ATTRIBUTE_NOT_SHOW ||
                            $attributeConfig["fax"] != ATTRIBUTE_NOT_SHOW
                        ) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_COMPANY_CONTACT_INFORMATION'); ?></h2>
                                <p> <?php echo JText::_('LNG_COMPANY_CONTACT_INFORMATION_TEXT'); ?></p>
                                <div class="form-container">
                                    <div class="row">
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
                                        <?php if ($attributeConfig["mobile_phone"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(PHONE, $this->item->package->features))) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="phone"><?php echo JText::_('LNG_MOBILE_PHONE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["mobile_phone"]) ?></label>
                                                    <div class="has-jicon-right">
                                                        <input type="text" name="mobile" id="mobile" class="form-control <?php echo $attributeConfig["mobile_phone"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->mobile) ?>" maxlength="55">
                                                        <i class="la la-mobile"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($attributeConfig["fax"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(PHONE, $this->item->package->features))) { ?>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="fax"><?php echo JText::_('LNG_FAX') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["fax"]) ?></label>
                                                    <div class="has-jicon-right">
                                                        <input type="text" name="fax" id="fax" class="form-control <?php echo $attributeConfig["fax"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->fax) ?>" maxlength="20">
                                                        <i class="la la-fax"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if ($attributeConfig["email"] != ATTRIBUTE_NOT_SHOW || $this->appSettings->allow_user_creation == 1) { ?>
                                        <div class="form-group">
                                            <label for="email"><?php echo JText::_('LNG_EMAIL')?> <?php echo JBusinessUtil::showMandatory($attributeConfig["email"] || $this->appSettings->allow_user_creation == 1) ?></label>
                                            <div class="has-jicon-right">
                                                <input type="text" name="email" id="email" class="form-control <?php echo $attributeConfig["email"] == ATTRIBUTE_MANDATORY || $this->appSettings->allow_user_creation == 1 ?"validate[required,custom[email]]":"validate[custom[email]]"?> text-input" value="<?php echo $this->escape($this->item->email) ?>" maxlength="100">
                                                <i class="la la-envelope"></i>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($this->appSettings->enable_request_quote_app && JBusinessUtil::isAppInstalled(JBD_APP_QUOTE_REQUESTS)) { ?>
                                        <div class="form-container">
                                            <label id="enable_request_quote" for="enable_request_quote" title=""><?php echo JText::_('LNG_ENABLE_CONTACT_FROM_REQUEST_QUOTE'); ?></label>
                                            <div class="form-group">
                                                <fieldset id="enable_request_quote" class="radio btn-group btn-group-yesno">
                                                    <label class="btn" id="label_enable_request_quote1" for="enable_request_quote1"><?php echo JTEXT::_("LNG_YES") ?></label>
                                                    <input type="radio" name="enable_request_quote" id="enable_request_quote1" value="1" <?php echo $this->item->enable_request_quote == 1 ? 'checked="checked"' : "" ?> />
                                                    <input type="radio" name="enable_request_quote" id="enable_request_quote0" value="0" <?php echo $this->item->enable_request_quote == 0 ? 'checked="checked"' : "" ?> />
                                                    <label class="btn" id="label_enable_request_quote0" for="enable_request_quote0"><?php echo JText::_('LNG_NO') ?></label>
                                                </fieldset>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php if ($attributeConfig["contact_person"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(CONTACT_FORM, $this->item->package->features))) { ?>
                            <fieldset class="boxed">
                                <div class="row">
                                    <div class="col-12">
                                        <h2> <?php echo JText::_('LNG_COMPANY_CONTACT_PERSON_INFORMATION'); ?></h2>
                                        <p> <?php echo JText::_('LNG_COMPANY_CONTACT_PERSON_INFORMATION_TEXT'); ?></p>
                                        <span id="contact_details">
                                            <?php $i = 1;
                                            foreach ($this->item->contacts as $contact) { ?>
                                                <div class="form-container" id="contact-form-box<?php echo $i ?>">
                                                    <span id="contact_header<?php echo $i ?>">
                                                        <h3><?php echo JText::_('LNG_CONTACT') . ' ' . $i; ?></h3>&nbsp;&nbsp;
                                                        <a <?php echo $i == 1 ? 'style="display:none;"' : ''; ?> class="right remove-contact" href="javascript:jbdListings.removeContact(<?php echo $i; ?>)">(<?php echo JText::_('LNG_REMOVE_CONTACT'); ?>)</a>
                                                    </span>
                                                    <div class="form-group">
                                                        <label for="contact_department"><?php echo JText::_('LNG_DEPARTMENT') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["contact_person"]) ?></label>
                                                        <input type="text" name="contact_department[]" id="contact_department<?php echo $i ?>" class="form-control <?php echo $attributeConfig["contact_person"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($contact->contact_department) ?>" maxlength="50">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contact_department"><?php echo JText::_('LNG_JOB_TITLE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["contact_person"]) ?></label>
                                                        <input type="text" name="contact_job_title[]" id="contact_job_title<?php echo $i ?>" class="form-control <?php echo $attributeConfig["contact_person"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($contact->contact_job_title) ?>" maxlength="50">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contact_name"><?php echo JText::_('LNG_NAME') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["contact_person"]) ?></label>
                                                        <input type="text" name="contact_name[]" id="contact_name<?php echo $i ?>" class="form-control <?php echo $attributeConfig["contact_person"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($contact->contact_name) ?>" maxlength="50">
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md">
                                                            <div class="form-group">
                                                                <label for="contact_phone"><?php echo JText::_('LNG_TELEPHONE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["contact_person"]) ?></label>
                                                                <div class="has-jicon-right">
                                                                    <input type="text" name="contact_phone[]" id="contact_phone<?php echo $i ?>" class="form-control <?php echo $attributeConfig["contact_person"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $contact->contact_phone ?>" maxlength="20">
                                                                    <i class="la la-phone"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md">
                                                            <label for="contact_email"><?php echo JText::_('LNG_EMAIL') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["contact_person"]) ?></label>
                                                            <div class="has-jicon-right">
                                                                <input type="text" name="contact_email[]" id="contact_email<?php echo $i ?>" class="form-control  <?php echo $attributeConfig["contact_person"] == ATTRIBUTE_MANDATORY ? "validate[required,custom[email]]" : " validate[custom[email]]" ?>" value="<?php echo $this->escape($contact->contact_email) ?>" maxlength="60">
                                                                <i class="la la-envelope"></i>
                                                            </div>
                                                            <div class="description">e.g. office@domain.com</div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="contact_id[]" id="contact_id<?php echo $i ?>" value="<?php echo $contact->id ?>" />
                                                    <hr />
                                                </div>
                                            <?php
                                                $i++;
                                            }
                                            $totalContacts = $i - 1; ?>
                                            <?php ?>
                                        </span>
                                        <span id="total_contacts" style="display:none;"><?php echo $totalContacts ?></span>
                                        <a id="add_contact" class="new-item-btn" href="javascript:void(0)" onclick="jbdListings.addNewContact('<?php echo $i - 1 ?>')"><?php echo JText::_('LNG_ADD_NEW_CONTACT'); ?> + </a>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php
                        if ($attributeConfig["social_networks"] != ATTRIBUTE_NOT_SHOW) {
                            if (!$enablePackages || isset($this->item->package->features) && in_array(SOCIAL_NETWORKS, $this->item->package->features)) { ?>
                                <fieldset class="boxed">
                                    <h2> <?php echo JText::_('LNG_SOCIAL_NETWORKS'); ?></h2>
                                    <p><?php echo JText::_('LNG_SOCIAL_NETWORKS_TEXT'); ?></p>
                                    <div class="form-container">
                                        <div class="row">
                                            <?php if ($attributeConfig["instagram"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <label for="instagram"><?php echo JText::_('LNG_INSTAGRAM') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                        <input type="text" name="instagram" id="instagram" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->instagram); ?>" maxlength="150">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($attributeConfig["tiktok"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <label for="tiktok"><?php echo JText::_('LNG_TIKTOK') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                        <input type="text" name="tiktok" id="tiktok" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->tiktok); ?>" maxlength="150">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
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
                                            <?php if ($attributeConfig["pinterest"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <label for="pinterest"><?php echo JText::_('LNG_PINTEREST') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["social_networks"]) ?></label>
                                                        <input type="text" name="pinterest" id="pinterest" class="form-control <?php echo $attributeConfig["social_networks"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->pinterest); ?>" maxlength="150">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="row">
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
                                </fieldset>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($attributeConfig["business_team"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(TEAM_FEATURE, $this->item->package->features))) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_MEMBERS'); ?></h2>
                                <p> <?php echo JText::_('LNG_MEMBERS_TEXT'); ?></p>
                                <div id="member_details">
                                    <?php $i = 1;
                                    $companyFolder = COMPANY_PICTURES_PATH . ($this->item->id) . "/";
                                    $companyFolderPathId = JBusinessUtil::getUploadUrl() . "&t=" . strtotime("now") . "&picture_type=" . PICTURE_TYPE_GALLERY . "&_path_type=1&_target=" . urlencode(COMPANY_PICTURES_PATH . ($this->item->id) . "/") . "&croppable=1";

                                    foreach ($this->item->members as $member) { ?>
                                        <div class="form-box" id="member-form-box<?php echo $i ?>">
                                            <div id="member_header<?php echo $i ?>">
                                                <h3><?php echo JText::_('LNG_MEMBER') . ' ' . $i; ?></h3>
                                                <a <?php echo $i == 1 ? 'style="display:none;"' : ''; ?> class="right la la-trash" href="javascript:jbdListings.removeMember(<?php echo $i; ?>)">(<?php echo JText::_('LNG_REMOVE_MEMBER'); ?>
                                                    )</a>
                                            </div>
                                            <div class="form-group">
                                                <label for="member_name"><?php echo JText::_('LNG_NAME') ?></label>
                                                <input type="text" name="member_name[]" id="member_name<?php echo $i ?>" class="input_txt" value="<?php echo $member->name ?>" maxlength="50">
                                                <div class="clear"></div>
                                            </div>
                                            <div id="member_type_select" class="form-group">
                                                <label for="member_type"><?php echo JText::_('LNG_MEMBER_TYPE') ?></label>
                                                <select name="member_type[]" id="member_type1" class="input_sel chosen select chosen-select">
                                                    <option <?php echo $member->type == MEMBER_TYPE_LEADERSHIP ? "selected" : "" ?> value="<?php echo MEMBER_TYPE_LEADERSHIP ?>"><?php echo JText::_('LNG_MEMBER_TYPE_LEADERSHIP') ?></option>
                                                    <option <?php echo $member->type == MEMBER_TYPE_TEAM ? "selected" : "" ?> value="<?php echo MEMBER_TYPE_TEAM ?>"><?php echo JText::_('LNG_MEMBER_TYPE_TEAM') ?></option>
                                                </select>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="member_title"><?php echo JText::_('LNG_MEMBER_TITLE') ?></label>
                                                <input type="text" name="member_title[]" id="member_title<?php echo $i ?>" class="input_txt" value="<?php echo $member->title; ?>" maxlength="50">
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-group" id="member_description_container1">
                                                <label for="member_description"><?php echo JText::_('LNG_DESCRIPTION') ?></label>
                                                <textarea name="member_description[]" id="member_description1" class="input_txt" cols="75" rows="5" maxLength="350"><?php echo $member->description ?></textarea>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="form-group" id="memberImageSection">
                                                <label> <?php echo JText::_('LNG_IMAGE'); ?></label>
                                                <div class="form-upload-elem">
                                                    <div class="form-upload" id="memberImageSection">
                                                        <input type="file" class="input-imageLocationSize" id="member-imageUploader<?php echo $i ?>" name="uploadfile" size="50">
                                                        <div class="clear"></div>
                                                        <input type="text" style="visibility:hidden;height:1px !important;" class="input-imageLocation" name="member_image[]" id="member-imageLocation<?php echo $i ?>" value="<?php echo $member->image ?>">
                                                        <a id="anchor" href="javascript:uploadInstance.removeImage('member-',<?php echo $i ?>);"><?php echo JText::_("LNG_REMOVE_IMAGE") ?></a>
                                                    </div>

                                                    <div class="jupload logo-jupload">
                                                        <div class="jupload-body">
                                                            <div class="jupload-files">
                                                                <div class="upload-files-img image-fit-contain picture-preview services" id="member-picture-preview<?php echo $i ?>">
                                                                    <?php if (!empty($member->image)) { ?>
                                                                        <img id="itemImg" class="item-image" src="<?php echo BD_PICTURES_PATH . $member->image ?>">
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="member_id[]" id="member_id<?php echo $i ?>" value="<?php echo $member->id ?>" />
                                            <hr />
                                        </div>
                                        <?php $i++; ?>
                                    <?php } ?>
                                    <?php $totalMembers = $i - 1; ?>
                                    <?php ?>
                                </div>
                                <span id="total_members" style="display:none;"><?php echo $totalMembers ?></span>
                                <a id="add_member" class="new-item-btn" href="javascript:void(0)" onclick="jbdListings.addNewMember('<?php echo $i - 1 ?>','<?php echo ($companyFolder) ?>','<?php echo ($companyFolderPathId) ?>')"><?php echo JText::_('LNG_ADD_NEW_MEMBER'); ?> + </a>
                            </fieldset>
                        <?php } ?>
                    </div>
                    <div id="edit-tab5" class="edit-tab">
                        <?php if ($attributeConfig["logo"]!=ATTRIBUTE_NOT_SHOW) { ?>
                            <?php if (!$enablePackages || isset($this->item->package->features) && in_array(SHOW_COMPANY_LOGO,$this->item->package->features)) { ?>
                                <fieldset class="boxed">
                                    <div class="form-container">
                                        <h2> <?php echo JText::_('LNG_ADD_LOGO');?> <?php echo JBusinessUtil::showMandatory($attributeConfig["logo"]) ?></h2>
                                        <div>
                                            <?php echo JText::_('LNG_ADD_LOGO_TEXT');?>
                                        </div>
                                        
                                        <div class="jupload logo-jupload pt-5">
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
                                                    <label for="imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                                    <a name="" id="" class="" href="javascript:uploadInstance.removeImage()" role="button"><i class="la la-trash"></i></a>
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
                                </fieldset>
                            <?php
                            } ?>
                        <?php
                        } ?>

                        <?php if ($attributeConfig["cover_image"]!=ATTRIBUTE_NOT_SHOW) { ?>
                            <fieldset  class="boxed">
                                <div class="form-container">
                                    <h2> <?php echo JText::_('LNG_ADD_BUSINESS_COVER_IMAGE');?> <?php $attributeConfig["cover_image"]=ATTRIBUTE_OPTIONAL?"(". JText::_('LNG_OPTIONAL').")":"" ?></h2>
                                    <div>
                                        <?php echo JText::_('LNG_ADD_BUSINESS_COVER_IMAGE_TEXT');?>
                                    </div>
                                    <div class="jupload cover-jupload pt-5">
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
                                            <div class="jupload-options">
                                                <div class="jupload-options-btn jupload-actions">
                                                    <label for="cover-imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_CHOOSE_FILE")?></label>
                                                    <a name="" id="" class="" href="javascript:uploadInstance.removeImage('cover-')" role="button"><i class="la la-trash"></i></a>
                                                </div>
                                            </div>

                                            <fieldset>
                                                <input type="hidden" name="business_cover_image" id="cover-imageLocation" value="<?php echo $this->item->business_cover_image ?>">
                                                <input type="file" id="cover-imageUploader" name="uploadfile" size="50">
                                                <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>. <?php echo JBusinessUtil::showMandatory($attributeConfig["cover_image"]) ?>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php
                        if ($attributeConfig["pictures"] != ATTRIBUTE_NOT_SHOW) {
                            if (!$enablePackages || isset($this->item->package->features) && in_array(IMAGE_UPLOAD, $this->item->package->features)) { ?>
                                <fieldset class="boxed">
                                    <h2> <?php echo JText::_('LNG_COMPANY_PICTURES'); ?> </h2>
                                    <p> <?php echo JText::_('LNG_COMPANY_PICTURE_INFORMATION_TEXT'); ?>.</p>
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
                                                        <button class="btn btn-primary start" id="file-upload-submitAll">
                                                            <i class="glyphicon glyphicon-upload"></i>
                                                            <span><?php echo JText::_('LNG_UPLOAD_ALL'); ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <?php if ($attributeConfig["custom_gallery"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <fieldset class="boxed">
                                        <h2> <?php echo JText::_('LNG_EXTRA_PICTURES'); ?> </h2>
                                        <p> <?php echo JText::_('LNG_EXTRA_PICTURE_DESC'); ?>.</p>
                                        <a class="btn btn-outline-danger" id="remove-pictures" href="javascript:void(0);" onclick="uploadInstance.removeAllPicture('extra')"><?php echo JText::_('LNG_DELETE_ALL') ?></a>
                                        <input type='button' name='btn_removefile' id='btn_removefile' value='x' style='display:none'>
                                        <input type='hidden' name='crt_pos' id='crt_pos' value=''>
                                        <input type='hidden' name='crt_path' id='crt_path' value=''>

                                        <div class="jupload" id="extra-pictures-list">
                                            <div class="jupload-header">
                                                <div class="jupload-header-title"></div>
                                                <div class="jupload-header-desc"></div>
                                            </div>
                                            <div class="jupload-body">
                                                <ul id="extra" class="jbd-item-list">
                                                    <?php
                                                    if (!empty($this->item->extra_pictures)) {
                                                        foreach ($this->item->extra_pictures as $picture) { ?>
                                                            <li class="jbd-item" id="jbd-item-<?php echo $picture['id'] ?>">
                                                                <div class="jupload-files">
                                                                    <div class="jupload-files-img">
                                                                        <img src='<?php echo BD_PICTURES_PATH . $picture['image_path'] ?>'>
                                                                    </div>
                                                                    <div class="jupload-files-info">
                                                                        <div class="jupload-filename">
                                                                            <p><?php echo substr(basename($picture['image_path']), 0, 30) ?></p>
                                                                            <input id="jupload-filename-<?php echo $picture['id'] ?>" type="text" name="image_title[]" value="<?php echo $picture['image_title'] ?>" maxlength="255" placeholder="<?php echo JText::_('LNG_TITLE') ?>">
                                                                            <input id="jupload-filename-<?php echo $picture['id'] ?>" type="text" name="image_info[]" value="<?php echo $picture['image_info'] ?>" maxlength="255" placeholder="<?php echo JText::_('LNG_DESCRIPTION') ?>">
                                                                        </div>
                                                                        <div class="jupload-actions jbd-item-actions">
                                                                            <label for="jupload-filename-<?php echo $picture['id'] ?>">
                                                                                <i class="la la-pencil"></i>
                                                                            </label>

                                                                            <input type="hidden" name="image_enable[]" id="image_enable_<?php echo $picture['id'] ?>" value="<?php echo $picture['image_enable'] ?>" />
                                                                            <input type='hidden' name='image_path[]' id='image_path_<?php echo $picture['id'] ?>' value='<?php echo $this->escape($picture['image_path']) ?>' />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>

                                                    <?php
                                                        }
                                                    } ?>
                                                </ul>

                                                <div class="dropzone dropzone-previews container-fluid" id="image-upload">
                                                    <div id="actions" style="margin-left:-15px;" class="row">
                                                        <div class="col d-flex justify-content-center">
                                                            <!-- The fileinput-btn span is used to style the file input field as button -->
                                                            <span class="btn btn-success fileinput-btn dz-clickable mr-1">
                                                                <i class="glyphicon glyphicon-plus"></i>
                                                                <span><?php echo JText::_('LNG_ADD_FILES'); ?></span>
                                                            </span>
                                                            <button class="btn btn-primary start" id="image-upload-submitAll">
                                                                <i class="glyphicon glyphicon-upload"></i>
                                                                <span><?php echo JText::_('LNG_UPLOAD_ALL'); ?></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                <?php } ?>
                            <?php
                            } ?>
                        <?php
                        } ?>

                        <?php if ($attributeConfig["ad_images"] != ATTRIBUTE_NOT_SHOW) {
                            $required = ($attributeConfig["ad_images"] == ATTRIBUTE_MANDATORY) ? "validate[required]" : "";
                        ?>
                            <fieldset class="boxed">
                                <div class="form-container">
                                    <h2> <?php echo JText::_('LNG_ADD_LISTING_AD'); ?><?php $attributeConfig["ad_images"] == ATTRIBUTE_OPTIONAL ? "(" . JText::_('LNG_OPTIONAL') . ")" : "" ?></h2>
                                    <div>
                                        <?php echo JText::_('LNG_ADD_LISTING_AD_TEXT'); ?>
                                    </div>

                                    <div class="row pt-3">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="ad_caption"><?php echo JText::_('LNG_ADD_LISTING_CAPTION') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["ad_images"]) ?></label>
                                                <input type="text" name="ad_caption" id="ad_caption" class="form-control <?php echo $attributeConfig["ad_images"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->ad_caption); ?>" maxlength="245">
                                            </div>
                                        </div>
                                    </div>

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
                                                <label for="ad-imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD") ?></label>
                                                <a name="" id="" class="" href="javascript:uploadInstance.removeImage('ad-')" role="button"><i class="la la-trash"></i></a>
                                            </div>
                                            <div class="">
                                                <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                            </div>
                                        </div>
                                        <input type="text" name="ad_image" style="visibility:hidden;height:1px;width: 1px" id="ad-imageLocation" class="form-control <?php echo $required ?> " value="<?php echo $this->item->ad_image ?>"><br />
                                        <div class="jupload-footer">
                                            <fieldset>
                                                <input type="hidden" value="2097152" name="MAX_FILE_SIZE">
                                                <input type="file" id="ad-imageUploader" name="uploadLogo" size="50">
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <?php
                        }

                        if ($attributeConfig["video"] != ATTRIBUTE_NOT_SHOW) {
                            if (!$enablePackages || isset($this->item->package->features) && in_array(VIDEOS, $this->item->package->features)) { ?>
                                <fieldset class="boxed">
                                    <h2> <?php echo JText::_('LNG_COMPANY_VIDEOS'); ?></h2>
                                    <p> <?php echo JText::_('LNG_COMPANY_VIDEO_INFORMATION_TEXT'); ?></p>
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
                                        <a id="add-video" class="new-item-btn" href="javascript:void(0);" onclick="uploadInstance.addVideo('withTitle')"><?php echo JText::_('LNG_ADD_VIDEO') ?></a>
                                    </div>
                                </fieldset>
                            <?php
                            } ?>
                        <?php
                        } ?>

                        <?php if($attributeConfig["sounds"]!=ATTRIBUTE_NOT_SHOW &&
                                (!$enablePackages || isset($this->item->package->features) && in_array(SOUNDS_FEATURE,$this->item->package->features))) {
                        ?>
                            <fieldset class="boxed">
                                <div>
                                    <h2> <?php echo JText::_('LNG_COMPANY_SOUNDS');?></h2>
                                    <p> <?php echo  htmlentities(JText::_('LNG_COMPANY_SOUNDS_INFORMATION_TEXT')); ?>.</p>
                                    <input type="hidden" name="sounds-included" value="1"/>
                                    <div class="form-container">
                                        <div id="sound-container">
                                            <?php if(count($this->item->sounds) == 0){?>
                                                <div class="form-group" id="soundDetailBox0">
                                                    <?php if($attributeConfig["sounds"] == ATTRIBUTE_MANDATORY){?>
                                                        <div  class="form-detail req"></div>
                                                    <?php }?>
                                                    <label for="sound1"><?php echo JText::_('LNG_SOUND')?> <?php echo JBusinessUtil::showMandatory($attributeConfig["sounds"]) ?></label>
                                                    <div class="input-group">
                                                        <input type="text" name="sounds[]" id="0" placeholder="<?php echo JText::_('LNG_SOUND_URL');?>" value="" class="form-control <?php echo $attributeConfig["sounds"] == ATTRIBUTE_MANDATORY?"validate[required]":""?>" maxlength="450"/>
                                                        <div class="input-group-append">
                                                            <button class="input-group-text btn-secondary" alt="Delete sound" onclick="uploadInstance.removeRow('soundDetailBox0')">
                                                                <i class="la la-lg la-remove"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php $index = 0;
                                            if(count($this->item->sounds)>0){
                                                foreach($this->item->sounds as $sound) { ?>
                                                    <div id="soundDetailBox<?php echo $index ?>" class="sound-details-box">
                                                        <label for="<?php echo $sound->id?>"><?php echo JText::_('LNG_SOUND')?> <?php echo JBusinessUtil::showMandatory($attributeConfig["sounds"]) ?></label>
                                                        <div class="input-group">
                                                            <input type="text" name="sounds[]" id="<?php echo $sound->id?>" value="<?php echo $this->escape($sound->url)?>" class="form-control <?php echo $attributeConfig["sounds"] == ATTRIBUTE_MANDATORY?"validate[required]":""?>" maxlength="450" />
                                                            <div class="input-group-append">
                                                                <button class="input-group-text btn-secondary" alt="Delete sound" onclick="uploadInstance.removeRow('soundDetailBox<?php echo $index++; ?>')">
                                                                    <i class="la la-lg la-remove"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <a id="add-sound" class="new-item-btn" href="javascript:void(0);" onclick="uploadInstance.addSound()"><?php echo JText::_('LNG_ADD_SOUND') ?></a>
                                </div>
                            </fieldset>
                        <?php } ?>


                        <?php if (
                            $this->appSettings->enable_attachments && ((!$enablePackages || isset($this->item->package->features) && in_array(ATTACHMENTS, $this->item->package->features)))
                            && $attributeConfig["attachments"] != ATTRIBUTE_NOT_SHOW
                        ) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_ATTACHMENTS'); ?></h2>
                                <p> <?php echo JText::_('LNG_ATTACHMENTS_INFORMATION_TEXT'); ?>.</p>
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
                            </fieldset>
                        <?php } ?>

                        <?php if ($attributeConfig["testimonials"] != ATTRIBUTE_NOT_SHOW && (!$enablePackages || isset($this->item->package->features) && in_array(TESTIMONIALS, $this->item->package->features))) { ?>
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_COMPANY_TESTIMONIALS'); ?></h2>
                                <p> <?php echo JText::_('LNG_COMPANY_TESTIMONIALS_TEXT'); ?></p>
                                <span id="testimonial_details">
                                    <?php $i = 1;
                                    foreach ($this->item->testimonials as $testimonial) { ?>
                                        <div class="form-container" id="testimonial-form-box<?php echo $i ?>">
                                            <span id="testimonial_header<?php echo $i ?>">
                                                <h3><?php echo JText::_('LNG_TESTIMONIAL') . ' ' . $i; ?></h3>&nbsp;&nbsp;
                                                <a <?php echo $i == 1 ? 'style="display:none;"' : ''; ?> class="right remove-testimonial" href="javascript:jbdListings.removeTestimonial(<?php echo $i; ?>)">(<?php echo JText::_('LNG_REMOVE_TESTIMONIAL'); ?>)</a>
                                            </span>
                                            <div class="form-group">
                                                <?php if ($attributeConfig["testimonials"] == ATTRIBUTE_MANDATORY) { ?>
                                                    <div class="form-detail req"></div>
                                                <?php } ?>
                                                <label for="testimonial_title"><?php echo JText::_('LNG_TITLE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["testimonials"]) ?></label>
                                                <input type="text" name="testimonial_title[]" id="testimonial_title<?php echo $i ?>" class="form-control <?php echo $attributeConfig["testimonials"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $testimonial->testimonial_title ?>" maxlength="50">
                                            </div>
                                            <div class="form-group">
                                                <?php if ($attributeConfig["testimonials"] == ATTRIBUTE_MANDATORY) { ?>
                                                    <div class="form-detail req"></div>
                                                <?php } ?>
                                                <label for="testimonial_name"><?php echo JText::_('LNG_NAME') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["testimonials"]) ?></label>
                                                <input type="text" name="testimonial_name[]" id="testimonial_name<?php echo $i ?>" class="form-control <?php echo $attributeConfig["testimonials"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $testimonial->testimonial_name ?>" maxlength="50">
                                            </div>
                                            <div class="form-group">
                                                <?php if ($attributeConfig["testimonials"] == ATTRIBUTE_MANDATORY) { ?>
                                                    <div class="form-detail req"></div>
                                                <?php } ?>
                                                <label for="testimonial_description"><?php echo JText::_('LNG_DESCRIPTION') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["testimonials"]) ?></label>
                                                <textarea name="testimonial_description[]" id="testimonial_description<?php echo $i ?>" class="form-control <?php echo $attributeConfig["testimonials"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" cols="75" rows="5" maxLength="450"><?php echo $testimonial->testimonial_description ?></textarea>
                                            </div>

                                            <input type="hidden" name="testimonial_id[]" id="testimonial_id<?php echo $i ?>" value="<?php echo $testimonial->id ?>" />
                                            <hr />
                                        </div>
                                    <?php
                                        $i++;
                                    }
                                    $totalTestimonials = $i - 1; ?>
                                    <?php ?>
                                </span>
                                <span id="total_testimonials" style="display:none;"><?php echo $totalTestimonials ?></span>
                                <a id="add_testimonial" class="new-item-btn" href="javascript:void(0)" onclick="jbdListings.addNewTestimonial('<?php echo $i - 1 ?>')"><?php echo JText::_('LNG_ADD_NEW_TESTIMONIAL'); ?> + </a>
                            </fieldset>
                        <?php
                        } ?>
                    </div>

                    <div id="edit-tab6" class="edit-tab">
                        <!-- Related companies Field-->
                        <?php if (!isset($isProfile)) {
                            if ($attributeConfig["related_listing"] != ATTRIBUTE_NOT_SHOW  && (!$enablePackages || isset($this->item->package->features) && in_array(RELATED_COMPANIES, $this->item->package->features))) { ?>
                                <fieldset class="boxed">
                                    <h2> <?php echo JText::_('LNG_RELATED_COMPANIES'); ?><?php echo JBusinessUtil::showMandatory($attributeConfig["related_listing"]) ?></h2>
                                    <p><?php echo JText::_('LNG_SELECT_RELATED_COMPANIES'); ?></p>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <div id="showSearchedCompanies">
                                                <input type="hidden" name="process-listings" id="process-listings" value="1" />
                                                <select id="related-listings" name="related-listings[]" multiple class="ajax-chosen-select <?php echo $attributeConfig["related_listing"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>">
                                                    <?php
                                                    if (!empty($this->item->selectedCompanies))
                                                        echo JHtml::_('select.options', $this->item->companyRelatedOptions, 'value', 'text', $this->item->companyRelatedOptions); ?>
                                                </select>
                                                <div>
                                                    <a href="javascript:jbdUtils.uncheckAll('related-listings')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                                </div>
                                            </div>
                                            <div id="norelatedcomp"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>
                                </fieldset>
                            <?php } ?>
                            <!-- End Related companies Field-->
                        <?php } ?>

                        <?php if ($attributeConfig["custom_tab"] != ATTRIBUTE_NOT_SHOW) { ?>
                            <?php if (!$enablePackages || isset($this->item->package->features) && in_array(CUSTOM_TAB, $this->item->package->features)) { ?>
                                <fieldset class="boxed">
                                    <h2> <?php echo JText::_('LNG_ADDITIONAL_TAB'); ?></h2>
                                    <p> <?php echo JText::_('LNG_ADDITIONAL_TAB_TXT'); ?></p>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label for="custom_tab_name"><?php echo JText::_('LNG_CUSTOM_TAB_NAME') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["custom_tab"]) ?></label>
                                            <?php
                                            if ($this->appSettings->enable_multilingual) {
                                                echo $jbdTabs->startTabSet('tab_group_custom_tab_name');
                                                foreach ($this->languages as $k => $lng) {
                                                    echo $jbdTabs->addTab('tab_group_custom_tab_name', 'tab-' . $lng, $k);
                                                    $langContent = isset($this->customTabTranslation[$lng . "_name"]) ? $this->customTabTranslation[$lng . "_name"] : "";
                                                    $required = $attributeConfig["custom_tab"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "";
                                                    if ($lng == JBusinessUtil::getLanguageTag()) {
                                                        if (empty($langContent)) {
                                                            $langContent = $this->item->custom_tab_name;
                                                        }
                                                    }
                                                    echo "<input type='text' name='custom_tab_name_$lng' id='custom_tab_name_$lng' class='form-item-name form-control $required' value=\"" . $this->escape($langContent) . "\"  maxLength='100'>";
                                                    echo $jbdTabs->endTab();
                                                }
                                                echo $jbdTabs->endTabSet();
                                            } else { ?>
                                                <input type="text" name="custom_tab_name" id="custom_tab_name" class="form-control text-input <?php echo $attributeConfig["custom_tab"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $this->escape($this->item->custom_tab_name) ?>" maxlength="100">
                                            <?php } ?>

                                        </div>
                                        <div class="form-group">
                                            <label for="custom_tab_content"><?php echo JText::_('LNG_CUSTOM_TAB_DESCRIPTION') ?></label>
                                            <?php
                                            if ($this->appSettings->enable_multilingual) {
                                                echo $jbdTabs->startTabSet('tab_group_custom_description');
                                                foreach ($this->languages as $k => $lng) {
                                                    echo $jbdTabs->addTab('tab_group_custom_description', 'tab-' . $lng, $k);
                                                    $langContent = isset($this->customTabTranslation[$lng]) ? $this->customTabTranslation[$lng] : "";
                                                    if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                                        $langContent = $this->item->custom_tab_content;
                                                    }
                                                    echo JBusinessUtil::getEditor()->display('custom_tab_content_' . $lng, $langContent, '100%', '300', '70', '10', false);
                                                    echo $jbdTabs->endTab();
                                                }
                                                echo $jbdTabs->endTabSet();
                                            } else {
                                                echo JBusinessUtil::getEditor()->display('custom_tab_content', $this->item->custom_tab_content, '100%', '300', '70', '10', false);
                                            } ?>
                                            <?php
                                            ?>
                                        </div>
                                    </div>
                                    <span class="error_msg" id="frmCustomTab_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                </fieldset>
                            <?php } ?>
                        <?php } ?>
                    </div>
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
                                                        <td><?php echo $log->display_name ?></td>
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
            <?php if (isset($isProfile)) { ?>
                <?php if ($this->item->id == 0) { ?>
                    <div>
                        <?php echo JBusinessUtil::renderTermsAndConditions(); ?>
                    </div>
                <?php } ?>
                <?php if ($isNew && !$showTabs || !$isNew) { ?>
                    <div class="button-row">
                        <button type="button" class="btn btn-success button-save" onclick="saveCompanyInformation('apply');">
                            <i class="la la-edit"></i> <?php echo JText::_("LNG_SAVE") ?>
                        </button>
                        <button type="button" class="btn btn-success button-close" onclick="saveCompanyInformation('save');">
                            <span class="ui-button-text"><i class="la la-check"></i> <?php echo JText::_("LNG_SAVE_AND_CLOSE") ?></span>
                        </button>
                        <?php if (!$isNew) { ?>
                            <button type="button" class="btn btn-primary button-save" onclick="saveCompanyInformation('duplicate')">
                                <i class="icon-save-copy"></i> <?php echo JText::_("LNG_DUPLICATE") ?>
                            </button>
                        <?php } ?>
                        <button type="button" class="btn btn-dark button-cancel" onclick="cancel()">
                            <i class="la la la-close"></i> <?php echo JText::_("LNG_CANCEL") ?>
                        </button>
                    </div>
                <?php } ?>

                <?php if ($isNew && $showTabs) { ?>
                    <div class="button-row">
                        <button id="prev-btn" type="button" class="btn btn-dark" onclick="jbdTabs.previousTab();">
                            <i class="la la-angle-double-left"></i> <?php echo JText::_("LNG_PREVIOUS") ?>
                        </button>
                        <button id="next-btn" type="button" class="btn btn-success" onclick="jbdTabs.nextTab()">
                            <?php echo JText::_("LNG_NEXT") ?> <i class="la la-angle-double-right"></i>
                        </button>
                        <button id="save-btn" type="button" class="btn btn-success button-close" onclick="saveCompanyInformation('save')">
                            <i class="la la-edit"></i> <?php echo JText::_("LNG_SAVE") ?>
                        </button>
                        <button type="button" class="btn btn-outline-dark button-cancel" onclick="cancel()">
                            <i class="la la la-close"></i> <?php echo JText::_("LNG_CANCEL") ?>
                        </button>
                    </div>
                <?php } ?>
            <?php } ?>
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
