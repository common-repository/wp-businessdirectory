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
$db = JFactory::getDBO();
$address = JBusinessUtil::getAddressText($this->company); 
?>

<div id="one-page-container" class="one-page-container listing-style-3" itemscope itemtype="http://schema.org/LocalBusiness">
	<?php
	require_once 'breadcrumbs.php';
	?>
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
	<div id="company-info" class="company-info">
		<div class="row">
			<?php if (isset($this->package->features) && in_array(SHOW_COMPANY_LOGO, $this->package->features) || !$appSettings->enable_packages) { ?>
				<div class="col-md-4">
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
					</div>
				</div>
			<?php } ?>
			<div class="col-md">
				<div class="company-info-container">
					<div class="company-info-review">
						<!-- Business Ratings -->
						<?php  if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
									&& $appSettings->enable_reviews ) { ?>
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
									&& $appSettings->enable_reviews ) { ?>
							<div class="review-count">
								<?php if (count($this->totalReviews)) { ?>
									<a href="javascript:void(0)" onclick="jQuery('#dir-tab-3').click()"><?php echo count($this->totalReviews) ?> <?php echo JText::_('LNG_REVIEWS') ?></a>
									&nbsp;&nbsp;&nbsp;
									<a href="javascript:void(0)" onclick="jbdReviews.addNewReview(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0" ?>)"> <?php echo JText::_('LNG_WRITE_REVIEW') ?></a>
								<?php } else { ?>
									<a href="javascript:void(0)" onclick="jbdReviews.addNewReview(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0" ?>)"><?php echo JText::_('LNG_BE_THE_FIRST_TO_REVIEW') ?></a>
								<?php } ?>
							</div>
						<?php } ?>
					</div>

					<div>
						<div class="company-info-details">
							<?php if ($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)) { ?>
								<?php if (!empty($this->companyContacts) && (!empty($this->companyContacts[0]->contact_name) || !empty($this->companyContacts[0]->contact_phone))) { ?>
									<strong><?php echo count($this->companyContacts) > 1 ? JText::_('LNG_CONTACT_PERSONS') : JText::_('LNG_CONTACT_PERSON'); ?></strong>
									<?php require_once 'contact_details.php'; ?>
								<?php } ?>
							<?php } ?>
							<p>
								<?php if ($showData && !empty($address)) { ?>
									<div class="item-element">
										<span itemprop="address">
											<i class="icon map-marker"></i> <?php echo $this->escape($address) ?>
										</span>
									</div>
								<?php } ?>
							</p>

							<?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
								<?php if ($showData && !empty($this->company->phone)) { ?>
									<div class="item-element">
										<span class="phone" itemprop="telephone">
											<i class="icon phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
										</span>
									</div>
								<?php } ?>

								<?php if (!empty($this->company->mobile)) { ?>
									<div class="item-element">
										<span class="phone" itemprop="telephone">
											<i class="icon mobile"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
										</span>
									</div>
								<?php } ?>


								<?php if (!empty($this->company->fax)) { ?>
									<div class="item-element">
										<span class="faxNumber">
											<i class="icon fax"></i> <?php echo $this->escape($this->company->fax) ?>
										</span>
									</div>
								<?php } ?>

							<?php } ?>

							<?php if (!empty($this->company->email) && $showData && $appSettings->show_email) { ?>
								<div class="item-element">
									<span itemprop="email">
										<i class="icon envelope"></i> <a href="mailto:<?php echo $this->escape($this->company->email) ?>"><?php echo $this->escape($this->company->email) ?></a>
									</span>
								</div>
							<?php } ?>
					

							<div class="item-element">
								<ul class="features-links">
									<?php if ($showData && (isset($this->package->features) && in_array(WEBSITE_ADDRESS, $this->package->features) || !$appSettings->enable_packages) && !empty($company->website)) {
										if ($appSettings->enable_link_following) {
											$followLink = (isset($this->package->features) && in_array(LINK_FOLLOW, $this->package->features) && $appSettings->enable_packages) ? 'rel="follow noopener"' : 'rel="nofollow noopener"';
										} else {
											$followLink = 'rel="noopener"';
										} ?>
										<li>
											<a target="_blank" <?php echo $followLink ?> target="_blank" itemprop="url" title="<?php echo $this->escape($this->company->name) ?> Website" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $this->escape($company->website) ?>"><i class="icon link-square"></i> <?php echo JText::_('LNG_WEBSITE') ?></a>
										</li>
									<?php } else { ?>
										<span style="display:none;" itemprop="url">
											<?php echo JBusinessUtil::getCompanyLink($this->company); ?>
										</span>
									<?php } ?>
									
									<?php if ((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
										<li>
											<a href="javascript:jbdListings.contactCompany(<?php echo $showData ? "1" : "0" ?>)"><i class="icon envelope"></i> <?php echo !empty($this->company->userId)?JText::_("LNG_CONTACT_COMPANY"):JText::_("LNG_REQUEST_MORE_INFO") ?></a>
										</li>
									<?php } ?>

									<?php if ($showData && ((!isset($this->company->userId) || $this->company->userId == 0) && $appSettings->claim_business)) { ?>
										<li>
											<i class="la la-check-square la-fw"></i> <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.checkUser&claim_listing_id=' . $this->company->id.$menuItemId) ?>"><?php echo JText::_('LNG_CLAIM_COMPANY') ?></a>
										</li>
									<?php  } ?>
								</ul>
							</div>

							<div class="item-element">
								<ul class="features-links">
									<?php if (
										$showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
										&& !empty($this->company->email) && $appSettings->enable_request_quote
									) { ?>
										<li>
											<a href="javascript:jbdListings.showQuoteCompany('<?php echo $this->company->id ?>','<?php echo $showData ? "1" : "0" ?>')"><i class="la la-question-circle la-fw"></i> <?php echo JText::_('LNG_QUOTE'); ?></a>
										</li>
									<?php } ?>
									<?php if ($this->showListLinkButton && $appSettings->enable_linked_listings) { ?>
										<li>
											<a href="javascript:jbdListings.joinListing(<?php echo count($this->joinedCompanies); ?>,<?php echo $this->company->id ?>,<?php echo ($user->ID == 0) ? 0 : 1 ?>,<?php echo count($this->userCompanies) < 2 ? false : true; ?>)"><i class="la la-user-plus la-fw"></i> <?php echo JText::_('LNG_JOIN_LEAVE'); ?></a>
										</li>
									<?php } ?>

									<?php if (!empty($appSettings->content_responsible)) { ?>
										<li>
											<span>
												<i class="icon info-circle" style="color: #0e84b5"></i> <a href="javascript:void(0)" style="padding-left: 0px" id="content-responsible-link"><?php echo JText::_('LNG_CONTENT_RESPONSIBLE_PERSON') ?></a>
											</span>
											<div id="content_responsible_text" style="display: none;">
												<?php echo JHTML::_("content.prepare", $appSettings->content_responsible); ?>
											</div>
										</li>
									<?php } ?>

									<?php if ($appSettings->enable_reporting) { ?>
										<li>
											<i class="la la-flag-o la-fw"></i> <a href="javascript:jbdListings.showReportAbuse()"><?php echo JText::_('LNG_REPORT_LISTING'); ?></a>
										</li>
									<?php } ?>
								</ul>
							</div>

							<?php
								if (
									$showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
									&& ((!empty($this->company->linkedin) || !empty($this->company->youtube) || !empty($this->company->facebook) || !empty($this->company->twitter)
										|| !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest) || !empty($this->company->whatsapp)))
								) {
									require_once 'listing_social_networks.php';
								}
							?>
						</div>
					</div>
				</div>
			</div>
			<?php if ($showData) { ?>
				<div class="col-md-2">
					<?php
					if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
						&& !empty($this->company->latitude) && !empty($this->company->longitude)
					) {
					?>
						<a href="javascript:jbdListings.showCompanyMap()" title="Show Map">
							<?php echo JBusinessUtil::getStaticMap($this->company->latitude, $this->company->longitude, null, 180, 240); ?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
		&& !empty($this->company->latitude) && !empty($this->company->longitude)
	) { ?>
		<div id="company-map-holder" class="company-cell">
			<div class="map_close">
				<a title="" class="" href="javascript:jbdUtils.hideMap()"><?php echo JText::_("LNG_CLOSE_MAP") ?></a>
			</div>
			<h2><?php echo JText::_("LNG_BUSINESS_MAP_LOCATION") ?></h2>
			<?php require_once 'map.php'; ?>
		</div>
	<?php } ?>
	<div class="clear"></div>

	<div class="company-menu">
		<nav>
			<a id="business-link" href="javascript:jbdListings.showDetails('company-business');" class="active"><?php echo JText::_("LNG_BUSINESS_DETAILS") ?></a>
			<?php
			if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->pictures) && count($this->pictures) > 0
			) {
			?>
				<a id="gallery-link" href="javascript:jbdListings.showDetails('company-gallery');" class=""><?php echo JText::_("LNG_GALLERY") ?></a>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->videos) && count($this->videos) > 0
			) {
			?>
				<a id="videos-link" href="javascript:jbdListings.showDetails('company-videos');" class=""><?php echo JText::_("LNG_VIDEOS") ?></a>
			<?php } ?>


			<?php if ((isset($this->package->features) && in_array(SOUNDS_FEATURE, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->sounds)
			) { ?>
				<a id="sounds-link" href="javascript:jbdListings.showDetails('company-sounds');" class=""><?php echo JText::_("LNG_SOUNDS") ?></a>
			<?php } ?>


			<?php
			if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers
			) {
			?>
				<a id="offers-link" href="javascript:jbdListings.showDetails('company-offers');" class=""><?php echo JText::_("LNG_OFFERS") ?></a>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->productCategories) && count($this->productCategories)
			) { ?>
				<a id="products-link" href="javascript:jbdListings.showDetails('company-products');" class=""><?php echo JText::_("LNG_PRODUCTS") ?></a>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->services) && count($this->services) && $appSettings->enable_services
			) {
			?>
				<a id="services-link" href="javascript:jbdListings.showDetails('company-services');" class=""><?php echo JText::_("LNG_SERVICES") ?></a>
			<?php } ?>

			<?php
			if (!empty($this->associatedCompanies) && $appSettings->enable_linked_listings) {
			?>
				<a id="associated-link" href="javascript:jbdListings.showDetails('company-associated');" class=""><?php echo JText::_("LNG_LINKED_COMPANIES") ?></a>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->events) && count($this->events) && $appSettings->enable_events
			) {
			?>
				<a id="events-link" href="javascript:jbdListings.showDetails('company-events');" class=""><?php echo JText::_("LNG_EVENTS") ?></a>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
				&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events
			) {
			?>
				<a id="associated-link" href="javascript:jbdListings.showDetails('events-associated');" class=""><?php echo JText::_("LNG_ASSOCIATED_EVENTS") ?></a>
			<?php } ?>

			<?php
			if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->companyTestimonials)
			) {
			?>
				<a id="testimonials-link" href="javascript:jbdListings.showDetails('company-testimonials');" class=""><?php echo JText::_("LNG_TESTIMONIALS") ?></a>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->services_list) && count($this->services_list)
			) {
			?>
				<a id="price-list-link" href="javascript:jbdListings.showDetails('company-price-list');" class=""><?php echo JText::_("LNG_PRICE_LIST") ?></a>
			<?php } ?>

			<?php
			if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
				&& !empty($this->companyProjects)){ ?>
				<a id="projects-link" href="javascript:jbdListings.showDetails('company-projects');" class=""><?php echo JText::_("LNG_PROJECTS") ?></a>
			<?php } ?>

			<?php 
			if (isset($this->offers) && count($this->offers) && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
				<a id="offers-price-list-link" href="javascript:jbdListings.showDetails('company-offers-price-list');" class=""><?php echo JText::_("LNG_OFFERS_PRICE_LIST") ?></a>
			<?php } ?>

			<?php if((isset($this->package->features) && in_array(MEMBERSHIPS,$this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) { ?>
				<a id="memberships-link" href="javascript:jbdListings.showDetails('company-memberships');" class=""><?php echo JText::_("LNG_MEMBERSHIPS") ?></a>
			<?php } ?>

			<?php if (!empty($this->companyArticles) && file_exists(JPATH_SITE . '/plugins/content/business/business.php')) { ?>
				<a id="listing-articles-link" href="javascript:jbdListings.showDetails('listing-articles');" class=""><?php echo JText::_("LNG_LISTING_ARTICLES") ?></a>
			<?php } ?>

			<?php if((!empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) && (isset($this->package->features)
				&& in_array(TEAM_FEATURE, $this->package->features) || !$appSettings->enable_packages) ){
			?>
				<a id="company-team-link" href="javascript:jbdListings.showDetails('company-team');" class=""><?php echo JText::_("LNG_TEAM") ?></a>
			<?php } ?>

			<?php  if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					    && $appSettings->enable_reviews ) {  ?>
				<a id="reviews-link" href="javascript:jbdListings.showDetails('company-reviews');" class=""><?php echo JText::_("LNG_REVIEWS") ?></a>
			<?php } ?>
		</nav>
	</div>

	<div id="company-details" class="company-cell">
		<?php if (isset($this->company->slogan) && strlen($this->company->slogan) > 2) { ?>
			<p class="business-slogan"><?php echo $this->escape($this->company->slogan) ?> </p>
		<?php } ?>

		<dl>
			<?php if (!empty($this->company->typeName)) { ?>
				<dt><?php echo JText::_('LNG_TYPE') ?>:</dt>
				<dd><?php echo $this->escape($this->company->typeName) ?></dd>
			<?php } ?>

			<?php if (!empty($this->company->establishment_year)) { ?>
				<dt><?php echo JText::_('LNG_ESTABLISHMENT_YEAR') ?>:</dt>
				<dd><?php echo $this->company->establishment_year ?></dd>
			<?php } ?>

			<?php if (!empty($this->company->employees)) { ?>
				<dt><?php echo JText::_('LNG_EMPLOYEES') ?>:</dt>
				<dd><?php echo $this->company->employees ?></dd>
			<?php } ?>

			<!-- Listing Categories -->
			<?php if (!empty($this->company->categoriesDetails)) { ?>
				<dt class="listing-category-title"><?php echo JText::_('LNG_CATEGORIES') ?>:</dt>
				<dd class="listing-category-content">
					<?php require_once 'listing_categories.php'; ?>
				</dd>
			<?php } ?>

			<?php if (!empty($this->company->keywords)) { ?>
				<dt><?php echo JText::_('LNG_KEYWORDS') ?>:</dt>
				<dd>
					<ul>
						<?php
						$keywords =  explode(',', $this->company->keywords);
						for ($i = 0; $i < count($keywords); $i++) { ?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&searchkeyword=' . $this->escape($keywords[$i]) . $menuItemId) ?>"><?php echo $this->escape($keywords[$i]) ?><?php echo $i < (count($keywords) - 1) ? ',&nbsp;' : '' ?></a>
							</li>
						<?php
						} ?>
					</ul>
				</dd>
			<?php } ?>

			<?php if (!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
				<dt><?php echo JText::_("LNG_GENERAL_INFO") ?></dt>
				<dd>
					<div id="dir-listing-description" class="dir-listing-description">
						<?php if (!empty($this->company->description) && (isset($this->package->features) && in_array(HTML_DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
							<?php echo JHTML::_("content.prepare", $this->company->description); ?>
						<?php }else if (!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
							<?php echo strip_tags($this->company->description); ?>
						<?php } ?>
					</div>
				</dd>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(CUSTOM_TAB, $this->package->features) || !$appSettings->enable_packages)
				&& !empty($this->company->custom_tab_name)
			) { ?>
				<dt><?php echo $this->company->custom_tab_name ?></dt>
				<dd><?php echo JHTML::_("content.prepare", $this->company->custom_tab_content);	?>&nbsp;</dd>
			<?php } ?>

			<?php if (
				!empty($this->company->locations) && $appSettings->show_secondary_locations == 1
				&& (isset($this->package->features) && in_array(SECONDARY_LOCATIONS, $this->package->features) || !$appSettings->enable_packages)
			) { ?>
				<dt><?php echo JText::_("LNG_COMPANY_LOCATIONS") ?></dt>
				<dd><?php require_once 'locations.php'; ?></dd>
			<?php } ?>

			<?php if ((isset($this->package->features) && in_array(OPENING_HOURS, $this->package->features) || !$appSettings->enable_packages)
				&& (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE
			) { ?>
				<dd><?php require_once 'listing_hours.php'; ?> </dd>
			<?php } ?>

			<?php if ($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
				<?php if (!empty($this->company->attachments)) { ?>
					<dt><?php echo JText::_("LNG_ATTACHMENTS") ?></dt>
					<dd>
						<?php require "listing_attachments.php" ?>
					</dd>
				<?php } ?>
			<?php } ?>
		</dl>

		<div class="classification">
			<?php require_once 'listing_attributes.php'; ?>
		</div>

		<?php if ((isset($this->package->features) && in_array(RELATED_COMPANIES, $this->package->features) || !$appSettings->enable_packages)
			&& isset($this->realtedCompanies) && count($this->realtedCompanies)
		) {
		?>
			<div class="listing-detail related">
				<div class="listing-detail-header"><?php echo JText::_('LNG_RELATED') ?></div>
				<?php require_once 'related_business.php'; ?>
			</div>
		<?php } ?>


	</div>
	<div class="clear"></div>

	<?php if ((isset($this->package->features) && in_array(ANNOUNCEMENTS, $this->package->features) || !$appSettings->enable_packages)
		&& !empty($this->companyAnnouncements) && count($this->companyAnnouncements) && $appSettings->enable_announcements
	) { ?>
		<div id="company-announcements" class="company-cell">
			<h2><?php echo JText::_("LNG_ANNOUNCEMENTS") ?></h2>
			<?php require_once 'listing_announcements.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
		&& ((isset($this->pictures) && count($this->pictures) > 0) || (isset($this->videos) && count($this->videos) > 0))
	) {
	?>
		<div id="company-gallery" class="company-cell">
			<h2><?php echo JText::_("LNG_GALLERY") ?></h2>
			<?php require_once 'gallery_slider.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
		&& isset($this->videos) && count($this->videos) > 0
	) {
	?>
		<div id="company-videos" class="company-cell">
			<h2><?php echo JText::_("LNG_VIDEOS") ?></h2>
			<?php require_once 'listing_videos.php'; ?>
		</div>
	<?php }	?>

	<?php if ((isset($this->package->features) && in_array(SOUNDS_FEATURE, $this->package->features) || !$appSettings->enable_packages)
		&& !empty($this->sounds)
	) { ?>
		<div id="company-sounds" class="company-cell">
			<h2><?php echo JText::_("LNG_SOUNDS") ?></h2>
			<?php require_once 'listing_sounds.php'; ?>
		</div>
	<?php }	?>

	<?php
	if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
		&& isset($this->offers) && count($this->offers) && $appSettings->enable_offers
	) {
	?>
		<div id="company-offers" class="company-cell" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
			<h2><?php echo JText::_("LNG_COMPANY_OFFERS") ?></h2>
			<?php require_once 'listing_offers.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php if ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages)
		&& isset($this->productCategories) && count($this->productCategories)
	) { ?>
		<div id="company-products" class="company-cell">
			<h2><?php echo JText::_("LNG_PRODUCTS") ?></h2>
			<?php require_once 'listing_product_categories.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
		&& isset($this->services) && count($this->services) && $appSettings->enable_services
	) {
	?>
		<div id="company-services" class="company-cell">
			<h2><?php echo JText::_("LNG_SERVICES") ?></h2>
			<?php require_once 'listing_services.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if (!empty($this->associatedCompanies) && $appSettings->enable_linked_listings) {
	?>
		<div id="company-associated" class="company-cell">
			<h2><?php echo JText::_("LNG_LINKED_COMPANIES") ?></h2>
			<?php require_once 'listing_associated_companies.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
		&& isset($this->events) && count($this->events) && $appSettings->enable_events
	) {
	?>
		<div id="company-events" class="company-cell">
			<h2><?php echo JText::_("LNG_COMPANY_EVENTS") ?></h2>
			<?php require_once 'listing_events.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
		&& isset($this->associatedEvents) && count($this->associatedEvents) && $appSettings->enable_events
	) {
	?>
		<div id="events-associated" class="company-cell">
			<h2><?php echo JText::_("LNG_ASSOCIATED_EVENTS") ?></h2>
			<?php require_once 'listing_associated_events.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php
	if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
		&& !empty($this->companyTestimonials)
	) {
	?>
		<div id="company-testimonials" class="company-cell">
			<h2><?php echo JText::_("LNG_TESTIMONIALS") ?></h2>
			<?php require_once 'listing_testimonials.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>



	<?php if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
		&& !empty($this->services_list) && $appSettings->enable_price_list
	) {
	?>
		<div id="company-price-list" class="company-cell">
			<h2><?php echo JText::_("LNG_PRICE_LIST") ?></h2>
			<?php require_once 'listing_price_list.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php 
	if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
		&& !empty($this->companyProjects)){ ?>
		<div id="company-projects" class="company-cell">
			<h2 onclick="jbdListings.returnToProjects();" onmouseover="this.style.cursor='hand';this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">
				<?php echo JText::_("LNG_PROJECTS") ?></h2>
			<?php require_once 'listing_projects.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php if (isset($this->offers) && count($this->offers)  && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
		<div id="company-offers-price-list" class="company-cell">
			<h2><?php echo JText::_("LNG_OFFERS_PRICE_LIST") ?></h2>
			<?php if($appSettings->offer_price_list_view_style == 1) {
						require_once 'listing_offers_price_list_style_2.php';
					} else {
						require_once 'listing_offers_price_list.php';
					}
			?>    		
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php if((isset($this->package->features) && in_array(MEMBERSHIPS,$this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) { ?>
		<div id="company-memberships" class="company-cell">
			<h2 onmouseover="this.style.cursor='hand';this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">
				<?php echo JText::_("LNG_MEMBERSHIPS") ?></h2>
			<?php require_once 'listing_memberships.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php if (!empty($this->companyArticles) && file_exists(JPATH_SITE . '/plugins/content/business/business.php')) { ?>
		<div id="listing-articles" class="company-cell">
			<h2><?php echo JText::_("LNG_LISTING_ARTICLES") ?></h2>
			<?php require_once 'listing_articles.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php if((!empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) && (isset($this->package->features)
		&& in_array(TEAM_FEATURE, $this->package->features) || !$appSettings->enable_packages) ){
	?>
		<div id="company-team" class="company-cell">
			<h2><?php echo JText::_("LNG_TEAM") ?></h2>
			<?php require_once 'listing_team.php'; ?>
		</div>
		<div class="clear"></div>
	<?php } ?>

	<?php  if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
				&& $appSettings->enable_reviews ) {  ?>
		<div id="company-reviews" class="company-cell">
			<h2><?php echo JText::_("LNG_BUSINESS_REVIEWS") ?></h2>
			<?php require_once 'listing_reviews.php'; ?>
		</div>
		<?php if(!$appSettings->enable_reviews_users || !$user->ID ==0) { ?>
			<div id="company-reviews" class="company-cell pt-4">
				<?php require_once 'default_addreview.php'; ?>
			</div>
		<?php } ?>
		<div class="clear"></div>
	<?php } ?>

	<form name="tabsForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory' . $menuItemId) ?>" id="tabsForm" method="post">
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>" />
		<input type="hidden" name="task" value="companies.displayCompany" />
		<input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId ?>" />
		<input type="hidden" name="view" value="companies" />
		<input type="hidden" name="layout2" id="layout2" value="" />
		<input type="hidden" name="companyId" value="<?php echo $this->company->id ?>" />
		<input type="hidden" name="controller" value="companies" />
	</form>
</div>

<script>
	window.addEventListener('load', function() {
		<?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
			&& !empty($this->company->latitude) && !empty($this->company->longitude)
		) { ?>
			loadDetailsViewMap();
		<?php } ?>
		jbdUtils.hideMap();
	});
</script>

<?php if ( is_active_sidebar( 'wpbd-listing-details' ) ) { ?>
	<div class="dir-company-module">
		<?php dynamic_sidebar( 'wpbd-listing-details' ); ?>
	</div>
<?php } ?>

<?php require_once 'listing_util.php'; ?>