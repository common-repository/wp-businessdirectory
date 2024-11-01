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
$editSimple = true;

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
$tabIndex = 1;
?>

<script type="text/javascript">
    window.addEventListener('load', function() {
        JBD.submitbutton = function(task) {

            jQuery("#item-form #task").val("");

            jQuery("#item-form").validationEngine('detach');
            if (jbdUtils.getProperty("isMultilingual")) {
                jQuery(".tab-" + jbdUtils.getProperty("defaultLang")).each(function() {
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

<div id="jbd-container" class="jbd-container jbd-edit-container simple-edit-form">
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
        <?php if ($enablePackages) { ?>
            <?php
                $packageId = $jinput->get("filter_package");
                if (empty($packageId) && !empty($this->item->package)) {
                    $packageId = $this->item->package->id;
                }
            ?>
            <input type="hidden" name="filter_package" id="filter_package" value="<?php echo $packageId ?>" />
        <?php } ?>
        <div id="form-mode-container">
            <div class="row">
                <div class="<?php echo (!isset($isProfile) || isset($isProfile) && $this->appSettings->front_end_meta_data && !$isNew) ? "col-lg-8" : "col-md-12" ?>">
                   
                    <div id="validation-error" class="validation-error row" style="display:none">
                        <i class="la la-warning"></i> <?php echo JText::_("LNG_VALIDATION_ERROR") ?>
                    </div>

                    <?php if ($showTabs) { ?>
                        <div id="simple-process-tabs" class="simple-process-tabs">
                            <div id="tab1" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(1)"' : '' ?>>
                                <div class="process-tab-content">
                                    <span class="tab-counter"><?php echo $tabIndex++ ?></span> <?php echo JText::_("LNG_SIMPLE_TAB_1") ?>
                                </div>
                            </div>

                            <?php 
                                if ($attributeConfig["category"] != ATTRIBUTE_NOT_SHOW
                                    || ((isset($attributeConfig["custom_attributes"]) && $attributeConfig["custom_attributes"] != ATTRIBUTE_NOT_SHOW) && (((!empty($this->item->customFields) && $this->item->containsCustomFields) || $this->appSettings->enable_attribute_category) && (!$enablePackages || isset($this->item->package) && $this->item->package->max_categories > 0)))
                                    || ($attributeConfig["opening_hours"] != ATTRIBUTE_NOT_SHOW) && (!$enablePackages || isset($this->item->package->features) && in_array(OPENING_HOURS, $this->item->package->features))){
                            ?>       
                                <div id="tab2" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(2)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <span class="tab-counter"><?php echo $tabIndex++ ?></span> <?php echo JText::_("LNG_SIMPLE_TAB_2") ?>
                                    </div>
                                    <?php $presentTabs[] =2; ?>
                                </div>
                            <?php } ?>

                            <?php 
                                if ((($attributeConfig["mobile_phone"] != ATTRIBUTE_NOT_SHOW ||
                                    $attributeConfig["phone"] != ATTRIBUTE_NOT_SHOW ||
                                    $attributeConfig["fax"] != ATTRIBUTE_NOT_SHOW) && (!$enablePackages || isset($this->item->package->features) && in_array(PHONE, $this->item->package->features)))
                                    || $attributeConfig["email"] != ATTRIBUTE_NOT_SHOW
                                    || $showLocation){
                            ?>
                                <div id="tab3" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(3)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <span class="tab-counter"><?php echo $tabIndex++ ?></span> <?php echo JText::_("LNG_SIMPLE_TAB_3") ?>
                                    </div>
                                    <?php $presentTabs[] = 3; ?>
                                </div>
                            <?php } ?>

                            <?php if ($attributeConfig["logo"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["cover_image"] != ATTRIBUTE_NOT_SHOW) { ?>
                                <div id="tab4" class="process-tab <?php echo !$isNew ? "tab-active" : "" ?>" <?php echo !$isNew ? 'onclick="jbdTabs.openTab(4)"' : '' ?>>
                                    <div class="process-tab-content">
                                        <span class="tab-counter"><?php echo $tabIndex++ ?></span> <?php echo JText::_("LNG_SIMPLE_TAB_4") ?>
                                        <?php $presentTabs[] = 4; ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="edit-tab-container">
                        <div id="edit-tab1" class="edit-tab">
                            <fieldset class="">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group title">
                                            <h2> <?php echo JText::_('LNG_COMPANY_DETAILS'); ?></h2>
                                            <p>
                                                <?php echo JText::_('LNG_DISPLAY_INFO_TXT'); ?><br />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-divider"></div>
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
                                    </div>

                                    <?php if ($attributeConfig["website"] != ATTRIBUTE_NOT_SHOW || $attributeConfig["type"] != ATTRIBUTE_NOT_SHOW) { ?>
                                        <div class="row">
                                            <?php if ($attributeConfig["website"] != ATTRIBUTE_NOT_SHOW) { ?>
                                                <?php if (!$enablePackages || isset($this->item->package->features) && in_array(WEBSITE_ADDRESS, $this->item->package->features)) { ?>
                                                    <div class="col-md">
                                                        <div class="form-group">
                                                            <label for="website"><?php echo JText::_('LNG_WEBSITE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["website"]) ?></label>
                                                            <input type="text" name="website" id="website" value="<?php echo $this->escape($this->item->website) ?>" class="form-control <?php echo $attributeConfig["website"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" maxlength="250">
                                                        </div>
                                                    </div> 
                                                <?php } ?>
                                            <?php } ?>

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
                                                        <a href="javascript:jbdUtils.uncheckAll('companyTypes')"><?php echo JText::_("LNG_UNCHECK_ALL") ?></a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="form-divider"></div>
                                    <?php if ($attributeConfig["slogan"] != ATTRIBUTE_NOT_SHOW) { ?>
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
                                                            echo "<textarea id='slogan_$lng' name='slogan_$lng' class='form-control $required' cols='75' rows='2' maxLength='" . $maxSloganLength . "'>" . $this->escape($langContent) . "</textarea>";
                                                            echo $jbdTabs->endTab();
                                                        }
                                                        echo $jbdTabs->endTabSet();
                                                    } else { ?>
                                                        <textarea name="slogan" id="slogan" class="form-control text-input <?php echo $attributeConfig["slogan"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" cols="75" rows="2" maxLength="<?php echo $maxSloganLength ?>" onkeyup="jbdUtils.calculateLengthSlogan();"><?php echo $this->item->slogan ?></textarea>
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

                                                            echo "<textarea id='short_description_$lng' name='short_description_$lng' class='form-control $required' cols='75' rows='2' maxLength='" . $maxShortDescriptionLength . "'>$langContent</textarea>";
                                                            echo $jbdTabs->endTab();
                                                        }
                                                        echo $jbdTabs->endTabSet();
                                                    } else { ?>
                                                        <textarea name="short_description" id="short_description" class="form-control <?php echo $attributeConfig["short_description"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" cols="75" rows="2" maxLength="<?php echo $maxShortDescriptionLength ?>" onkeyup="jbdUtils.calculateLengthShort();"><?php echo $this->item->short_description ?></textarea>
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
                                <div class="form-divider"></div>
                            </fieldset>
                        </div>

                        <div id="edit-tab2" class="edit-tab" style="display:none">
                            <fieldset>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group title">
                                            <h2> <?php echo JText::_('LNG_COMPANY_CLASSIFICATION'); ?></h2>
                                            <p>
                                                <?php echo JText::_('LNG_DISPLAY_INFO_TXT'); ?><br />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-divider"></div>
                                <?php if (($attributeConfig["category"] != ATTRIBUTE_NOT_SHOW) && (!$enablePackages || isset($this->item->package) && $this->item->package->max_categories > 0)) { ?>
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
                                    <div class="form-divider"></div>
                                <?php } ?>

                                <?php if ((isset($attributeConfig["custom_attributes"]) && $attributeConfig["custom_attributes"] != ATTRIBUTE_NOT_SHOW) &&
                                        (((!empty($this->item->customFields) && $this->item->containsCustomFields) || $this->appSettings->enable_attribute_category) && (!$enablePackages || isset($this->item->package) && $this->item->package->max_categories > 0))) { ?>
                                    <?php
                                        $packageFeatures = !empty($this->item->package->features) ? $this->item->package->features : null;
                                        $renderedContent = AttributeService::renderAttributes($this->item->customFields, $enablePackages, $packageFeatures);
                                    ?>
                                    <div id="custom-attributes-section" style="<?php echo empty($renderedContent) ? "display:none;" : "" ?>">
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
                                        </div>
                                    <div class="form-divider"></div>
                                <?php } ?>

                                <?php
                                if ($attributeConfig["opening_hours"] != ATTRIBUTE_NOT_SHOW) { ?>
                                    <?php
                                    if (!$enablePackages || isset($this->item->package->features) && in_array(OPENING_HOURS, $this->item->package->features)) { ?>
                                        <!-- Work Hours Section -->
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
                                    <?php } ?>
                                <?php } ?>
                            </fieldset>
                        </div>
                            <div id="edit-tab3" class="edit-tab" style="display:none">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group title">
                                                <h2><?php echo JText::_('LNG_CONTACT_DETAILS'); ?></h2>
                                                <p><?php echo JText::_('LNG_COMPANY_LOCATION_TXT'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-divider"></div>
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

                                    <div class="form-divider"></div>

                                    <?php if ($showLocation) { ?>
                                        <div class="address-container">
                                            <?php FormService::renderAddressFields($attributeConfig, $this->item); ?>
                                            
                                            <input type="hidden" name="latitude" id="latitude" value="<?php echo $this->item->latitude ?>" maxlength="45">
                                            <input type="hidden" name="longitude" id="longitude" value="<?php echo $this->item->longitude ?>" maxlength="45">
                                        </div>
                                        <div id="map-container" style="display:none">
                                            <div id="company-map">
                                            </div>
                                        </div>

                                        <div class="form-divider"></div>
                                    <?php } ?>
                                </fieldset>
                            </div>
                        
                        <div id="edit-tab4" class="edit-tab" style="display:none">
                            <fieldset>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group title">
                                            <h2><?php echo JText::_('LNG_COMPANY_LOCATION'); ?></h2>
                                            <p><?php echo JText::_('LNG_COMPANY_LOCATION_TXT'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-divider"></div>
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
                                        <div class="form-divider"></div>
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
                                    <div class="form-divider"></div>
                                <?php } ?>
                            </fieldset>
                        </div>

                        <?php if (isset($isProfile)) { ?>
                            <?php if ($isNew && $showTabs) { ?>
                                <div class="button-row">
                                    <button id="prev-btn" type="button" class="btn btn-link" onclick="jbdTabs.previousTab();" style="display:none">
                                        <?php echo JText::_("LNG_BACK") ?>
                                    </button>
                                    <button id="next-btn" type="button" class="btn btn-success" onclick="jbdTabs.nextTab()">
                                        <?php echo JText::_("LNG_NEXT") ?>
                                    </button>
                                    <button id="save-btn" type="button" class="btn btn-success button-close" onclick="saveCompanyInformation('save')" style="display:none">
                                        <?php echo JText::_("LNG_SAVE") ?>
                                    </button>
                                </div>
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

<?php require "edit_utils.php" ?>
