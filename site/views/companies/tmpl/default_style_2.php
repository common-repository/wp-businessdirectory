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

<?php require_once 'breadcrumbs.php'; ?>

<div id="listing-style-2" class="listing-style-2" itemscope itemtype="http://schema.org/LocalBusiness">
	<div class="row">
		<div class="col-md-7">
			<h1 itemprop="name">
				<?php echo isset($this->company->name) ? $this->company->name : ""; ?>
				<?php if ($this->company->recommended && $appSettings->show_recommended) { ?>
					<span class="recommended"><i class="la la-star"></i> <?php echo JText::_("LNG_RECOMMENDED") ?></span>
				<?php } ?>
				<?php if ($this->company->userId != 0 && $appSettings->show_claimed) { ?>
					<span class="claimed"><i class="la la-check"></i> <?php echo JText::_("LNG_CLAIMED") ?></span>
				<?php } ?>
			</h1>
			<div class="company-info-review">
				<!-- Business Ratings -->
				<?php  if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					        && $appSettings->enable_reviews ) {  ?>
					<div class="company-info-rating" <?php echo !$appSettings->enable_ratings ? 'style="display:none"' : '' ?>>
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
							<span class="user-rating-avg" id="rating-average" title="<?php echo $company->review_score ?>" alt="<?php echo $company->id ?>" style="display: block;"></span>
						</div>
					</div>
				<?php } ?>
				<?php  if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					    && $appSettings->enable_reviews ) {  ?>
					<div class="review-count">
						<?php if (count($this->totalReviews)) { ?>
							<a href="javascript:void(0)" onclick="jQuery('#dir-tab-3').click()"><?php echo count($this->totalReviews) ?> <?php echo JText::_('LNG_REVIEWS') ?></a>
							&nbsp;|&nbsp;
							<a href="javascript:void(0)" onclick="jbdReviews.addNewReviewOnTabs(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0" ?>)"> <?php echo JText::_('LNG_WRITE_REVIEW') ?></a>
						<?php } else { ?>
							<a href="javascript:void(0)" onclick="jbdReviews.addNewReviewOnTabs(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0" ?>)"><?php echo JText::_('LNG_BE_THE_FIRST_TO_REVIEW') ?></a>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<?php if (isset($this->company->slogan) && strlen($this->company->slogan) > 2) { ?>
				<div class="business-slogan"><?php echo  $this->company->slogan ?></div>
			<?php } ?>
		</div>
		<div class="col-md-5 text-right header-icons">
			<?php require_once JPATH_COMPONENT_SITE . "/include/social_share.php" ?>
			<div class="dir-print">
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
		</div>

	</div>
	<div id="company-info" class="company-info row">
		<?php if (isset($this->package->features) && in_array(SHOW_COMPANY_LOGO, $this->package->features) || !$appSettings->enable_packages) { ?>
			<div class="col-md-3">
				<span itemprop="image" style="display:none;"><?php echo (!empty($this->company->logoLocation)) ? BD_PICTURES_PATH . $this->company->logoLocation : BD_ASSETS_FOLDER_PATH . "no_image.jpg" ?></span>
				<div class="company-image" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
					<?php if (!empty($this->company->logoLocation)) { ?>
						<img title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo BD_PICTURES_PATH . $this->company->logoLocation ?>" itemprop="contentUrl">
					<?php } else { ?>
						<img title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>" itemprop="contentUrl">
					<?php } ?>
					<?php if ($this->appSettings->enable_bookmarks) { ?>
						<div id="bookmark-container">
							<?php if (!empty($company->bookmark)) { ?>
								<a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID == 0 ? "1" : "0" ?>,<?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_UPDATE_BOOKMARK") ?>" class="bookmark"><i class="la la-heart"></i> </a>
							<?php } else { ?>
								<a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID == 0 ? "1" : "0" ?>,<?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK") ?>" class="bookmark"><i class="la la-heart-o"></i> </span></a>
							<?php } ?>
						</div>
						<?php } ?>
						<?php if($this->appSettings->show_open_status && (!$this->appSettings->enable_packages || isset($this->package->features) && in_array(OPENING_HOURS,$this->package->features))) { ?>
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
						</div>
			</div>
		<?php } ?>
		<div class="company-info-container col-md">
			<div class="row">
				<div class="col-md-7">
					<div class="listing-cotact-details">
						<?php if ($showData && !empty($address)) { ?>
							<strong><?php echo JText::_('LNG_ADDRESS') ?>:</strong>
							<span class="company-address" itemprop="address">
								<?php echo JBusinessUtil::getAddressText($this->company) ?>
							</span>
						<?php } ?>

						<?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
							<div class="comany-contact-details">
								<?php if (!empty($company->phone)) { ?>
									<span>
										<strong><?php echo JText::_('LNG_PHONE') ?>: </strong> <a href="tel:<?php echo $this->escape($company->phone); ?>"> <span itemprop="telephone"><?php echo $this->escape($company->phone); ?></span></a>
									</span><br />
								<?php } ?>
								<?php if (!empty($company->fax)) { ?>
									<span>
										<strong><?php echo JText::_('LNG_FAX') ?>: </strong><span itemprop="faxNumber"><?php echo $this->escape($company->fax); ?></span>
									</span><br />
								<?php } ?>
								<?php if (!empty($company->mobile)) { ?>
									<span>
										<strong><?php echo JText::_('LNG_MOBILE') ?>: </strong><a href="tel:<?php echo $this->escape($company->mobile); ?>"> <?php echo $this->escape($company->mobile); ?></a>
									</span><br />
								<?php } ?>
								<?php if (!empty($this->company->email) && $showData && $appSettings->show_email) { ?>
									<span itemprop="email">
										<strong><?php echo JText::_('LNG_EMAIL') ?>: </strong> <a href="mailto:<?php echo $this->escape($this->company->email) ?>"><?php echo $this->escape($this->company->email) ?></a>
									</span><br />
								<?php } ?>
							</div>
						<?php } ?>

						<ul class="features-links">
							<li>
								<?php if ($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
									if ($appSettings->enable_link_following) {
										$followLink = (isset($this->package->features) && in_array(LINK_FOLLOW, $this->package->features) && $appSettings->enable_packages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
									} else {
										$followLink = 'rel="noopener"';
									} ?>
									<strong><?php echo JText::_('LNG_WEBSITE') ?>: </strong><a target="_blank" <?php echo $followLink ?> itemprop="url" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $this->escape($company->website) ?>"> <?php echo $this->escape($this->company->website) ?></a>
								<?php } else { ?>
									<span style="display:none;" itemprop="url">
										<?php echo JBusinessUtil::getCompanyLink($this->company); ?>
									</span>
								<?php } ?>
							</li>
						</ul>
					</div>

					<div class="align-items-end pt-4">
						<?php if ((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
							<button type="button" class="btn btn-primary" onclick="jbdListings.contactCompany(<?php echo $showData ? "1" : "0" ?>)">
								<i class="la la-envelope"></i> <?php echo !empty($this->company->userId)?JText::_("LNG_CONTACT_COMPANY"):JText::_("LNG_REQUEST_MORE_INFO") ?>
							</button>
						<?php } ?>
						<?php if (
							$showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
							&& !empty($this->company->email) && $appSettings->enable_request_quote
						) { ?>
							<button type="button" class="btn btn-primary" onclick="jbdListings.showQuoteCompany('<?php echo $this->company->id ?>','<?php echo $showData ? "1" : "0" ?>')">
								<i class="la la-edit"></i><?php echo JText::_("LNG_QUOTE") ?>
							</button>
						<?php } ?>
						<?php if ($this->showListLinkButton && $appSettings->enable_linked_listings) { ?>
							<button type="button" class="btn btn-primary" onclick="jbdListings.joinListing(<?php echo count($this->joinedCompanies); ?>,<?php echo $this->company->id ?>,<?php echo ($user->ID == 0) ? 0 : 1 ?>,<?php echo count($this->userCompanies) < 2 ? false : true; ?>)">
								<i class="la la-user"></i><?php echo JText::_("LNG_JOIN_LEAVE") ?>
							</button>
						<?php } ?>
					</div>
				</div>
				<div class="col-md-5">
					<?php if ($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
						<?php if (!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone))) { ?>
							<strong><?php echo count($this->companyContacts) > 1 ? JText::_('LNG_CONTACT_PERSONS') : JText::_('LNG_CONTACT_PERSON'); ?></strong>
							<?php require_once 'contact_details.php'; ?>
						<?php } ?>
					<?php } ?>

					<!-- Listing Categories -->
					<?php if (!empty($this->company->categoriesDetails)) { ?>
						<dt class="listing-category-title"><?php echo JText::_('LNG_CATEGORIES') ?>:</dt>
						<dd class="listing-category-content">
							<?php require_once 'listing_categories.php'; ?>
						</dd>
					<?php } ?>

					<?php if ($appSettings->enable_reporting) { ?>
						<div>
							<a href="javascript:jbdListings.showReportAbuse()"><i class="la la-flag"></i> <?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
						</div>
					<?php } ?>

					<?php if ((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business) { ?>
						<div class="claim-container" id="claim-container">
							<i class="la la-check-square"></i> <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkUser&claim_listing_id=' . $this->company->id.$menuItemId) ?>"><?php echo JText::_('LNG_CLAIM_COMPANY') ?></a>
						</div>
					<?php  } ?>
					<?php if (!empty($appSettings->content_responsible)) { ?>
						<span>
							<i class="icon info-circle"></i> <a href="javascript:void(0)" id="content-responsible-link"><?php echo JText::_('LNG_CONTENT_RESPONSIBLE_PERSON') ?></a>
						</span>
						<div id="content_responsible_text" style="display: none;">
							<?php echo JHTML::_("content.prepare", $appSettings->content_responsible); ?>
						</div>
					<?php } ?>
					<?php if (
						$showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
						&& ((!empty($this->company->linkedin) || !empty($this->company->youtube) || !empty($this->company->facebook) || !empty($this->company->twitter)
							|| !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))
					) {
					?>
						<div class="pt-4">
							<?php require_once 'listing_social_networks.php'; ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="clear"></div>
	</div>



	<form name="tabsForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory' . $menuItemId) ?>" id="tabsForm" method="post">
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>" />
		<input type="hidden" name="task" value="companies.displayCompany" />
		<input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId ?>" />
		<input type="hidden" name="view" value="companies" />
		<input type="hidden" name="layout2" id="layout2" value="" />
		<input type="hidden" name="companyId" value="<?php echo $this->company->id ?>" />
		<input type="hidden" name="controller" value="companies" />
	</form>

	<div id="tab-panel">

		<div id="tabs" class="clearfix">
			<ul class="tab-list">
				<?php
				$tabs = array();
				if (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages) {
					$tabs[1] = JText::_('LNG_BUSINESS_DETAILS');
				}
				if (
					$showData &&  (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->company->latitude) && !empty($this->company->longitude)
				) {
					$tabs[2] = JText::_('LNG_MAP');
				}
				if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					&& $appSettings->enable_reviews ) { 
					$tabs[3] = JText::_('LNG_REVIEWS');
				}
				if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->pictures)
				) {
					$tabs[4] = JText::_('LNG_GALLERY');
				}
				if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->videos) && count($this->videos) > 0
				) {
					$tabs[5] = JText::_('LNG_VIDEOS');
				}
				if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers
				) {
					$tabs[6] = JText::_('LNG_OFFERS');
				}

				if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->events) && count($this->events) && $appSettings->enable_events
				) {
					$tabs[7] = JText::_('LNG_EVENTS');
				}

				if (!empty($this->company->locations) && $appSettings->show_secondary_locations == 1 && (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)) {
					$tabs[8] = JText::_('LNG_COMPANY_LOCATIONS');
				}

				if ((isset($this->package->features) && in_array(OPENING_HOURS, $this->package->features) || !$appSettings->enable_packages)
				    && (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE
				) {
					$tabs[9] = JText::_('LNG_OPENING_HOURS');
				}

				if ((isset($this->package->features) && in_array(CUSTOM_TAB, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->company->custom_tab_name)
				) {
					$tabs[10] = $this->company->custom_tab_name;
				}

				if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->services) && count($this->services) && $appSettings->enable_services
				) {
					$tabs[13] = JText::_('LNG_COMPANY_SERVICES');
				}

				if ((isset($this->package->features) && in_array(SOUNDS_FEATURE, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->sounds)
				) {
					$tabs[14] = JText::_('LNG_SOUNDS');
				}

				if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->services_list) && count($this->services_list)
				) {
					$tabs[15] = JText::_('LNG_PRICE_LIST');
				}

				if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->companyTestimonials)
				) {
					$tabs[16] = JText::_('LNG_TESTIMONIALS');
				}

				if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
					&& !empty($this->companyProjects)){
					$tabs[17] = JText::_('LNG_PROJECTS');
				}

				if((isset($this->package->features) && in_array(MEMBERSHIPS,$this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) {
					$tabs[18] = JText::_('LNG_MEMBERSHIPS');
				}

				if ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->productCategories) && count($this->productCategories)
				) {
					$tabs[19] = JText::_('LNG_PRODUCTS');
				}

				if ((isset($this->package->features) && in_array(ANNOUNCEMENTS, $this->package->features)
					|| !$appSettings->enable_packages) && !empty($this->companyAnnouncements) && count($this->companyAnnouncements) && $appSettings->enable_announcements) {
					$tabs[20] = JText::_('LNG_ANNOUNCEMENTS');
				}

				if (!empty($this->associatedCompanies) && $appSettings->enable_linked_listings) {
					$tabs[21] = JText::_('LNG_LINKED_COMPANIES');
				}

				if (!empty($this->companyArticles) && file_exists(JPATH_SITE . '/plugins/content/business/business.php')) {
					$tabs[22] = JText::_('LNG_LISTING_ARTICLES');
				}

				if((!empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) && (isset($this->package->features)
						&& in_array(TEAM_FEATURE, $this->package->features) || !$appSettings->enable_packages) ){
					$tabs [23] = JText::_ ( 'LNG_TEAM' );
				}

				if (isset($this->offers) && count($this->offers) && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) {
					$tabs[24] = JText::_('LNG_OFFERS_PRICE_LIST');
				}

				foreach ($tabs as $key => $tab) {
				?>
					<li class="dir-dir-tabs-options"><span id="dir-tab-<?php echo $key ?>" onclick="jbdListings.showDirTab('#tabs-<?php echo $key ?>')" class="track-business-details"><?php echo $tab ?></span></li>
				<?php } ?>
			</ul>
			<div class="clear"> </div>

			<?php if (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages) { ?>
				<div id="tabs-1" class="dir-tab ui-tabs-panel">

					<?php require_once 'details.php'; ?>
				</div>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->company->latitude) && isset($this->company->longitude)
			) {
			?>

				<div id="tabs-2" class="dir-tab ui-tabs-panel">
					<?php
					if (!empty($this->company->latitude) && !empty($this->company->longitude))
						require_once 'map.php';
					else
						echo JText::_("LNG_NO_MAP_COORDINATES_DEFINED");
					?>
				</div>
			<?php } ?>

			<?php  if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					    && $appSettings->enable_reviews ) {  ?>
				<div id="tabs-3" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_reviews.php'; ?>
					<?php if(!$appSettings->enable_reviews_users || !$user->ID ==0) { ?>
							<?php require_once 'default_addreview.php'; ?>
					<?php } ?>
				</div>
			<?php } ?>
			<?php
			if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->pictures)
			) {
			?>
				<div id="tabs-4" class="dir-tab ui-tabs-panel">
					<?php require_once JPATH_COMPONENT_SITE . '/include/image_gallery.php'; ?>
				</div>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->videos) && count($this->videos) > 0
			) {
			?>
				<div id="tabs-5" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_videos.php'; ?>
				</div>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers
			) {
			?>
				<div id="tabs-6" class="dir-tab ui-tabs-panel" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
					<?php require_once 'listing_offers.php'; ?>
				</div>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->events) && count($this->events) && $appSettings->enable_events
			) {
			?>
				<div id="tabs-7" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_events.php'; ?>

					<?php if(!empty($this->associatedEvents) && $appSettings->enable_events){ ?>
						<div class="associated-events pt-3">
							<?php echo JText::_('LNG_ASSOCIATED_EVENTS') ?>
							<?php require_once 'listing_associated_events.php'; ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<?php if (
				!empty($this->company->locations) && $appSettings->show_secondary_locations == 1
				&& (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)
			) { ?>
				<div id="tabs-8" class="dir-tab ui-tabs-panel">
					<?php require_once 'locations.php'; ?>
				</div>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(OPENING_HOURS, $this->package->features) || !$appSettings->enable_packages)
				&& (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE
			) { ?>
				<div id="tabs-9" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_hours.php'; ?>
				</div>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(CUSTOM_TAB, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->company->custom_tab_name)
			) { ?>
				<div id="tabs-10" class="dir-tab ui-tabs-panel">
					<?php echo JHTML::_("content.prepare", $this->company->custom_tab_content); ?>
				</div>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->services) && count($this->services) && $appSettings->enable_services
			) {
			?>
				<div id="tabs-13" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_services.php'; ?>
				</div>
				<div class="clear"></div>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(SOUNDS_FEATURE, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->sounds)
			) { ?>
				<div id="tabs-14" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_sounds.php'; ?>
				</div>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->services_list) && $appSettings->enable_price_list
			) {
			?>
				<div id="tabs-15" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_price_list.php'; ?>
				</div>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->companyTestimonials)
			) {
			?>
				<div id="tabs-16" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_testimonials.php'; ?>
				</div>
			<?php } ?>

			<?php 
			if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
				&& !empty($this->companyProjects)){ ?>
				<div id="tabs-17" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_projects.php'; ?>
				</div>
			<?php } ?>

			<?php if((isset($this->package->features) && in_array(MEMBERSHIPS,$this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) { ?>
				<div id="tabs-18" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_memberships.php'; ?>
				</div>
			<?php } ?>

			<!-- Listing Products -->
			<?php if ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->productCategories) && count($this->productCategories)
			) { ?>
				<div id="tabs-19" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_product_categories.php'; ?>
				</div>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(ANNOUNCEMENTS, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->companyAnnouncements) && count($this->companyAnnouncements) && $appSettings->enable_announcements
			) {
			?>
				<div id="tabs-20" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_announcements.php'; ?>
				</div>
			<?php } ?>

			<?php if (!empty($this->associatedCompanies) && $appSettings->enable_linked_listings) {
			?>
				<div id="tabs-21" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_associated_companies.php'; ?>
				</div>
			<?php } ?>

			<?php if (!empty($this->companyArticles) && file_exists(JPATH_SITE . '/plugins/content/business/business.php')) { ?>
				<div id="tabs-22" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_articles.php'; ?>
				</div>
			<?php } ?>

			<?php if((!empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) && (isset($this->package->features)
                	    && in_array(TEAM_FEATURE, $this->package->features) || !$appSettings->enable_packages) ){
			?>
				<div id="tabs-23" class="dir-tab ui-tabs-panel">
					<?php require_once 'listing_team.php'; ?>
				</div>
			<?php } ?>

			<?php if (isset($this->offers) && count($this->offers)  && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
				<div id="tabs-24" class="dir-tab ui-tabs-panel">
					<?php if($appSettings->offer_price_list_view_style == 1) {
								require_once 'listing_offers_price_list_style_2.php';
							} else {
								require_once 'listing_offers_price_list.php';
							}
					?>    				
				</div>
			<?php } ?>

		</div>
	</div>
	<div class="clear"></div>
</div>

<?php if ( is_active_sidebar( 'wpbd-listing-details' ) ) { ?>
	<div class="dir-company-module">
		<?php dynamic_sidebar( 'wpbd-listing-details' ); ?>
	</div>
<?php } ?>

<script>
	window.addEventListener('load', function() {
		jbdListings.initTabs(<?php echo $this->tabId ?>);
		jQuery("#dir-tab-7").click(function() {
			// jQuery('#events-calendar').fullCalendar(calendarOptions);
		});
	});
</script>

<?php require_once 'listing_util.php'; ?>