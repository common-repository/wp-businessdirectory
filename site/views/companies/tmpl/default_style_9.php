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
$disableMapDefaultUI = true;

$address = JBusinessUtil::getAddressText($this->company); 
?>

<style>
    #sp-main-body {
        padding: 0;
        margin: 0;
    }

    #sp-main-body>div {
        margin: 0;
        width: auto;
        max-width: none;
    }

    @media (min-width: 768px) {
        #sp-component {
            padding: 0;
        }
    }

    .slick-slider {
        margin-bottom: 0;
    }

    #content .container:first-child{
        max-width: none!important;
        padding: 0;
        margin:0;
    }
</style>

<div id="listing-style-9" class="listing-style-9" itemscope itemtype="http://schema.org/LocalBusiness">
    <div id="company-style-9-header">
        <div class="company-style-9-header-image">
            <div class="dir-overlay"></div>
            <div class="container">
                <div class="header-body">
                    <?php $logo = BD_PICTURES_PATH . '/no_image.jpg'; ?>
                    <?php if (isset($this->package->features) && in_array(SHOW_COMPANY_LOGO, $this->package->features) || !$appSettings->enable_packages) { ?>
                        <!-- Business Logo -->
                        <?php if (!empty($this->company->logoLocation)) { ?>
                            <?php $logo = BD_PICTURES_PATH . $this->company->logoLocation; ?>
                        <?php } ?>
                    <?php } ?>
                    <div class="header-logo"  itemprop="logo" itemscope itemtype="http://schema.org/ImageObject" style="background-image: url(<?php echo $logo?>)">
                        <span itemprop="image" style="display:none;"><?php echo $logo ?></span>
                    </div>

                    <div class="header-column header-column-1">
                        <h1 class="header-title"  itemprop="name">
                            <?php echo isset($this->company->name) ? $this->escape($this->company->name) : ""; ?>
                            <?php if ($this->company->recommended && $appSettings->show_recommended) { ?>
                                <span class="recommended"><i class="la la-star"></i> <?php echo JText::_("LNG_RECOMMENDED") ?></span>
                            <?php } ?>
                            <?php if ($this->company->userId != 0 && $appSettings->show_claimed) { ?>
                                <span class="claimed"><i class="la la-check"></i> <?php echo JText::_("LNG_CLAIMED") ?></span>
                            <?php } ?>
                        </h1>
                        <p class="header-subtitle"> 
                            <?php if (!empty($this->company->slogan)) { ?>
                                <?php echo  $this->company->slogan ?>
                            <?php } ?>
                        </p>
                    </div>
     
                    <div class="header-column header-column-2">
                        <!-- Business Ratings -->
                        <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                    && $appSettings->enable_reviews) { ?>
                            <div class="header-reviews-count"><?php echo count($this->totalReviews) . " " .((count($this->totalReviews)>1)? JText::_("LNG_REVIEWS"):JText::_("LNG_REVIEW")); ?></div>
                            <div class="company-info-rating header-rating" <?php echo !$appSettings->enable_ratings ? 'style="display:none"' : '' ?>>
                                <?php if (!empty($this->reviews) > 0) { ?>
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
                                    <span class="user-rating-avg" id="rating-average" title="<?php echo $this->company->review_score ?>" alt="<?php echo $company->id ?>" style="display: block;"></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="header-column header-column-3">
                        <?php if ((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
                            <a href="javascript:jbdListings.contactCompany(<?php echo $showData ? "1" : "0" ?>)" class="btn btn-dark btn-lg d-inline-block">
                                <?php echo !empty($this->company->userId)?JText::_("LNG_SEND_MESSAGE"):JText::_("LNG_REQUEST_MORE_INFO") ?>
                            </a>
                        <?php }else if ((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business) { ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkUser&view=businessuser&claim_listing_id=' . $this->company->id.$menuItemId) ?>" class="btn btn-dark btn-lg  d-inline-block">
                                <?php echo JText::_("LNG_CLAIM_COMPANY") ?>
                            </a>
                        <?php } ?>
                    </div>

		        </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="listing-content">
            <div class="row">
                <!-- BODY -->
                <div class="col-md-8">
                    <div id="company-details" class="content-box">

                        <!-- Business Description -->
                        <div class="content-box-body">
                            <div id="dir-listing-description" class="dir-listing-description" itemprop="description">
                                <?php if (!empty($this->company->description) && (isset($this->package->features) && in_array(HTML_DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                    <?php echo JHTML::_("content.prepare", $this->company->description); ?>
                                <?php }else if (!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                    <?php echo strip_tags($this->company->description); ?>
                                <?php } ?>                            
                            </div>
                            <div class="listing-details">
                                <?php if (!empty($this->company->establishment_year)) { ?>
                                    <div class="classification">
                                        <span><?php echo " " . JText::_('LNG_ESTABLISHMENT_YEAR') ?>: <?php echo " " . $this->company->establishment_year; ?></span>
                                    </div>
                                <?php } ?>

                                <?php if (!empty($this->company->employees)) { ?>
                                    <div class="classification">
                                        <span>
                                            <?php echo " " . JText::_('LNG_EMPLOYEES') ?>: <?php echo " " . $this->company->employees; ?>
                                        </span>
                                    </div>
                                <?php } ?>

                                <?php if ($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                    <?php if (!empty($this->company->attachments)) { ?>
                                        <div class="listing-detail">
                                            <div class="listing-detail-header"><?php echo JText::_('LNG_ATTACHMENTS') ?></div>
                                            <?php require "listing_attachments.php" ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                                <div class="classification">
                                    <?php require_once 'listing_attributes.php'; ?>
                                </div>

                                <!-- Business Locations -->
                                <?php if (
                                    !empty($this->company->locations) && $appSettings->show_secondary_locations == 1 &&
                                    (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)
                                ) { ?>
                                    <dt><?php echo JText::_("LNG_COMPANY_LOCATIONS"); ?>:</dt>
                                    <dd><?php require_once 'locations.php'; ?></dd>
                                <?php } ?>

                                <?php if ((isset($this->package->features) && in_array(RELATED_COMPANIES, $this->package->features) || !$appSettings->enable_packages)
                                    && isset($this->realtedCompanies) && count($this->realtedCompanies)
                                ) {
                                ?>
                                    <div class="listing-detail related">
                                        <div class="listing-detail-header"><?php echo JText::_('LNG_RELATED') ?></div>
                                        <?php require_once 'related_business.php'; ?>
                                    </div>
                                <?php } ?>

                                <?php if (!empty($this->associatedCompanies)) { ?>
                                    <div class="listing-detail associated">
                                        <div class="listing-detail-header"><?php echo JText::_('LNG_LINKED_COMPANIES') ?></div>
                                        <?php require_once 'listing_associated_companies.php'; ?>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>

                    <?php if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
                        && ((isset($this->pictures) && count($this->pictures) > 0) || (isset($this->videos) && count($this->videos) > 0))
                    ) { ?>
                        <div class="content-box" id="company-gallery">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_GALLERY") ?></h3>
                            </div>

                            <div class="content-box-body">
                            <?php require_once JPATH_COMPONENT_SITE . "/include/image_slick_gallery_thumb.php";  ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Videos -->
                    <?php if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->videos)
                    ) { ?>
                        <div class="content-box" id="company-videos">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_VIDEOS") ?></h3>
                            </div>

                            <div class="content-box-body">
                                <?php require_once 'listing_videos.php'; ?>
                            </div>
                        </div>
                    <?php }    ?>

                    <!-- Business Offers -->
                    <?php if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->offers) && $appSettings->enable_offers
                    ) { ?>
                        <div class="content-box" id="company-offers">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_COMPANY_OFFERS"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_offers.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Products -->
                    <?php if ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages)
                        && isset($this->productCategories) && count($this->productCategories)
                    ) { ?>
                        <div class="content-box" id="company-products">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_PRODUCTS"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_product_categories.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Company Services -->
                    <?php if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
                        && isset($this->services) && count($this->services) && $appSettings->enable_services
                    ) {
                    ?>
                        <div class="content-box" id="company-services">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_SERVICES"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_services.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Events -->
                    <?php if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
                        && isset($this->events) && count($this->events) && $appSettings->enable_events
                    ) { ?>
                        <div class="content-box" id="company-events">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_COMPANY_EVENTS"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_events.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Associated Events -->
                    <?php if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
                        && isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events
                    ) { ?>
                        <div class="content-box" id="events-associated">
                            <div class="content-box-title">
                                <h3><i class="la la-calendar"></i> <?php echo JText::_("LNG_ASSOCIATED_EVENTS"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_associated_events.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Company Testimonials -->
                    <?php if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->companyTestimonials)
                    ) { ?>
                        <div class="content-box" id="company-testimonials">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_TESTIMONIALS") ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_testimonials.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Price List -->
                    <?php if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->services_list) && count($this->services_list) && $appSettings->enable_price_list
                    ) { ?>
                        <div class="content-box" id="company-price-list">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_PRICE_LIST"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_price_list.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Projects -->
                    <?php 
                    if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
                        && !empty($this->companyProjects)){ ?>
                        <div class="content-box" id="company-projects">
                            <div class="content-box-title">
                                <h3 onclick="jbdListings.returnToProjects();" onmouseover="this.style.cursor='hand';this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">
                                    <?php echo JText::_("LNG_PROJECTS"); ?>
                                </h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_projects.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Offers Price List -->
                    <?php if (isset($this->offers) && count($this->offers) && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
                        <div class="content-box" id="company-offers-price-list">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_OFFERS_PRICE_LIST"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php if($appSettings->offer_price_list_view_style == 1) {
                                            require_once 'listing_offers_price_list_style_2.php';
                                        } else {
                                            require_once 'listing_offers_price_list.php';
                                        }
                                ?>                            
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Listing Team -->
                    <?php if((isset($this->package->features) && in_array(TEAM_FEATURE,$this->package->features) || !$appSettings->enable_packages )
                        && !empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) { ?>
                       <div class="content-box" id="company-team">
                            <div class="content-box-title">
                                <h3>
                                   <?php echo JText::_("LNG_TEAM"); ?>
                                </h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_team.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Reviews -->
                    <?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					            && $appSettings->enable_reviews) { ?>
                        <div class="content-box" id="company-reviews">
                            <div class="content-box-body">
                                <?php require_once 'listing_reviews.php'; ?>
                            </div>
                        </div>
                        
                        <?php if(!$appSettings->enable_reviews_users || !$user->ID ==0) { ?>
                            <div class="content-box" id="company-reviews">
                                <div class="content-box-body">
                                    <?php require_once 'default_addreview.php'; ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>


                </div>
                <!-- SIDEBAR -->
                <div class="col-md-4">
                    <?php if ($appSettings->social_profile && $this->company->userId != 0 && (empty($this->claimDetails) || isset($this->claimDetails) && ($this->claimDetails->status == 1))) { ?>
                        <div class="content-box">
                            <div class="content-box-title">
                                <h3><?php echo JText::_("LNG_COMMUNITY_OWNER_PROFILE"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php
                                require JPATH_COMPONENT_SITE . '/include/profile/listing_profile.php';
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Hours -->
                    <?php if (!empty($this->company->ad_image)) { ?>
                        <div class="content-box">
                            <div class="content-box-body">
                                <?php require_once 'ad_image.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ((!empty($this->company->email) && $showData && $appSettings->show_email)
                        || ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages) && !empty($company->phone))
                        || ($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)
                            || (!empty($address))
                            || (($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
                                && ((!empty($this->company->linkedin) || !empty($this->company->youtube) || !empty($this->company->facebook) || !empty($this->company->twitter)
                                    || !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest || !empty($this->company->whatsapp))))))
                            ||  ((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form))
                    ) {
                    ?>

                        <div class="content-box">
                            <div class="content-box-body">
                                <?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
                                    && !empty($this->company->latitude) && !empty($this->company->longitude)
                                ) { ?>
                                     <?php $mapHeight = "250px"; ?>
                                     <?php require_once 'map.php'; ?>
                                <?php } ?>

                                <div class="content-box-title pt-3">
                                    <h3><?php echo JText::_("LNG_CONTACT_INFO"); ?></h3>
                                </div>

                                <?php ?>
                                <?php if ($showData && !empty($address)) { ?>
                                    <div class="info-detail">
                                        <?php echo JBusinessUtil::getAddressText($this->company); ?>
                                    </div>
                                <?php } ?>

                                <?php if (!empty($this->company->email) && $showData && $appSettings->show_email) { ?>
                                    <div class="info-detail">
                                        <span itemprop="email">
                                            <i class="icon envelope"></i> <a href="mailto:<?php echo $this->escape($this->company->email) ?>"><?php echo $this->escape($this->company->email) ?></a>
                                        </span>
                                    </div>
                                <?php } ?>

                                <?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                    <?php if (!empty($this->company->phone)) { ?>
                                        <div class="info-detail">
                                            <span class="phone" itemprop="telephone">
                                                <i class="icon phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
                                            </span>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($this->company->mobile)) { ?>
                                        <div class="info-detail">
                                            <span class="phone" itemprop="telephone">
                                                <i class="icon mobile"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
                                            </span>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
                                    if ($appSettings->enable_link_following) {
                                        $followLink = (isset($this->package->features) && in_array(LINK_FOLLOW, $this->package->features) && $appSettings->enable_packages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
                                    } else {
                                        $followLink = 'rel="noopener"';
                                    } ?>
                                    <div class="info-detail">
                                        <span>
                                            <i class="icon link-square"></i>
                                            <a <?php echo $followLink ?> itemprop="url" class="website" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $this->escape($company->website) ?>">
                                                <?php echo JText::_('LNG_WEBSITE') ?>
                                            </a>
                                        </span>
                                    </div>

                                <?php } else { ?>
                                    <span style="display:none;" itemprop="url">
                                        <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                                    </span>
                                <?php } ?>


                                <?php if (($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
                                    && ((!empty($this->company->linkedin) || !empty($this->company->youtube) || !empty($this->company->facebook) || !empty($this->company->twitter)
                                        || !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest || !empty($this->company->whatsapp)))))) { ?>
                                    <div class="info-detail">
                                        <?php require_once 'listing_social_networks.php'; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Announcemetns -->
                    <?php if ((isset($this->package->features) && in_array(ANNOUNCEMENTS, $this->package->features) || !$appSettings->enable_packages)
                        && !empty($this->companyAnnouncements) && count($this->companyAnnouncements) && $appSettings->enable_announcements
                    ) { ?>
                        <div class="content-box">
                            <div class="content-box-title">
                                <h3><i class="la la-list-alt"></i> <?php echo JText::_("LNG_ANNOUNCEMENTS"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_announcements.php'; ?>
                            </div>
                        </div>
                    <?php } ?>


                    <?php if (((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->company->email) && $appSettings->enable_request_quote)
                        || $appSettings->enable_reporting
                        || !empty($appSettings->content_responsible)
                    ) { ?>
                        <div class="content-box">
                            <div class="content-box-body">
                                <?php if (
                                    $showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
                                    && !empty($this->company->email) && $appSettings->enable_request_quote
                                ) { ?>
                                    <div class="info-detail">
                                        <a href="javascript:jbdListings.showQuoteCompany('<?php echo $this->company->id ?>','<?php echo $showData ? "1" : "0" ?>')" class="">
                                            <i class="icon envelope"></i> <span class="ui-button-text"><?php echo JText::_("LNG_REQUEST_QUOTE") ?></span>
                                        </a>
                                    </div>
                                <?php } ?>
                                <?php if ($appSettings->enable_reporting) { ?>
                                    <div class="info-detail">
                                        <a href="javascript:jbdListings.showReportAbuse()" style="padding:0px;"><i class="la la-flag-o la-fw"></i> <?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($appSettings->content_responsible)) { ?>
                                    <div class="info-detail">
                                        <i class="icon info-circle  la-fw"></i> <a href="javascript:void(0)" id="content-responsible-link"><?php echo JText::_('LNG_CONTENT_RESPONSIBLE_PERSON') ?></a>
                                        <div id="content_responsible_text" style="display: none;">
                                            <?php echo JHTML::_("content.prepare", $appSettings->content_responsible); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    

                    <!-- Business Contact Persons Informations -->
                    <?php if ($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
                        <?php if (!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone))) { ?>
                            <div class="content-box">
                                <div class="content-box-title">
                                    <h3><i class="la la-user"></i> <?php echo JText::_("LNG_CONTACT_PERSONS"); ?></h3>
                                </div>
                                <div class="content-box-body">
                                    <?php require_once 'contact_details.php'; ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php  } ?>


                    <!-- Business Articles -->
                    <?php if (!empty($this->companyArticles) && file_exists(JPATH_SITE . '/plugins/content/business/business.php')) { ?>
                        <div class="content-box">
                            <div class="content-box-title">
                                <h3><i class="la la-newspaper"></i> <?php echo JText::_("LNG_LISTING_ARTICLES"); ?></h3>
                            </div>
                            <div class="content-box-body">
                                <?php require_once 'listing_articles.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ((isset($this->package->features) && in_array(MEMBERSHIPS, $this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) {  ?>
                        <div class="content-box">
                            <div class="content-box-body">
                                <?php require_once 'listing_memberships.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ((isset($this->package->features) && in_array(CUSTOM_TAB, $this->package->features) || !$appSettings->enable_packages)
                                    && !empty($this->company->custom_tab_name)
                                ) { ?>

                                    <div class="content-box">
                                        <div class="content-box-title">
                                            <h3><?php echo $this->company->custom_tab_name ?></h3>
                                        </div>
                                        <div class="content-box-body">
                                            <?php echo JHTML::_("content.prepare", $this->company->custom_tab_content); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                    <!-- Business Hours -->
                    <?php if ((isset($this->package->features) && in_array(OPENING_HOURS, $this->package->features) || !$appSettings->enable_packages)
                        && (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE
                    ) { ?>
                        <div class="content-box">
                            <div class="content-box-body">
                                <?php require_once 'listing_hours.php'; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
                        <div class="content-box">
                            <div class="content-box-body">
                                <?php require_once 'listing_contact.php'; ?>
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

            <?php
                jimport('joomla.application.module.helper');
                // this is where you want to load your module position
                $modules = JModuleHelper::getModules('related-business');
            ?>

            <?php if (!empty($modules) && empty((float)$this->package->price)) { ?>
                <div class="related-businesses">
                    <!--h2 class="professionals-details__related-business-title title">
                        Related Business
                    </h2-->
                    <?php
                    $fullWidth = false;
                    foreach ($modules as $module) {
                        echo JModuleHelper::renderModule($module);
                    } ?>
                </div>
            <?php } ?>

            <?php
                jimport('joomla.application.module.helper');
                // this is where you want to load your module position
                $modules = JModuleHelper::getModules('business-videos');
            ?>
            <?php if (!empty($modules) && empty((float)$this->package->price)) { ?>
                <div class="business-related-vidoes">
                    <h2 class="professionals-details__videos-title title">
                        Videos
                    </h2>
                    <?php
                    $fullWidth = false;
                    foreach ($modules as $module) {
                        echo JModuleHelper::renderModule($module);
                    } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', function() {
        <?php if (!empty($this->company->business_cover_image)) { ?>
            jQuery(".company-style-9-header-image").css({
                "background-image": "url('<?php echo BD_PICTURES_PATH . $this->company->business_cover_image ?>')",
                "background-repeat": "no-repeat",
                "background-size": "cover",
                "background-position": "center",
                "height": "450px"
            });
        <?php }else{ ?>
            jQuery(".company-style-9-header-image").css({
                "background-image": "url('<?php echo !empty($appSettings->default_bg_listing) ? BD_PICTURES_PATH. $appSettings->default_bg_listing : BD_PICTURES_PATH. '/app/default_bg.jpg'?>')", 
                "background-repeat": "no-repeat",
                "background-size": "cover",
                "background-position": "center",
                "height": "450px"
            });
        <?php } ?>

        <?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
            && !empty($this->company->latitude) && !empty($this->company->longitude)
        ) { ?>
            loadDetailsViewMap();
        <?php } ?>
        jbdUtils.hideMap();
    });
</script>

<?php require_once 'listing_util.php'; ?>