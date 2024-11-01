<?php

/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once 'header.php';
require_once BD_CLASSES_PATH . '/attributes/attributeservice.php';
$address = JBusinessUtil::getAddressText($this->company); 
?>
<div class="company-container-style-6" class="listing-style-6" itemscope itemtype="http://schema.org/LocalBusiness">

    <div class="dir-print header-icons">
        <?php require_once JPATH_COMPONENT_SITE . "/include/social_share.php"; ?>
        <a rel="nofollow" href="javascript:jbdUtils.printItem('<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component&layout=print&companyId=" . $this->company->id); ?>')"><i class="icon print-circle"></i></a>
    </div>
    <?php if ($appSettings->show_contact_cards) { ?>
        <div class="dir-print">
            <a rel="nofollow" target="_blank" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateQrCode&itemId=" . $this->company->id); ?>"><i class="la la-qrcode"></i> <?php echo JText::_("LNG_QR_CODE") ?></a>
        </div>
        <div class="dir-print">
            <a rel="nofollow" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateVCard&itemId=" . $this->company->id); ?>"><i class="la la-comments"></i> <?php echo JText::_("LNG_VCARD") ?></a>
        </div>
    <?php } ?>
    <?php require_once 'breadcrumbs.php'; ?>

    <div class="company-header" style="background-image:<?php echo !empty($this->company->business_cover_image)?("url('".BD_PICTURES_PATH.$this->company->business_cover_image." ')"):("url('".BD_PICTURES_PATH.$appSettings->default_bg_listing." ')"); ?>">
        <div class="company-overlay"></div>
        <?php if($this->appSettings->show_open_status && (!$appSettings->enable_packages || isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features))) { ?>
            <?php if($this->company->opening_status == COMPANY_OPEN_BY_TIMETABLE && $this->company->enableWorkingStatus) { 
                if ($this->company->workingStatus) { ?>
                    <div class="ribbon-open"><span><?php echo JText::_("LNG_OPEN")?></span></div>
                <?php } else{ ?>
                    <div class="ribbon-close"><span><?php echo JText::_("LNG_CLOSED")?></span></div>
                <?php } ?>
            <?php } else { ?>
                <?php 
                    $statusInfo = JBusinessUtil::getOpeningStatus($this->company->opening_status); 
                    if($this->company->opening_status == COMPANY_ALWAYS_OPEN) {?>
                        <div class="ribbon-open"><span><?php echo JText::_("LNG_OPEN")?></span></div>
                    <?php } else if (in_array($this->company->opening_status, array(COMPANY_TEMP_CLOSED, COMPANY_SEASON_CLOSED, COMPANY_PERMANENTLY_CLOSED))){ ?>
                        <div class="ribbon-close"><span><?php echo JText::_("LNG_CLOSED")?></span></div>
                    <?php } ?>
            <?php } ?>
        <?php } ?>
        <div class="header-bottom">
            <div class="row">
                <div class="col-md">
                    <div class="company-header-details">
                        <span itemprop="image" style="display:none;"><?php echo (!empty($this->company->logoLocation)) ? BD_PICTURES_PATH . $this->company->logoLocation : BD_ASSETS_FOLDER_PATH . "no_image.jpg" ?></span>

                        <div class="listing-name-container">
                            <h1 itemprop="name" class="company-name">
                                <span><?php echo $this->company->name ?></span>
                                <?php if ($this->company->recommended && $appSettings->show_recommended) { ?>
                                    <span class="recommended"><i class="la la-star"></i> <?php echo JText::_("LNG_RECOMMENDED") ?></span>
                                <?php } ?>
                            </h1>
                            <?php if ($this->company->userId != 0 && $appSettings->show_claimed) { ?>
                                <span class="claimed"><i class="la la-check"></i> <?php echo JText::_("LNG_CLAIMED") ?></span>
                            <?php } ?>
                        </div>

                        <div class="company-info-review">
                            <!-- Business Ratings -->
                            <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                    && $appSettings->enable_reviews) { ?>
                                <div class="company-info-rating" <?php echo !$appSettings->enable_ratings ? 'style="display:none"' : '' ?>>
                                    <?php if (count($this->totalReviews) > 0) { ?>
                                        <span style="display:none" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                            <span>
                                                <span itemprop="itemReviewed" itemscope itemtype="http://schema.org/LocalBusiness">
                                                    <span itemprop="name"><?php echo $this->company->name ?></span>
                                                    <span itemprop="image"><?php echo BD_PICTURES_PATH . $this->company->logoLocation ?></span>
                                                    <span itemprop="address"><?php echo $address ?></span>
                                                    <span itemprop="telephone"><?php echo $this->company->phone ?></span>
                                                </span>
                                                <span itemprop="ratingValue"><?php echo $this->company->review_score ?></span> <span itemprop="worstRating">0</span><span itemprop="bestRating">5</span>
                                            </span>
                                            <span itemprop="ratingCount"><?php echo count($this->totalReviews) ?></span>
                                        </span>
                                    <?php } ?>

                                    <div class="rating">
                                        <span class="user-rating-avg" id="rating-average" title="<?php echo $company->review_score ?>" alt="<?php echo $company->id ?>" style="display: block;"></span>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="review-info">
                                <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                    && $appSettings->enable_reviews) { ?>
                                    <a href="<?php echo $url ?>#go-company-reviews"><span><?php echo count($this->totalReviews); ?> <?php echo JText::_('LNG_REVIEWS'); ?></span></a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="dir-company-meta">
                            <div class="row">
                                <div class="col-lg-8">
                                    <?php if($showData) {?>
                                        <?php if (!empty($address)) { ?>
                                            <div class="listing-address white" itemprop="address">
                                                <i class="icon map-marker"></i>
                                                <?php echo $address; ?>
                                            </div>
                                        <?php } else { ?>
                                            <div class="listing-address white" itemprop="address">
                                                <i class="icon map-marker"></i>
                                                <?php echo  JBusinessUtil::getShortAddress($this->company); ?>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-4">
                                    <div class="links-container header-icons white">
                                        <?php if ($this->appSettings->enable_bookmarks) { ?>
                                            <?php if (!empty($company->bookmark)) { ?>
                                                <!-- Business Bookmarks -->
                                                <a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID == 0 ? "1" : "0" ?>,<?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_UPDATE") ?>" class=""><i class="la la-heart"></i></a>
                                            <?php } else { ?>
                                                <a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID == 0 ? "1" : "0" ?>,<?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_SAVE") ?>" class=""><i class="la la-heart-o"></i></a>
                                            <?php } ?>
                                        <?php } ?>
                                       
                                        <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                                && $appSettings->enable_reviews) { ?>
                                            <a class="round-border-buton" href="javascript:void(0)" onclick="jbdReviews.addNewReviewOnTabs(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0" ?>)"> <?php echo JText::_('LNG_WRITE_REVIEW') ?> <i class="la la-angle-right"></i></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="company-details-container">
        <div class="row">
            <div class="col-lg-8">
                <div class="company-menu">
                    <nav>
                        <a id="details-link" href="javascript:jbdListings.showTabContent('company-details');" class="active"><?php echo JText::_("LNG_BUSINESS_DETAILS") ?></a>

                        <?php
                        if (
                            $showData &&  (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->company->latitude) && !empty($this->company->longitude)
                        ) {
                        ?>
                            <a id="gmap-link" href="javascript:jbdListings.showTabContent('company-gmap');" class=""><?php echo JText::_("LNG_MAP") ?></a>
                        <?php } ?>

                        <?php
                        if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
                            && isset($this->services) && count($this->services) && $appSettings->enable_services
                        ) {
                        ?>
                            <a id="services-link" href="javascript:jbdListings.showTabContent('company-services');" class=""><?php echo JText::_("LNG_SERVICES") ?></a>
                        <?php } ?>

                        <?php
                        if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->companyTestimonials)
                        ) {
                        ?>
                            <a id="testimonials-link" href="javascript:jbdListings.showTabContent('company-testimonials');" class=""><?php echo JText::_("LNG_TESTIMONIALS") ?></a>
                        <?php } ?>

                        <?php 
                        if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
                            && !empty($this->companyProjects)){ ?>
                            <a id="projects-link" href="javascript:jbdListings.showTabContent('company-projects');" class=""><?php echo JText::_("LNG_PROJECTS") ?></a>
                        <?php } ?>

                        <?php if (isset($this->offers) && count($this->offers) && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
                            <a id="offers-price-list-link" href="javascript:jbdListings.showTabContent('company-offers-price-list');" class=""><?php echo JText::_("LNG_OFFERS_PRICE_LIST") ?></a>
                        <?php } ?>

                        <?php if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->services_list)){
                        ?>
                            <a id="price-list-link" href="javascript:jbdListings.showTabContent('company-price-list');"><?php echo JText::_("LNG_PRICE_LIST") ?></a>
                        <?php } ?>

                        <?php
                            if((!empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) && (isset($this->package->features)
                                    && in_array(TEAM_FEATURE, $this->package->features) || !$appSettings->enable_packages) ){
                        ?>
                                <a id="team-link" href="javascript:jbdListings.showTabContent('company-team');" class=""><?php echo JText::_("LNG_TEAM")?></a>
                        <?php } ?>

                        <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					            && $appSettings->enable_reviews) { ?>
                            <a id="reviews-link" href="javascript:jbdListings.showTabContent('company-reviews');" class=""><?php echo JText::_("LNG_REVIEWS") ?></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="dir-quick-links">
                    <div class="business-contact">
                        <?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
                            <?php if (!empty($this->company->phone)) { ?>
                                <span class="phone" itemprop="telephone">
                                    <i class="icon phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
                                </span>
                            <?php } ?>
                            <?php if (empty($this->company->phone) && !empty($this->company->mobile)) { ?>
                                <span class="phone" itemprop="telephone">
                                    <i class="icon mobile"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
                                </span><br />
                            <?php } ?>
                        <?php } ?>

                        <?php if ($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
                            if ($appSettings->enable_link_following) {
                                $followLink = (isset($this->package->features) && in_array(LINK_FOLLOW, $this->package->features) && $appSettings->enable_packages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
                            } else {
                                $followLink = 'rel="noopener"';
                            } ?>
                            <span class="nowrap">
                                <i class="icon link-square"></i>
                                <a <?php echo $followLink ?> itemprop="url" class="website" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $company->website ?>">
                                    <?php echo JText::_('LNG_WEBSITE') ?>
                                </a>
                            </span>
                        <?php } else { ?>
                            <span style="display:none;" itemprop="url">
                                <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <!-- Company Details -->
                <div class="company-details-content" id="company-details">
                    <div class="row">
                        <!-- Business Details -->
                        <div class="col-md-8">
                            <div class="company-style-box">
                                <div class="row">
                                    <div class="col-md">
                                        <h3><i class="la la-newspaper"></i> <?php echo JText::_("LNG_COMPANY_DETAILS"); ?></h3>
                                        <!-- Business Slogan -->
                                        <?php if (isset($this->company->slogan) && strlen($this->company->slogan) > 2) { ?>
                                            <p class="business-slogan"><?php echo  $this->company->slogan; ?> </p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!-- Business Description -->
                                <div class="row">
                                    <div class="col-md">
                                        <div id="dir-listing-description" class="dir-listing-description" itemprop="description">
                                            <?php if (!empty($this->company->description) && (isset($this->package->features) && in_array(HTML_DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                                <?php echo JHTML::_("content.prepare", $this->company->description); ?>
                                            <?php }else if (!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                                <?php echo strip_tags($this->company->description); ?>
                                            <?php } ?>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <dl>
                                            <!-- Listing Categories -->
                                            <?php if (!empty($this->company->categoriesDetails)) { ?>
                                                <dt class="listing-category-title"><?php echo JText::_('LNG_CATEGORIES') ?>:</dt>
                                                <dd class="listing-category-content">
                                                    <?php require_once 'listing_categories.php'; ?>
                                                </dd>
                                            <?php } ?>

                                            <!-- Listing Type -->
                                            <?php if (!empty($this->company->typeName)) { ?>
                                                <dt><?php echo JText::_('LNG_TYPE'); ?>:</dt>
                                                <dd><?php echo $this->company->typeName; ?></dd>
                                            <?php } ?>

                                            <?php if (!empty($this->company->establishment_year)) { ?>
                                                <dt><?php echo JText::_('LNG_ESTABLISHMENT_YEAR'); ?>:</dt>
                                                <dd><?php echo $this->company->establishment_year; ?></dd>
                                            <?php } ?>

                                            <?php if (!empty($this->company->employees)) { ?>
                                                <dt><?php echo JText::_('LNG_EMPLOYEES'); ?>:</dt>
                                                <dd><?php echo $this->company->employees; ?></dd>
                                            <?php } ?>

                                            <!-- Business Keywords -->
                                            <?php if (!empty($this->company->keywords)) { ?>
                                                <dt><?php echo JText::_('LNG_KEYWORDS'); ?>:</dt>
                                                <dd class="dir-keywords">
                                                    <ul>
                                                        <?php
                                                        $keywords =  explode(',', $this->company->keywords);
                                                        for ($i = 0; $i < count($keywords); $i++) { ?>
                                                            <li>
                                                                <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&searchkeyword=' . $keywords[$i] . $menuItemId) ?>"><?php echo $keywords[$i] ?><?php echo $i < (count($keywords) - 1) ? ',&nbsp;' : '' ?></a>
                                                            </li>
                                                        <?php
                                                        } ?>
                                                    </ul>
                                                </dd>
                                            <?php } ?>

                                            <!-- Business Locations -->
                                            <?php if (
                                                !empty($this->company->locations) && $appSettings->show_secondary_locations == 1 &&
                                                (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)
                                            ) { ?>
                                                <dt><?php echo JText::_("LNG_COMPANY_LOCATIONS"); ?>:</dt>
                                                <dd><?php require_once 'locations.php'; ?></dd>
                                            <?php } ?>

                                            <!-- Business Attachments -->
                                            <?php if ($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                                <?php if (!empty($this->company->attachments)) { ?>
                                                    <dt><?php echo JText::_("LNG_ATTACHMENTS"); ?>:</dt>
                                                    <dd>
                                                        <?php require "listing_attachments.php" ?>
                                                    </dd>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php if ((isset($this->package->features) && in_array(CUSTOM_TAB, $this->package->features) || !$appSettings->enable_packages)
                                                && !empty($this->company->custom_tab_name)
                                            ) { ?>
                                                <dt><?php echo $this->company->custom_tab_name ?></dt>
                                                <dd><?php echo JHTML::_("content.prepare", $this->company->custom_tab_content);    ?>&nbsp;</dd>
                                            <?php } ?>
                                        </dl>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="classification">
                                            <?php require_once 'listing_attributes.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Listing announcements -->
                            <?php if ((isset($this->package->features) && in_array(ANNOUNCEMENTS, $this->package->features) || !$appSettings->enable_packages)
                                && !empty($this->companyAnnouncements) && count($this->companyAnnouncements) && $appSettings->enable_announcements
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <div>
                                                <?php require_once 'listing_announcements.php'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Listing Gallery -->
                            <?php if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
                                && !empty($this->pictures)
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <?php require_once JPATH_COMPONENT_SITE . "/include/image_gallery.php";  ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Listing Videos -->
                            <?php if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
                                && isset($this->videos) && count($this->videos) > 0
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-video-camera"></i> <?php echo JText::_("LNG_VIDEOS") ?></h3>
                                            <div>
                                                <?php require_once 'listing_videos.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }    ?>

                            <!-- Listing Offers -->
                            <?php if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
                                && isset($this->offers) && count($this->offers) && $appSettings->enable_offers
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-tag"></i> <?php echo JText::_("LNG_COMPANY_OFFERS"); ?></h3>
                                            <div id="company-offers" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
                                                <?php require_once 'listing_offers.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Listing Products -->
                            <?php if ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages)
                                && isset($this->productCategories) && count($this->productCategories)
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-tag"></i> <?php echo JText::_("LNG_PRODUCTS"); ?></h3>
                                            <div id="company-products">
                                                <?php require_once 'listing_product_categories.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Listing Events -->
                            <?php if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
                                && isset($this->events) && count($this->events) && $appSettings->enable_events
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-calendar"></i> <?php echo JText::_("LNG_COMPANY_EVENTS"); ?></h3>
                                            <div>
                                                <?php require_once 'listing_events.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Associated Events -->
                            <?php if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
                                && isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-calendar"></i> <?php echo JText::_("LNG_ASSOCIATED_EVENTS"); ?></h3>
                                            <div>
                                                <?php require_once 'listing_associated_events.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Listing Sounds -->
                            <?php if ((isset($this->package->features) && in_array(SOUNDS_FEATURE, $this->package->features) || !$appSettings->enable_packages)
                                && !empty($this->sounds)
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-music"></i> <?php echo JText::_("LNG_SOUNDS") ?></h3>
                                            <div>
                                                <?php require_once 'listing_sounds.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }    ?>

                            <!-- Related Listings -->
                            <?php if ((isset($this->package->features) && in_array(RELATED_COMPANIES, $this->package->features) || !$appSettings->enable_packages)
                                && isset($this->realtedCompanies) && count($this->realtedCompanies)
                            ) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-tag"></i> <?php echo JText::_("LNG_RELATED_COMPANIES"); ?></h3>
                                            <div class="company-cell">
                                                <?php require_once 'related_business.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($this->associatedCompanies) && $appSettings->enable_linked_listings) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-tag"></i> <?php echo JText::_("LNG_LINKED_COMPANIES"); ?></h3>
                                            <div class="company-cell">
                                                <?php require_once 'listing_associated_companies.php'; ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="col-md-4">

                            <?php if ($appSettings->social_profile && $this->company->userId != 0 && (empty($this->claimDetails) || isset($this->claimDetails) && ($this->claimDetails->status == 1))) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-user"></i> <?php echo JText::_("LNG_COMMUNITY_OWNER_PROFILE"); ?></h3>
                                            <?php
                                            require JPATH_COMPONENT_SITE . '/include/profile/listing_profile.php';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if ((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business) { ?>
                                <div class="company-style-box">
                                    <!-- Business Claim -->
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-check-square"></i> <?php echo JText::_("LNG_CLAIM_COMPANY"); ?></h3>
                                            <div><?php echo JText::_('LNG_CLAIM_COMPANY_TEXT') ?></div><br />
                                            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkUser&view=businessuser&claim_listing_id=' . $this->company->id.$menuItemId) ?>" class="btn btn-primary">
                                                <i class="la la-pencil"></i>
                                                <span class="ui-button-text"><?php echo JText::_("LNG_CLAIM_COMPANY") ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="company-style-box">
                                <!-- Business Hours -->
                                <?php if ((isset($this->package->features) && in_array(OPENING_HOURS, $this->package->features) || !$appSettings->enable_packages)
                                    && (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE
                                ) { ?>
                                    <div class="row">
                                        <div class="col-md">
                                            <?php require_once 'listing_hours.php'; ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Business Contact Informations -->
                            <div class="company-style-box ">
                                <div class="row">
                                    <div class="col-md">
                                        <!-- Business Map -->
                                        <?php if (!empty($this->company->latitude) && !empty($this->company->longitude)) { ?>
                                            <div class="dir-map-image">
                                                <?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages) && !empty($this->company->latitude) && !empty($this->company->longitude)) {
                                                    echo JBusinessUtil::getStaticMap($this->company->latitude, $this->company->longitude);
                                                } ?>
                                            </div>
                                        <?php } ?>

                                        <!-- Business Address -->
                                        <?php $address = JBusinessUtil::getAddressText($this->company); ?>
                                        <?php if (!empty($address) && $showData) { ?>
                                            <div class="">
                                                <i class="icon map-marker la-fw"></i>
                                                <address><?php echo $address; ?></address>
                                            </div>
                                        <?php } ?>

                                        <?php if ($showData &&  !empty($this->company->email) && $appSettings->show_email) { ?>
                                            <div class="" itemprop="email">
                                                <i class="icon envelope"></i> <a href="mailto:<?php echo $this->escape($this->company->email) ?>"><?php echo $this->escape($this->company->email) ?></a>
                                            </div>
                                        <?php } ?>

                                        <?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                            <?php if (!empty($this->company->phone)) { ?>
                                                <div class="phone" itemprop="telephone">
                                                    <i class="icon phone"></i> <a href="tel:<?php echo $this->company->phone; ?>" style="color:black;"><?php echo $this->company->phone; ?></a>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($this->company->mobile)) { ?>
                                                <div class="phone" itemprop="telephone">
                                                    <i class="icon mobile"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>" style="color:black;"><?php echo $this->company->mobile; ?></a>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($this->company->fax)) { ?>
                                                <span class="phone" itemprop="faxNumber">
                                                    <i class="la la-fax la-fw"></i> <?php echo $this->company->fax ?>
                                                </span>
                                            <?php } ?>

                                            <?php if ($appSettings->enable_reporting) { ?>
                                                <div>
                                                    <a href="javascript:jbdListings.showReportAbuse()" style="padding:0px;"><i class="la la-flag-o la-fw"></i> <?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($appSettings->content_responsible)) { ?>
                                                <div>
                                                    <i class="icon info-circle la-fw"></i> <a href="javascript:void(0)" id="content-responsible-link"><?php echo JText::_('LNG_CONTENT_RESPONSIBLE_PERSON') ?></a>
                                                    <div id="content_responsible_text" style="display: none;">
                                                        <?php echo JHTML::_("content.prepare", $appSettings->content_responsible); ?>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        <?php } ?>

                                        <div class="socials-network">
                                            <?php if (
                                                $showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
                                                && ((!empty($this->company->linkedin) || !empty($this->company->youtube) || !empty($this->company->facebook) || !empty($this->company->twitter)
                                                    || !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))
                                            ) { ?>
                                                <?php if (!empty($this->company->facebook)) { ?>
                                                    <a target="_blank" title="Follow us on Facebook" class="share-social la la-facebook-f" href="<?php echo $this->company->facebook ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->twitter)) { ?>
                                                    <a target="_blank" title="Follow us on Twitter" class="share-social la la-twitter" href="<?php echo $this->company->twitter ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->linkedin)) { ?>
                                                    <a target="_blank" title="Follow us on LinkedIn" class="share-social la la-linkedin" href="<?php echo $this->company->linkedin ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->skype)) { ?>
                                                    <a target="_blank" title="Skype" class="share-social la la-skype" href="skyupe:<?php echo $this->company->skype ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->youtube)) { ?>
                                                    <a target="_blank" title="Follow us on Youtube" class="share-social la la-youtube" href="<?php echo $this->company->youtube ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->instagram)) { ?>
                                                    <a target="_blank" title="Follow us on Instagram" class="share-social la la-instagram" href="<?php echo $this->company->instagram ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->tiktok)) { ?>
                                                    <a target="_blank" title="Follow us on Tiktok" class="share-social la" href="<?php echo $this->company->tiktok ?>"><i style="background:white; margin:0 0 2px 0" class="icon tiktok"></i></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->pinterest)) { ?>
                                                    <a target="_blank" title="Follow us on Pinterest" class="share-social la la-pinterest" href="<?php echo $this->company->pinterest ?>"></a>
                                                <?php } ?>
                                                <?php if (!empty($this->company->whatsapp)) { ?>
                                                    <a target="_blank" id="whatsapp-link" title="Ping us on WhatsApp" class="share-social la la-whatsapp" href="https://api.whatsapp.com/send?phone=<?php echo intval($this->company->whatsapp) ?>&text=<?php echo JText::_("LNG_HELLO") ?>!"></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Business Contact Persons Informations -->
                            <?php if ($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                <?php if (!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone))) { ?>
                                    <div class="company-style-box">
                                        <div class="row">
                                            <div class="col-md">
                                                <h3><i class="la la-user"></i> <?php echo JText::_("LNG_CONTACT_PERSONS"); ?></h3>
                                                <?php require_once 'contact_details.php'; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php  } ?>

                            <div class="company-style-box">
                                <div class="row">
                                    <div class="col-md">
                                        <?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
                                            && !empty($this->company->email) && $appSettings->enable_request_quote) { ?>
                                                <a class="btn btn-primary" href="javascript:jbdListings.showQuoteCompany('<?php echo $this->company->id?>','<?php echo $showData?"1":"0"?>')">
                                                    <i class="la la-edit"></i> <?php echo JText::_("LNG_QUOTE")?>
                                                </a>
                                        <?php } ?>

                                        <?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || !$appSettings->enable_packages) && !empty($this->company->email) && $appSettings->show_contact_form){ ?>
                                            <button type="button" class="btn btn-primary" onclick="jbdListings.contactCompany(<?php echo $showData?"1":"0"?>)">
                                                <i class="la la-envelope"></i> <?php echo !empty($this->company->userId)?JText::_("LNG_CONTACT_COMPANY"):JText::_("LNG_REQUEST_MORE_INFO") ?>
                                            </button>
                                        <?php } ?>
                                    </div>
                                    <?php if ($this->showListLinkButton && $appSettings->enable_linked_listings) { ?>
                                        <div class="col-md">
                                            <a href="javascript:jbdListings.joinListing(<?php echo count($this->joinedCompanies); ?>,<?php echo $this->company->id ?>,<?php echo ($user->ID == 0) ? 0 : 1 ?>,<?php echo count($this->userCompanies) < 2 ? false : true; ?>)" class="btn btn-primary w-100">
                                                <i class=""></i><?php echo JText::_("LNG_JOIN_LEAVE") ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="clear"></div>
                                </div>
                            </div>

                            <!-- Business Articles -->
                            <?php if (!empty($this->companyArticles) && file_exists(JPATH_SITE . '/plugins/content/business/business.php')) { ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <h3><i class="la la-newspaper"></i> <?php echo JText::_("LNG_LISTING_ARTICLES"); ?></h3>
                                            <?php require_once 'listing_articles.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <!-- Listing Memberships -->
                            <?php if ((isset($this->package->features) && in_array(MEMBERSHIPS, $this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) {  ?>
                                <div class="company-style-box">
                                    <div class="row">
                                        <div class="col-md">
                                            <?php require_once 'listing_memberships.php'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="listing-banners">
                                <?php if ( is_active_sidebar( 'wpbd-listing-details' ) ) { ?>
                                    <div class="dir-company-module company-style-box">
                                        <?php dynamic_sidebar( 'wpbd-listing-details' ); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listing Map -->
        <?php if (
            $showData && (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
            && !empty($this->company->latitude) && !empty($this->company->longitude)
        ) { ?>
            <div id="company-gmap" class="company-style-6-content">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <h3><i class="icon map-marker"></i> <?php echo JText::_("LNG_BUSINESS_MAP_LOCATION"); ?></h3>
                            <div>
                                <?php require_once 'map.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>



        <!-- Listing Services -->
        <?php if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
            && isset($this->services) && count($this->services) && $appSettings->enable_services
        ) {
        ?>
            <div id="company-services" class="company-style-6-content">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <h3><i class="la la-th-list"></i> <?php echo JText::_("LNG_SERVICES"); ?></h3>
                            <div>
                                <?php require_once 'listing_services.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Listing Testimonials -->
        <?php if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
            && !empty($this->companyTestimonials)
        ) { ?>
            <div id="company-testimonials" class="company-style-6-content">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <h3><i class="la la-quote-left "></i> <?php echo JText::_("LNG_TESTIMONIALS"); ?></h3>
                            <div>
                                <?php require_once 'listing_testimonials.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>


        <!-- Listing Price List -->
        <?php if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
            && !empty($this->services_list) && $appSettings->enable_price_list
        ) { ?>
            <div id="company-price-list" class="company-style-6-content">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <h3><i class="la la-list-alt"></i> <?php echo JText::_("LNG_PRICE_LIST"); ?></h3>
                            <div>
                                <?php require_once 'listing_price_list.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Listing Projects -->
        <?php 
        if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
            && !empty($this->companyProjects)){ ?>
            <div id="company-projects" class="company-style-6-content">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <div>
                                <?php require_once 'listing_projects.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Business Offers Price List -->
        <?php if (isset($this->offers) && count($this->offers)  && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
            <div id="company-offers-price-list" class="company-style-6-content">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <div>
                                <?php if($appSettings->offer_price_list_view_style == 1) {
                                            require_once 'listing_offers_price_list_style_2.php';
                                        } else {
                                            require_once 'listing_offers_price_list.php';
                                        }
                                ?>                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Listing Team -->
        <?php if((isset($this->package->features) && in_array(TEAM_FEATURE,$this->package->features) || !$appSettings->enable_packages )
            && !empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) { ?>
            <div id="company-team">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <h3><i class="la la-comments"></i> <?php echo JText::_("LNG_TEAM"); ?></h3>
                            <div>
                                <?php require_once 'listing_team.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Listing Reviews -->
        <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					&& $appSettings->enable_reviews) { ?>
            <div id="company-reviews">
                <div class="company-style-box">
                    <div class="row">
                        <div class="col-md">
                            <h3><i class="la la-comments"></i> <?php echo JText::_("LNG_BUSINESS_REVIEWS"); ?></h3>
                            <div id="company-reviews">
                                <?php require_once 'listing_reviews.php'; ?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!$appSettings->enable_reviews_users || !$user->ID ==0) { ?>
                    <div class="company-style-box">
                        <?php require_once 'default_addreview.php'; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<script>

    window.addEventListener('load', function() {
        <?php if (
            $showData && (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
            && !empty($this->company->latitude) && !empty($this->company->longitude)
        ) { ?>
            loadDetailsViewMap();
        <?php }    ?>

        var length = jQuery(".company-menu a").length;
        jQuery(jQuery(".company-menu a")[0]).addClass("first-tab");
        jQuery(jQuery(".company-menu a")[length - 1]).addClass("last-tab");

        jQuery(".company-menu a").each(function() {
            var name = jQuery(this).attr('id');
            name = name.substring(0, name.lastIndexOf("-"));

            if (name !== "details")
                jQuery('#company-' + name).hide();
        });
    });
</script>

<?php require_once 'listing_util.php'; ?>