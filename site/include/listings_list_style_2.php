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
<div id="jbd-results-list-container" itemscope itemtype="http://schema.org/ItemList" class="list-style-2" <?php echo $appSettings->search_view_mode?'style="display: none"':'' ?>>
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
                                <div class="col-lg-3 pr-lg-0">
                                    <div class="item-image-cover" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                        <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>">
                                            <?php if (!empty($company->logoLocation)) { ?>
                                                <img title="<?php echo $this->escape($company->name) ?>" alt="<?php echo $this->escape($company->name) ?>" src="<?php echo BD_PICTURES_PATH . $company->logoLocation ?>" itemprop="contentUrl">
                                            <?php } else { ?>
                                                <img  title="<?php echo $this->escape($company->name) ?>" alt="<?php echo $this->escape($company->name) ?>" src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>" itemprop="contentUrl">
                                            <?php } ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-lg d-flex align-items-center">
                                <div class="result-content <?php echo !$showLogo?"no-image":""?>">
                                    <div class="result-content-top">
                                        <div class="row result-header-row">
                                            <div class="col-lg-7">
                                                <?php if (isset($company->featured) && $company->featured == 1) { ?>
                                                    <div class="featured-text">
                                                        <?php echo JText::_("LNG_FEATURED") ?>
                                                    </div>
                                                <?php } ?>

                                                <h3 class="item-name">
                                                    <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><?php echo $enableNumbering ? "<span>" . ($index + $limitStart + 1) . ". </span>" : "" ?><span itemprop="name"><?php echo $company->name ?> </span></a>
                                                </h3>
                                                <span style="display:none;" itemprop="url"><?php echo JBusinessUtil::getCompanyLink($company) ?></span>
                                                <?php if (!empty($company->slogan)) { ?>
                                                    <div class="item-slogan"><?php echo $company->slogan ?></div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="result-header-info">
                                                    <?php if (!empty($company->recommended) && $appSettings->show_recommended) { ?>
                                                        <div class="recommended-text" title="<?php echo JText::_("LNG_RECOMMENDED") ?>"><i class="la la-check"></i> <?php echo JText::_("LNG_RECOMMENDED") ?></div>
                                                    <?php } ?> 
                                                    <div class="company-rating" <?php echo !$enableRatings ? 'style="display:none"' : '' ?>>
                                                        <?php if ($appSettings->enable_ratings) { ?>
                                                            <div class="rating">
                                                                <p class="rating-average" title="<?php echo $company->review_score ?>" id="<?php echo $company->id ?>" style="display: block;"></p>
                                                            </div>
                                                            <?php if (!empty($company->review_score)) { ?>
                                                                <div class="review-count" <?php echo $company->review_score == 0 ? 'style="display:none"':'' ?>>
                                                                    <span> <?php echo $company->nr_reviews." ".JText::_("LNG_REVIEWS");?></span>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item-options">
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
                                            <?php if (!empty($company->customAttributes) && $appSettings->show_custom_attributes) { ?>
                                                <?php
                                                    $renderedContent = AttributeService::renderAttributesSearchResults($company->customAttributes, $enablePackages, $company->packageFeatures);
                                                    echo $renderedContent;
                                                ?>
                                            <?php } ?>
                                        </div>

                                        <?php if(!empty($company->short_description)){ ?>
                                            <div class="company-intro">
                                                <?php echo $company->short_description; ?>
                                                <a <?php echo $newTab; ?> href="<?php echo JBusinessUtil::getCompanyLink($company) ?>"><?php echo JText::_('LNG_MORE_INFO') ?></a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="result-content-bottom">
                                        <div class="row">
                                            <div class="col-lg-7">
                                                <div class="listing-actions">
                                                    <?php if ($appSettings->show_contact_form) { ?>
                                                        <a class="btn btn-outline-dark" title="<?php echo JText::_('LNG_CONTACT_COMPANY') ?>" href="javascript:jbdListings.showContactCompanyList(<?php echo $company->id ?>,<?php echo $showData ? "1" : "0" ?>, '<?php echo $company->name ?>', '<?php echo $company->logoLocation ?>',  '<?php echo $company->business_cover_image ?>', <?php echo $company->review_score ?>)"> <i class="la la-envelope"></i> <?php echo JText::_('LNG_MESSAGE') ?></a>
                                                    <?php } ?>

                                                    <?php if ($appSettings->enable_request_quote) { ?>
                                                        <a class="btn btn-outline-dark" title="<?php echo JText::_('LNG_REQUEST_QUOTE') ?>" href="javascript:jbdListings.showQuoteCompany(<?php echo $company->id ?>,<?php echo $showData ? "1" : "0" ?>)"><i class="la la-edit"></i> <?php echo JText::_('LNG_QUOTE') ?></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <?php require "listings_actions.php"; ?>
                                            </div>
                                        </div>
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