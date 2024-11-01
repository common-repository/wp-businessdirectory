<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

// This view is for printing purposes

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->setMetaData('ROBOTS', 'NOINDEX, FOLLOW');

require_once 'header.php';
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
$address = JBusinessUtil::getAddressText($this->company); 
?>

    <div id="company-style-5-container" itemscope itemtype="http://schema.org/LocalBusiness">
        <div id="company-style-5-header">
            <div class="row-fluid">
                <!-- Business Categories -->
                <div class="company-style-5-header-image col-md-12">
                    <div class="company-style-5-header-info row-fluid">
                        <div class="col-md-9 first-column">
                            <?php if(isset($this->package->features) && in_array(SHOW_COMPANY_LOGO,$this->package->features) || !$appSettings->enable_packages){ ?>
                                <div class="col-md-3" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
                                    <!-- Business Logo -->
                                    <?php if(!empty($this->company->logoLocation)){?>
                                        <img class="business-logo" title="<?php echo $this->company->name?>" alt="<?php echo $this->company->name?>" src="<?php echo BD_PICTURES_PATH.$this->company->logoLocation ?>" itemprop="contentUrl">
                                    <?php }else{ ?>
                                        <img class="business-logo" title="<?php echo $this->company->name?>" alt="<?php echo $this->company->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" itemprop="contentUrl">
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div class="col-md-9">
                                <!-- Business Name -->
                                <h1 itemprop="name"><?php echo isset($this->company->name)?$this->company->name:""; ?></h1>
                                <div class="dir-address">
								<span itemprop="address">
									<!-- Business Address -->
                                    <?php echo JBusinessUtil::getAddressText($this->company); ?>
								</span>
                                </div>
                                <div class="dir-categories">
                                    <!-- Business Categories -->
                                    <?php if(!empty($this->company->categories)){?>
                                        <?php
                                        foreach($this->company->categories as $i=>$category){
                                            ?>
                                            <a href="javascript:void(0)"><?php echo $category[1]?></a><?php echo $i<(count($this->company->categories)-1)? ',&nbsp;':'' ?>
                                            <?php
                                        }
                                        ?>
                                    <?php } ?>
                                </div>
                                <?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                    <?php if(!empty($this->company->phone)) { ?>
                                        <span class="phone" itemprop="telephone">
										<i class="la la-phone"></i> <a href="javascript:void(0)"><?php  echo $this->company->phone; ?></a>
									</span><br/>
                                    <?php } ?>
                                    <?php if(!empty($this->company->mobile)) { ?>
                                        <span class="phone" itemprop="telephone">
										<i class="la la-mobile"></i> <a href="javascript:void(0)"><?php  echo $this->company->mobile; ?></a>
									</span><br/>
                                    <?php } ?>
                                <?php }?>
                                <div>
                                    <!-- Business Ratings -->
                                    <?php if($appSettings->enable_ratings) { ?>
                                        <div class="company-info-rating" <?php echo !$appSettings->enable_ratings? 'style="display:none"':'' ?>>
                                            <?php if(count($this->totalReviews) > 0) { ?>
                                                <span style="display:none" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                                <span>
                                                     <span itemprop="itemReviewed" itemscope itemtype="http://schema.org/LocalBusiness">
                                                         <span itemprop="name"><?php echo $this->company->name?></span>
                                                         <span itemprop="image"><?php echo BD_PICTURES_PATH.$this->company->logoLocation ?></span>
                                                         <span itemprop="address"><?php echo $address ?></span>
                                                         <span itemprop="telephone"><?php echo $this->company->phone ?></span>
                                                     </span>
                                                    <span itemprop="ratingValue"><?php echo $this->company->review_score?></span>  <span itemprop="worstRating">0</span><span itemprop="bestRating">5</span>
                                                </span>
                                                <span itemprop="ratingCount"><?php echo count($this->totalReviews)?></span>
                                            </span>
                                            <?php } ?>

                                            <div class="rating">
                                                <span class="user-rating-avg" id="rating-average" title="<?php echo $company->review_score?>" alt="<?php echo $company->id?>" style="display: block;"></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="review-info">
                                        <?php if($appSettings->enable_reviews) { ?>
                                            <a href="#go-company-reviews"><span><?php echo count($this->totalReviews); ?> <?php echo JText::_('LNG_REVIEWS'); ?></span></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div class="attribute-icon-container">
                                    <?php foreach($this->companyAttributes as $attribute) {
                                        $packageFeatured = isset($this->package->features)?$this->package->features:null;
                                        $icons = AttributeService::getAttributeIcons($attribute, $appSettings->enable_packages, $packageFeatured);
                                        $color = !empty($attribute->color)?$attribute->color:'';
                                        if(!empty($icons)) {
                                            foreach($icons as $icon)
                                                echo '<i class="'.$icon.' attribute-icon" style="color:'.$color.';"></i>';
                                        }
                                    }?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
            if(false && $this->company->enableWorkingStatus && (!$appSettings->enable_packages || isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features))){
                if ($this->company->workingStatus){?>
                    <div class="ribbon-open"><span><?php echo JText::_("LNG_OPEN")?></span></div>
                <?php } else{ ?>
                    <div class="ribbon-close"><span><?php echo JText::_("LNG_CLOSED")?></span></div>
                <?php } ?>
            <?php } ?>
        </div>

        <div id="company-style-5-body">
            <div class="row-fluid">

                <!-- BODY -->
                <div class="col-md-8">
                    <!-- Business Gallery -->
                    <?php if((isset($this->package->features) && in_array(IMAGE_UPLOAD,$this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->pictures)){?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3><i class="la la-camera-retro"></i> <?php echo JText::_("LNG_GALLERY"); ?></h3>
                                    <?php require_once JPATH_COMPONENT_SITE."/include/image_gallery.php";  ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Business Details -->
                    <div class="company-style-box">
                        <div class="row-fluid">
                            <div class="col-md-12">
                                <h3><i class="la la-newspaper"></i> <?php echo JText::_("LNG_COMPANY_DETAILS"); ?></h3>
                                <!-- Business Slogan -->
                                <?php if(isset($this->company->slogan) && strlen($this->company->slogan)>2) { ?>
                                    <p class="business-slogan"><?php echo  $this->company->slogan; ?> </p>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Business Description -->
                        <div class="row-fluid">
                            <div class="col-md-12">
                                <div id="dir-listing-description" class="dir-listing-description" itemprop="description">
                                    <?php if(!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION,$this->package->features) || !$appSettings->enable_packages)) { ?>
                                        <?php echo JHTML::_("content.prepare", $this->company->description); ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="col-md-12">
                                <dl>
                                    <!-- Business Type -->
                                    <?php if(!empty($this->company->typeName)) { ?>
                                        <dt><?php echo JText::_('LNG_TYPE'); ?>:</dt>
                                        <dd><?php echo $this->company->typeName; ?></dd>
                                    <?php } ?>

                                    <?php if(!empty($this->company->establishment_year)) { ?>
                                        <dt><?php echo JText::_('LNG_ESTABLISHMENT_YEAR'); ?>:</dt>
                                        <dd><?php echo $this->company->establishment_year; ?></dd>
                                    <?php } ?>

                                    <?php if(!empty($this->company->employees)) { ?>
                                        <dt><?php echo JText::_('LNG_EMPLOYEES'); ?>:</dt>
                                        <dd><?php echo $this->company->employees; ?></dd>
                                    <?php } ?>

                                    <!-- Business Keywords -->
                                    <?php if(!empty($this->company->keywords)) { ?>
                                        <dt><?php echo JText::_('LNG_KEYWORDS'); ?>:</dt>
                                        <dd>
                                            <ul class="dir-keywords">
                                                <?php
                                                $keywords =  explode(',', $this->company->keywords);
                                                for($i=0; $i<count($keywords); $i++) { ?>
                                                    <li>
                                                        <a  href="javascript:void(0)"><?php echo $keywords[$i]?><?php echo $i<(count($keywords)-1)? ',&nbsp;':'' ?></a>
                                                    </li>
                                                    <?php
                                                } ?>
                                            </ul>
                                        </dd>
                                    <?php } ?>

                                    <!-- Business Locations -->
                                    <?php if(!empty($this->company->locations) && $appSettings->show_secondary_locations == 1 &&
                                            (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)){ ?>
                                        <dt><?php echo JText::_("LNG_COMPANY_LOCATIONS"); ?>:</dt>
                                        <dd><?php require_once 'locations.php'; ?></dd>
                                    <?php } ?>

                                </dl>
                            </div>
                        </div>
                    </div>

                    <?php if((isset($this->package->features) && in_array(CUSTOM_TAB,$this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->company->custom_tab_name)){ ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3> <?php echo $this->company->custom_tab_name; ?></h3>
                                    <div>
                                        <?php echo JHTML::_("content.prepare", $this->company->custom_tab_content);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Offers -->
                    <?php if((isset($this->package->features) && in_array(COMPANY_OFFERS,$this->package->features) || !$appSettings->enable_packages)
                            && isset($this->offers) && count($this->offers) && $appSettings->enable_offers) { ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                     <h3><i class="la la-tag"></i> <?php echo JText::_("LNG_COMPANY_OFFERS"); ?></h3>
                                    <div id="company-offers" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
                                        <?php require_once 'listing_offers.php';?>
                                        <div class="clear"></div>
                                    </div>
                            	</div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Related Companies -->
                    <?php if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
                            && isset($this->realtedCompanies) && count($this->realtedCompanies)){?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                     <h3><i class="la la-tag"></i> <?php echo JText::_("LNG_RELATED_COMPANIES"); ?></h3>
                                    <div class="company-cell" >
                                        <?php require_once 'related_business.php';?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Company Services -->
                    <?php if((isset($this->package->features) && in_array(COMPANY_SERVICES,$this->package->features) || !$appSettings->enable_packages)
                            && isset($this->services) && count($this->services) && $appSettings->enable_services){
                        ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3><i class="la la-wrench"></i> <?php echo JText::_("LNG_SERVICES"); ?></h3>
                                    <div id="company-services">
                                        <?php require_once 'listing_services.php';?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Events -->
                    <?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
                            && isset($this->events) && count($this->events) && $appSettings->enable_events) { ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3><i class="la la-calendar"></i> <?php echo JText::_("LNG_COMPANY_EVENTS"); ?></h3>
                                    <div id="company-events">
                                        <div class='events-container full' style="">
                                            <ul class="event-list">
                                                <?php
                                                if(!empty($this->events)){
                                                    foreach ($this->events as $event){ ?>
                                                        <li>
                                                            <div class="event-box row">
                                                                <div class="event-img-container col-md-3">
                                                                    <a class="event-image"	href="<?php echo JBusinessUtil::getEventLink($event->id, $event->alias) ?>">
                                                                        <?php if(!empty($event->picture_path)){?>
                                                                            <img src="<?php echo BD_PICTURES_PATH.$event->picture_path?>">
                                                                        <?php }else{ ?>
                                                                            <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>">
                                                                        <?php } ?>
                                                                    </a>
                                                                </div>
                                                                <div class="event-content col-md-7">
                                                                    <div class="event-subject">
                                                                        <a href="javascript:void(0)"><?php echo $event->name?></a>
                                                                    </div>
                                                                    <?php if(!empty($address)) { ?>
                                                                        <div class="event-location">
                                                                            <i class="icon map-marker"></i>&nbsp;<span itemprop="name"><?php echo $address ?></span>
                                                                        </div>
                                                                    <?php } ?>

                                                                    <div class="event-location">
                                                                        <i class="la la-calendar"></i>  <?php echo JBusinessUtil::getDateGeneralFormat($event->start_date)." ".JText::_("LNG_UNTIL")." ".JBusinessUtil::getDateGeneralFormat($event->end_date) ?>
                                                                    </div>
                                                                    <div class="event-type">
                                                                        <?php echo JText::_("LNG_TYPE")?>:  <?php echo $event->eventType?>
                                                                    </div>
                                                                    <div class="event-desciption">
                                                                        <?php echo $event->short_description ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php }
                                                }else{
                                                    echo JText::_("LNG_NO_EVENT_FOUND");
                                                } ?>
                                            </ul>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Associated Events -->
                    <?php if((isset($this->package->features) && in_array(COMPANY_EVENTS,$this->package->features) || !$appSettings->enable_packages)
                            && isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events) { ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                     <h3><i class="la la-calendar"></i> <?php echo JText::_("LNG_ASSOCIATED_EVENTS"); ?></h3>
                                    <div>
                                        <?php require_once 'listing_associated_events.php';?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Price List -->
                    <?php if((isset($this->package->features) && in_array(SERVICES_LIST,$this->package->features) || !$appSettings->enable_packages)
                            && !empty($this->services_list) && count($this->services_list) && $appSettings->enable_price_list) { ?>
                        <div id="company-price-list" class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3><i class="la la-list-alt"></i> <?php echo JText::_("LNG_PRICE_LIST"); ?></h3>
                                    <div id="company-reviews">
                                        <?php require_once 'listing_price_list.php'; ?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <!-- SIDEBAR -->
                <div class="col-md-4">
                    <?php if($appSettings->social_profile && $this->company->userId!=0 && (empty($this->claimDetails) || isset($this->claimDetails) && ($this->claimDetails->status == 1))){?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3><i class="la la-user"></i> <?php echo JText::_("LNG_COMMUNITY_OWNER_PROFILE"); ?></h3>
                                    <?php
                                    require JPATH_COMPONENT_SITE.'/include/profile/listing_profile.php';
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="company-style-box">
                        <!-- Business Map -->
                        <div class="row-fluid hidden-xs hidden-phone">
                            <div class="col-md-12">
                                <div class="dir-map-image">
                                    <?php
                                    $key="";
                                    if(!empty($appSettings->google_map_key))
                                        $key="&key=".$appSettings->google_map_key;
                                    ?>

                                    <?php if((isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages) && !empty($this->company->latitude) && !empty($this->company->longitude)) {
                                        echo '<img alt="company map" src="https://maps.googleapis.com/maps/api/staticmap?center='.$this->company->latitude.','.$this->company->longitude.'&zoom=13&size=300x300&markers=color:blue|'.$this->company->latitude.','.$this->company->longitude.$key.'&sensor=false">';
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Business Address -->
                        <div class="row-fluid">
                            <div class="col-md-12 dir-address">
                                <?php echo JBusinessUtil::getAddressText($this->company); ?>
                            </div>
                        </div>

                        <!-- Business Contact Informations -->
                        <div class="row-fluid">
                            <div class="col-md-12">
                                <div class="company-info-details">
                                    <?php if(!empty($this->company->email) && $showData && $appSettings->show_email){?>
                                        <span itemprop="email">
										<i class="la la-envelope"></i> <?php echo $this->company->email?>
									</span>
                                        <br/>
                                    <?php } ?>

                                    <?php if($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
                                        <?php if(!empty($this->company->phone)) { ?>
                                            <span class="phone" itemprop="telephone">
											<i class="la la-phone"></i> <a href="javascript:void(0)"><?php  echo $this->company->phone; ?></a>
										</span><br/>
                                        <?php } ?>

                                        <?php if(!empty($this->company->mobile)) { ?>
                                            <span class="phone" itemprop="telephone">
											<i class="la la-mobile"></i> <a href="javascript:void(0)"><?php  echo $this->company->mobile; ?></a>
										</span><br/>
                                        <?php } ?>

                                        <?php if(!empty($this->company->fax)) {?>
                                            <span class="phone" itemprop="faxNumber">
											<i class="icon fax"></i> <?php echo $this->company->fax?>
										</span>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Business Website & Business Contact -->
                        <div class="row-fluid">
                            <div class="col-md-12">
                                <div class="company-links">
                                    <?php if($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
                                        if ($appSettings->enable_link_following){
                                            $followLink = (isset($this->package->features) && in_array(LINK_FOLLOW,$this->package->features) && $appSettings->enable_packages)?'rel="follow noopener"' : 'rel="nofollow noopener"';
                                        }else{
                                            $followLink ='rel="noopener"';
                                        }?>
                                        <i class="la la-globe"></i>
                                        <a itemprop="url" class="website" href="javascript:void(0)">
                                            <?php echo $company->website ?>
                                        </a>
                                    <?php } else { ?>
                                        <span style="display:none;" itemprop="url">
                                        <?php echo JBusinessUtil::getCompanyLink($this->company); ?>
                                    </span>
                                    <?php } ?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Contact Persons Informations -->
                    <?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
                        <?php if(!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone) )) { ?>
                            <div class="company-style-box">
                                <div class="row-fluid">
                                    <h3><i class="la la-user"></i> <?php echo JText::_("LNG_CONTACT_PERSONS"); ?></h3>
                                    <div class="col-md-12">
                                        <?php require_once 'contact_details.php'; ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php  } ?>

                    <!-- Business Social Networks -->
                    <?php if($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
                            && ((!empty($this->company->linkedin) || !empty($this->company->youtube) ||!empty($this->company->facebook) || !empty($this->company->twitter)
                                     || !empty($this->company->linkedin) || !empty($this->company->skype)|| !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))) { ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <h3><i class="la la-share-alt"></i> <?php echo JText::_("LNG_SOCIAL_NETWORK"); ?></h3>
                                    <div id="social-networks-container">
                                        <ul class="socials-network" style="display: grid;">
                                            <li>
                                                <span class="social-networks-follow"><?php echo JText::_("LNG_FOLLOW_US")?>: &nbsp;</span>
                                            </li>
                                            <?php if(!empty($this->company->facebook)){ ?>
                                                <li >
                                                    <a class="share-social la la-facebook-f" href="javascript:void(0)"><?php echo $this->company->facebook ?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->twitter)){ ?>
                                                <li >
                                                    <a class="share-social la la-twitter" href="javascript:void(0)"><?php echo $this->company->twitter ?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->linkedin)){ ?>
                                                <li >
                                                    <a class="share-social la la-linkedin" href="javascript:void(0)"><?php echo $this->company->linkedin?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->skype)){ ?>
                                                <li >
                                                    <a class="share-social la la-skype" href="javascript:void(0)"><?php echo $this->company->skype?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->youtube)){ ?>
                                                <li >
                                                    <a class="share-social la la-youtube" href="javascript:void(0)"><?php echo $this->company->youtube?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->instagram)){ ?>
                                                <li >
                                                    <a class="share-social la la-instagram" href="javascript:void(0)"><?php echo $this->company->instagram?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->tiktok)){ ?>
                                                <li >
                                                    <a class="share-social" href="javascript:void(0)"><?php echo $this->company->tiktok?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->pinterest)){ ?>
                                                <li >
                                                    <a class="share-social la la-pinterest" href="javascript:void(0)"><?php echo $this->company->pinterest?></a>
                                                </li>
                                            <?php } ?>
                                            <?php if(!empty($this->company->whatsapp)){ ?>
                                                <li >
                                                    <a class="share-social la la-whatsapp" href="javascript:void(0)"><?php echo $this->company->whatsapp?></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Business Hours -->
                    <?php if((isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features) || !$appSettings->enable_packages)
                            && (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE) { ?>
                        <div class="company-style-box">
                            <div class="row-fluid">
                                <div class="col-md-12">
                                    <?php require_once 'listing_hours.php'; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="listing-banners">
                        <?php
                        jimport('joomla.application.module.helper');
                        // this is where you want to load your module position
                        $modules = JModuleHelper::getModules('dir-listing');
                        $fullWidth = true;
                        ?>
                        <?php if(isset($modules) && count($modules)>0) { ?>
                            <div class="listing-banner">
                                <?php
                                $fullWidth = false;
                                foreach($modules as $module) {
                                    echo JModuleHelper::renderModule($module, array("style"=>"xhtml"));
                                } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form name="tabsForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" id="tabsForm" method="post">
        <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="companies.displayCompany" />
        <input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId?>" />
        <input type="hidden" name="view" value="companies" />
        <input type="hidden" name="layout2" id="layout2" value="" />
        <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
        <input type="hidden" name="controller"	value="<?php echo JFactory::getApplication()->input->get('controller', 'J-BusinessDirectory')?>" />
    </form>

    <script>
        window.addEventListener('load', function() {
            <?php if($showData && (isset($this->package->features) && in_array(GOOGLE_MAP,$this->package->features) || !$appSettings->enable_packages )
        && !empty($this->company->latitude) && !empty($this->company->longitude)){ ?>
            jbdMap.loadMapScript();
            <?php }	?>

            <?php if(!empty($this->company->business_cover_image)) { ?>
            jQuery(".company-style-5-header-image").css({
                "background-image": "url('<?php echo BD_PICTURES_PATH.$this->company->business_cover_image ?>')",
                "background-repeat": "no-repeat",
                "background-size": "cover",
                "background-position": "center"
            });
            <?php }else{ ?>
            jQuery(".company-style-5-header-image").css({
                "background-image": "url('<?php echo !empty($appSettings->default_bg_listing) ? BD_PICTURES_PATH. $appSettings->default_bg_listing : BD_PICTURES_PATH. '/app/default_bg.jpg'?>')", 
                "background-repeat": "no-repeat",
                "background-size": "cover",
                "background-position": "center"
            });
            <?php } ?>
        });
    </script>

<?php require_once 'listing_util.php'; ?>