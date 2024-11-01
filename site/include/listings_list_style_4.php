<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once BD_CLASSES_PATH . '/attributes/attributeservice.php';

$appSettings = JBusinessUtil::getApplicationSettings();
$enableSEO = $appSettings->enable_seo;
$enablePackages = $appSettings->enable_packages;
$enableRatings = $appSettings->enable_ratings;
$enableNumbering = $appSettings->enable_numbering;
$user = JBusinessUtil::getUser();

$limitStart = JFactory::getApplication()->input->getInt('limitstart', 0);
if (empty($limitStart)) {
	$limitStart  = JFactory::getApplication()->input->get('start', 0);
}

if(!$appSettings->search_results_loading) {
    $total_page_string = $this->pagination->getPagesCounter();
    $current_page = substr((string)$total_page_string, 5, 1);
    if (($current_page === 1) || $total_page_string==null) {
        $limitStart = 0;
    }
}

$showData = !($user->ID == 0 && $appSettings->show_details_user == 1);

?>
<div id="jbd-results-list-container" itemscope itemtype="http://schema.org/ItemList" class="list-style-4" <?php echo $appSettings->search_view_mode?'style="display: none"':'' ?>>
    <?php
        if (!empty($this->companies)) {
            $itemCount = 0;
            foreach ($this->companies as $index => $company) {
                $showLogo = $this->defaultAttributes["logo"] != ATTRIBUTE_NOT_SHOW && (isset($company->packageFeatures) && in_array(SHOW_COMPANY_LOGO, $company->packageFeatures) || !$enablePackages);
    ?>
        <?php
            if (!empty($searchModules) && isset($searchModules[$index])) {
                foreach ($searchModules[$index] as $module) {
        ?>
                    <div class="search-result-module">
                        <?php echo JModuleHelper::renderModule($module, array("style"=>"xhtml")); ?>
                    </div>
            <?php } ?>
        <?php } ?>

            <div class="result-item <?php echo isset($company->featured) && $company->featured == 1 ? "featured" : "" ?>" style="<?php echo !empty($company->featured) && !empty($appSettings->listing_featured_bg) ? "background-color: $appSettings->listing_featured_bg" : "" ?>">
                <div itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
                    <span style="display:none;" itemprop="position"><?php echo $itemCount ?></span>
                    <div itemscope itemprop="item" itemtype="http://schema.org/LocalBusiness">
                        <div class="row">
                            <?php if ($showLogo) { ?>
                                <div class="col-lg-3 pr-0 d-flex justify-content-center">
                                    <div class="item-image-border">
                                        <div class="item-image" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                            <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>">
                                                <?php if (!empty($company->logoLocation)) { ?>
                                                    <img title="<?php echo $this->escape($company->name) ?>" alt="<?php echo $this->escape($company->name) ?>" src="<?php echo BD_PICTURES_PATH . $company->logoLocation ?>" itemprop="contentUrl">
                                                <?php } else { ?>
                                                    <img  title="<?php echo $this->escape($company->name) ?>" alt="<?php echo $this->escape($company->name) ?>" src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>" itemprop="contentUrl">
                                                <?php } ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-lg d-flex p-lg-0">
                                <div class="result-content <?php echo !$showLogo?"no-image":""?>">
                                    <div>
                                        <?php if (isset($company->featured) && $company->featured == 1) { ?>
                                            <div class="featured-text">
                                                <?php echo JText::_("LNG_FEATURED") ?>
                                            </div>
                                        <?php } ?>

                                        <div class="item-name-container">
                                            <div class="company-rating" <?php echo !$enableRatings ? 'style="display:none"' : '' ?>>
                                                <?php if ($appSettings->enable_ratings) { ?>
                                                    <div class="rating">
                                                        <p class="rating-average" title="<?php echo $company->review_score ?>" id="<?php echo $company->id ?>" style="display: block;"></p>
                                                    </div>
                                                    <div class="review-count">
                                                        <a <?php echo $company->review_score == 0 ? 'style="display:none"' : '' ?>></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <h3 class="item-name">
                                                <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><?php echo $enableNumbering ? "<span>" . ($index + $limitStart + 1) . ". </span>" : "" ?><span itemprop="name"><?php echo $company->name ?> </span></a>
                                                <?php if (!empty($company->recommended) && $appSettings->show_recommended) { ?>
                                                    <div class="recommended" title="<?php echo JText::_("LNG_RECOMMENDED") ?>"><i class="la la-check"></i> </div>
                                                <?php } ?> 
                                            </h3>
                                            <?php if (!empty($company->typeName)) { ?>
                                                <div class="item-type"><?php echo $company->typeName ?></div>
                                            <?php } ?>
                                        </div>

                                        <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>
                                        <div class="item-options ">
                                            <?php if (!empty($company->customAttributes) && $appSettings->show_custom_attributes) { ?>
                                                <?php
                                                    $renderedContent = AttributeService::renderAttributesSearchResults($company->customAttributes, $enablePackages, $company->packageFeatures);
                                                    echo $renderedContent;
                                                ?>
                                            <?php } ?>
                                            <?php
                                                $address = JBusinessUtil::getShortAddress($company);
                                                if (!empty($company->bestMatchLocation)) {
                                                    $address = $company->bestMatchLocation;
                                                    $address .= " <a target=\"_blank\" href=\"" . JBusinessUtil::getCompanyLink($company) . "\"> (" . JText::_("LNG_VIEW_ALL_LOCATIONS") . ")</a>";
                                                }
                                            ?>

                                            <?php if (!empty($address)) { ?>
                                                <div class="item-option">
                                                    <span itemprop="address"><i class="icon map-marker"></i>&nbsp;<?php echo $address ?></span>

                                                    <?php if (!empty($company->distance)) { ?>
                                                        <span>
                                                            <?php echo JText::_("LNG_DISTANCE") . ": " . round($company->distance, 1) . " " . ($appSettings->metric == 1 ? JText::_("LNG_MILES") : JText::_("LNG_KM")) ?>
                                                        </span>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <?php if(!empty($company->short_description)){ ?>
                                            <div class="company-intro">
                                                <?php echo $company->short_description; ?>
                                                <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><?php echo JText::_('LNG_MORE_INFO') ?></a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="result-actions-container">
                                    <div class="result-actions">
                                        <?php if ($showData && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$enablePackages)) { ?>
                                            <?php if (!empty($company->phone)) { ?>
                                                <a class="item-action" itemprop="telephone" href="tel:<?php echo $this->escape($company->phone); ?>" title="<?php echo JText::_('LNG_CALL') ?> <?php echo $this->escape($company->name) ?>"><i class="icon phone"></i> <?php echo $this->escape($company->phone); ?></a>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($showData && (isset($company->packageFeatures) && in_array(CONTACT_FORM, $company->packageFeatures) || !$enablePackages)) { ?>
                                            <?php if ($appSettings->show_contact_form) { ?>
                                                <a class="item-action" href="javascript:jbdListings.showContactCompanyList(<?php echo $company->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $company->name ?>', '<?php echo $company->logoLocation ?>',  '<?php echo $company->business_cover_image ?>', <?php echo $company->review_score ?>)" title="<?php echo JText::_('LNG_CONTACT') ?> <?php echo $this->escape($company->name) ?>"><i class="icon envelope"></i> <?php echo JText::_('LNG_CONTACT') ?></a>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if ($showData && !empty($company->website) && (isset($company->packageFeatures) && in_array(WEBSITE_ADDRESS, $company->packageFeatures) || !$enablePackages)) {
                                            if ($appSettings->enable_link_following) {
                                                $followLink = (isset($company->packageFeatures) && in_array(LINK_FOLLOW, $company->packageFeatures) && $enablePackages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
                                            } else {
                                                $followLink = 'rel="noopener"';
                                            } ?>
                                                <a class="item-action" target="_blank" <?php echo $followLink ?> title="<?php echo $this->escape($company->name) ?> <?php echo JText::_('LNG_WEBSITE') ?>" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $this->escape($company->website) ?>"><i class="icon link-round"></i> <?php echo JText::_('LNG_WEBSITE') ?></a>
                                        <?php } ?>

                                        <?php if ($appSettings->show_contact_cards && false) { ?>
                                            <div class="contact-card">
                                                <a class="item-action" rel="nofollow" target="_blank" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateQrCode&itemId=" . $company->id); ?>"><i class="icon qr-code"></i><?php echo JText::_("LNG_QR_CODE") ?></a>
                                                <a class="item-action" rel="nofollow" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateVCard&itemId=" . $company->id); ?>"><i class="icon vcard"></i><?php echo JText::_("LNG_VCARD") ?></a>
                                            </div>
                                        <?php } ?>

                                        <a class="item-action dark text-center" <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><?php echo JText::_("LNG_VIEW_PROFILE")?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $itemCount++; ?>
        <?php
        } ?>
    <?php
    } ?>
</div>