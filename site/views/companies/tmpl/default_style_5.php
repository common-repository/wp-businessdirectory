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
?>

<div id="company-style-5-container" itemscope itemtype="http://schema.org/LocalBusiness">
	<div>
		<div class="dir-print header-icons">
			<?php require_once JPATH_COMPONENT_SITE . "/include/social_share.php"; ?>
			<a class="print-icon" rel="nofollow" href="javascript:jbdUtils.printItem('<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=companies&tmpl=component&layout=print&companyId=" . $this->company->id); ?>')"><i class="icon print-circle"></i></a>
			<?php if ($appSettings->show_contact_cards) { ?>
				<a rel="nofollow" target="_blank" class="" title="<?php echo JText::_("LNG_QR_CODE") ?>" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateQrCode&itemId=" . $this->company->id); ?>"><i class="la la-qrcode"></i></a>
				<a rel="nofollow" class="" title="<?php echo JText::_("LNG_VCARD") ?>" href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&task=companies.generateVCard&itemId=" . $this->company->id); ?>"><i class="la la-comments"></i></a>
			<?php } ?>
		</div>
		<?php require_once 'breadcrumbs.php'; ?>
	</div>
	<div class="clear"></div>
	<div id="company-style-5-header">
		<div class="row">
			<div class="col-12">
				<!-- Business Categories -->
				<div class="company-style-5-header-image col-md" style="background-image:<?php echo !empty($this->company->business_cover_image)?("url('".BD_PICTURES_PATH.$this->company->business_cover_image." ')"):("url('".BD_PICTURES_PATH.$appSettings->default_bg_listing." ')"); ?>">
					<div class="company-style-5-header-info row">
						<div class="col-md-9 first-column">
							<div class="row">
								<?php if (isset($this->package->features) && in_array(SHOW_COMPANY_LOGO, $this->package->features) || !$appSettings->enable_packages) { ?>
									<span itemprop="image" style="display:none;"><?php echo (!empty($this->company->logoLocation)) ? BD_PICTURES_PATH . $this->company->logoLocation : BD_ASSETS_FOLDER_PATH . "no_image.jpg" ?></span>

									<div class="col-md-3" itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
										<!-- Business Logo -->
										<?php if (!empty($this->company->logoLocation)) { ?>
											<img class="business-logo" title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo BD_PICTURES_PATH . $this->company->logoLocation ?>" itemprop="contentUrl">
										<?php } else { ?>
											<img class="business-logo" title="<?php echo $this->escape($this->company->name) ?>" alt="<?php echo $this->escape($this->company->name) ?>" src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>" itemprop="contentUrl">
										<?php } ?>
									</div>
								<?php } ?>
								<div class="col-md">
									<!-- Business Name -->
									<h1 itemprop="name">
										<?php echo isset($this->company->name) ? $this->escape($this->company->name) : ""; ?>
										<?php if ($this->company->recommended && $appSettings->show_recommended) { ?>
											<span class="recommended"><i class="la la-star"></i> <?php echo JText::_("LNG_RECOMMENDED") ?></span>
										<?php } ?>
										<?php if ($this->company->userId != 0 && $appSettings->show_claimed) { ?>
											<span class="claimed"><i class="la la-check"></i> <?php echo JText::_("LNG_CLAIMED") ?></span>
										<?php } ?>
									</h1>

									<?php $address = JBusinessUtil::getAddressText($this->company); ?>
									<?php if ($showData && !empty($address)) { ?>
										<div class="dir-address">
											<span itemprop="address">
												<!-- Business Address -->
												<?php echo $address ?>
											</span>
										</div>
									<?php } ?>
									<div class="dir-categories">
										<?php require_once 'listing_categories.php'; ?>
									</div>
									<?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
										<?php if (!empty($this->company->phone)) { ?>
											<div>
												<span class="phone" itemprop="telephone">
													<i class="la la-phone la-fw"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
												</span>
											</div>
										<?php } ?>

										<?php if (!empty($this->company->mobile)) { ?>
											<div>
												<span class="phone" itemprop="telephone">
													<i class="la la-mobile la-fw"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
												</span>
											</div>
										<?php } ?>

										<?php if (!empty($this->company->fax)) { ?>
											<div>
												<span class="faxNumber">
													<i class="la la-fax la-fw"></i> <?php echo $this->escape($this->company->fax) ?>
												</span>
											</div>
										<?php } ?>
									<?php } ?>

									<div class="company-info-review">
										<!-- Business Ratings -->
										<?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                    			&& $appSettings->enable_reviews) {?>
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
													<span class="user-rating-avg" id="rating-average" title="<?php echo $this->company->review_score ?>" alt="<?php echo $this->company->id ?>" style="display: block;"></span>
												</div>
											</div>
										<?php } ?>
										<div class="review-info">
											<?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                   					 && $appSettings->enable_reviews) {
												if (GET_DATA_FROM_YELP) {
													$totalReview = isset($this->reviews->error) ? 0 : $this->reviews->total;
												} else {
													$totalReview = count($this->totalReviews);
												}
											?>
												<a href="<?php echo $url ?>#go-company-reviews"><span><?php echo $totalReview ?> <?php echo JText::_('LNG_REVIEWS'); ?></span></a>
											<?php } ?>

											<?php if ($this->appSettings->enable_bookmarks) { ?>
												<?php if ($appSettings->enable_reviews) { ?> | <?php } ?>
												<?php if (!empty($company->bookmark)) { ?>
													<a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showUpdateBookmarkDialog(<?php echo $user->ID == 0 ? "1" : "0" ?>,<?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_UPDATE_BOOKMARK") ?>" class="bookmark"><i class="la la-heart"></i> </a>
												<?php } else { ?>
													<a id="bookmark-<?php echo $company->id ?>" href="javascript:jbdUtils.showAddBookmark(<?php echo $user->ID == 0 ? "1" : "0" ?>,<?php echo $company->id ?>,<?php echo BOOKMARK_TYPE_BUSINESS ?>)" title="<?php echo JText::_("LNG_ADD_BOOKMARK") ?>" class="bookmark"><i class="la la-heart-o"></i> </span></a>
												<?php } ?>
											<?php } ?>
										</div>
									</div>
									<div class="clear"></div>
									<div class="attribute-icon-container">
										<?php foreach ($this->companyAttributes as $attribute) {
											$packageFeatured = isset($this->package->features) ? $this->package->features : null;
											$icons = AttributeService::getAttributeIcons($attribute, $appSettings->enable_packages, $packageFeatured);
											$color = !empty($attribute->color) ? $attribute->color : '';
											if (!empty($icons)) {
												foreach ($icons as $icon)
													echo '<i class="' . $icon . ' attribute-icon" style="color:' . $color . ';"></i>';
											}
										} ?>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 second-column">
							<!-- Business Socials -->
							<?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					                    && $appSettings->enable_reviews) { ?>
								<div class="clear">
									<!-- Business Add Review -->
									<a href="<?php echo $url ?>#reviews" onclick="jbdListings.showReviewForm(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0"; ?>);event.stopPropagation();" class="btn btn-outline">
										<?php echo JText::_("LNG_ADD_NEW_REVIEW") ?>
									</a>
								</div>
							<?php } ?>
						</div>
					</div>
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
		</div>
	</div>

	<div id="company-style-5-body">
		<div class="row">
			<!-- BODY -->
			<div class="col-md-8">
				<!-- Business Gallery -->
				<?php if ((isset($this->package->features) && in_array(IMAGE_UPLOAD, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->pictures)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-camera-retro"></i> <?php echo JText::_("LNG_GALLERY"); ?></h3>
								<?php require_once JPATH_COMPONENT_SITE . "/include/image_gallery.php";  ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<!-- Business Details -->
				<div class="company-style-box">
					<div class="row">
						<div class="col-md">
							<h3><i class="la la-newspaper"></i> <?php echo JText::_("LNG_COMPANY_DETAILS"); ?></h3>
							<!-- Business Slogan -->
							<?php if (isset($this->company->slogan) && strlen($this->company->slogan) > 2) { ?>
								<p class="business-slogan"><?php echo $this->escape($this->company->slogan); ?> </p>
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
								<!-- Business Type -->
								<?php if (!empty($this->company->typeName)) { ?>
									<dt><?php echo JText::_('LNG_TYPE'); ?>:</dt>
									<dd><?php echo $this->escape($this->company->typeName); ?></dd>
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
									<dd>
										<ul class="dir-keywords">
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

				<?php if ((isset($this->package->features) && in_array(CUSTOM_TAB, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->company->custom_tab_name)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3> <?php echo $this->company->custom_tab_name; ?></h3>
								<div>
									<?php echo JHTML::_("content.prepare", $this->company->custom_tab_content); ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Videos -->
				<?php if ((isset($this->package->features) && in_array(VIDEOS, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->videos)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-video-camera"></i> <?php echo JText::_("LNG_VIDEOS") ?></h3>
								<div id="company-videos">
									<?php require_once 'listing_videos.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php }	?>

				<!-- Business Sounds -->
				<?php if ((isset($this->package->features) && in_array(SOUNDS_FEATURE, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->sounds)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-music"></i> <?php echo JText::_("LNG_SOUNDS") ?></h3>
								<div id="company-sounds">
									<?php require_once 'listing_sounds.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php }	?>

				<!-- Business Map Location -->
				<?php if (
					$showData && (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->company->latitude) && !empty($this->company->longitude)
				) { ?>
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
				<?php } ?>

				<!-- Business Offers -->
				<?php if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->offers) && $appSettings->enable_offers
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

				<!-- Business Products -->
				<?php if ((isset($this->package->features) && in_array(COMPANY_OFFERS, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->products) && $appSettings->enable_offers
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-tag"></i> <?php echo JText::_("LNG_PRODUCTS"); ?></h3>
								<div id="company-offers" itemprop="hasOfferCatalog" itemscope itemtype="http://schema.org/OfferCatalog">
									<?php require_once 'listing_products.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>


				<!-- Related Companies -->
				<?php if ((isset($this->package->features) && in_array(RELATED_COMPANIES, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->realtedCompanies)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-tag"></i> <?php echo JText::_("LNG_RELATED_COMPANIES"); ?></h3>
								<div id="company-related" class="company-cell">
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
								<div id="company-related" class="company-cell">
									<?php require_once 'listing_associated_companies.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Company Services -->
				<?php if ((isset($this->package->features) && in_array(COMPANY_SERVICES, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->services) && count($this->services) && $appSettings->enable_services
				) {
				?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-th-list"></i> <?php echo JText::_("LNG_SERVICES"); ?></h3>
								<div id="company-services">
									<?php require_once 'listing_services.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Events -->
				<?php if ((isset($this->package->features) && in_array(COMPANY_EVENTS, $this->package->features) || !$appSettings->enable_packages)
					&& isset($this->events) && count($this->events) && $appSettings->enable_events
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-calendar"></i> <?php echo JText::_("LNG_COMPANY_EVENTS"); ?></h3>
								<div id="company-events">
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
								<div id="associated-events">
									<?php require_once 'listing_associated_events.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Company Testimonials -->
				<?php if ((isset($this->package->features) && in_array(TESTIMONIALS, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->companyTestimonials)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-quote-left"></i> <?php echo JText::_("LNG_TESTIMONIALS") ?></h3>
								<div id="company-testimonials">
									<?php require_once 'listing_testimonials.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>


				<!-- Business Price List -->
				<?php if ((isset($this->package->features) && in_array(SERVICES_LIST, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->services_list) && $appSettings->enable_price_list
				) { ?>
					<div id="company-price-list" class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-list-alt"></i> <?php echo JText::_("LNG_PRICE_LIST"); ?></h3>
								<div id="company-reviews">
									<?php require_once 'listing_price_list.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Price List -->
				<?php if ((isset($this->package->features) && in_array(ANNOUNCEMENTS, $this->package->features) || !$appSettings->enable_packages)
					&& !empty($this->companyAnnouncements) && count($this->companyAnnouncements) && $appSettings->enable_announcements
				) { ?>
					<div id="company-announcements-list" class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-list-alt"></i> <?php echo JText::_("LNG_ANNOUNCEMENTS"); ?></h3>
								<div id="company-reviews">
									<?php require_once 'listing_announcements.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Products -->
				<?php if (
					false && ((isset($this->package->features) && in_array(PRODUCTS, $this->package->features) || !$appSettings->enable_packages))
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

				<!-- Business Projects -->
				<?php 
				if((isset($this->package->features) && in_array(PROJECTS,$this->package->features) || !$appSettings->enable_packages) 
					&& !empty($this->companyProjects)){ ?>
					<div id="company-projects" class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3 onclick="jbdListings.returnToProjects();" onmouseover="this.style.cursor='hand';this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">
									<i class="la la-briefcase"></i> <?php echo JText::_("LNG_PROJECTS"); ?>
								</h3>
								<div>
									<?php require_once 'listing_projects.php'; ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Offers Price List -->
				<?php if (isset($this->offers) && count($this->offers)  && in_array("1", array_column($this->offers, "add_to_price_list")) && $appSettings->enable_offers && $appSettings->show_offer_price_list) { ?>
					<div id="company-team" class="company-style-box">
						<div class="row">
							<div class="col-12">
								<h3>
									<i class="la la-list-alt"></i> <?php echo JText::_("LNG_OFFERS_PRICE_LIST"); ?>
								</h3>
								<div>
									<?php if($appSettings->offer_price_list_view_style == 1) {
												require_once 'listing_offers_price_list_style_2.php';
											} else {
												require_once 'listing_offers_price_list.php';
											}
									?>  
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Listing Team -->
				<?php if((isset($this->package->features) && in_array(TEAM_FEATURE,$this->package->features) || !$appSettings->enable_packages )
					&& !empty($this->teamMembers['leadership']) || !empty($this->teamMembers['team'])) { ?>
					<div id="company-team" class="company-style-box">
						<div class="row">
							<div class="col-12">
								<h3>
									<i class="la la-user"></i> <?php echo JText::_("LNG_TEAM"); ?>
								</h3>
								<div>
									<?php require_once 'listing_team.php'; ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Reviews -->
    			<?php if ($appSettings->enable_packages && $appSettings->enable_reviews && isset($this->package->features) && in_array(REVIEWS,$this->package->features) || !$appSettings->enable_packages 
					    	&& $appSettings->enable_reviews) { ?>
					<div id="go-company-reviews" class="company-style-box">
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
				<?php } ?>
			</div>
			<!-- SIDEBAR -->
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
					<!-- Business Map -->
					<div class="row">
						<div class="col-md">
							<div class="dir-map-image">
								<?php if ((isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages) && !empty($this->company->latitude) && !empty($this->company->longitude)) {
									echo JBusinessUtil::getStaticMap($this->company->latitude, $this->company->longitude);
								} ?>
							</div>
						</div>
					</div>

					<!-- Business Address -->
					<div class="row">
						<?php $address = JBusinessUtil::getAddressText($this->company); ?>
						<?php if (!empty($address)) { ?>
							<div class="col-md dir-address">
								<?php echo $address ?>
							</div>
						<?php } ?>
					</div>

					<!-- Business Contact Informations -->
					<div class="row">
						<div class="col-md">
							<div class="company-info-details">
								<?php if (!empty($this->company->email) && $showData && $appSettings->show_email) { ?>
									<div>
										<span itemprop="email">
											<i class="la la-envelope"></i> <a href="mailto:<?php echo $this->escape($this->company->email) ?>"><?php echo $this->escape($this->company->email) ?></a>
										</span>
									</div>
								<?php } ?>

								<?php if ($showData && (isset($this->package->features) && in_array(PHONE, $this->package->features) || !$appSettings->enable_packages)) { ?>
									<?php if (!empty($this->company->phone)) { ?>
										<div>
											<span class="phone" itemprop="telephone">
												<i class="la la-phone"></i> <a href="tel:<?php echo $this->escape($this->company->phone); ?>"><?php echo $this->escape($this->company->phone); ?></a>
											</span>
										</div>
									<?php } ?>

									<?php if (!empty($this->company->mobile)) { ?>
										<div>
											<span class="phone" itemprop="telephone">
												<i class="icon mobile"></i> <a href="tel:<?php echo $this->escape($this->company->mobile); ?>"><?php echo $this->escape($this->company->mobile); ?></a>
											</span>
										</div>
									<?php } ?>

									<?php if (!empty($this->company->fax)) { ?>
										<div>
											<span class="faxNumber">
												<i class="la la-fax la-fw"></i> <?php echo $this->escape($this->company->fax) ?>
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
									<div>
										<i class="la la-globe la-fw"></i>
										<a <?php echo $followLink ?> itemprop="url" class="website" title="<?php echo $this->escape($this->company->name) ?> Website" target="_blank" onclick="jbdUtils.registerStatAction(<?php echo $company->id ?>,<?php echo STATISTIC_ITEM_BUSINESS ?>,<?php echo STATISTIC_TYPE_WEBSITE_CLICK ?>)" href="<?php echo $this->escape($company->website) ?>">
											<?php echo JText::_('LNG_WEBSITE') ?>
										</a>
									</div>
								<?php } else { ?>
									<span style="display:none;" itemprop="url">
										<?php echo JBusinessUtil::getCompanyLink($this->company); ?>
									</span>
								<?php } ?>


								<?php if ((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages) && !empty($company->email) && $appSettings->show_contact_form) { ?>
									<div>
										<br />
										<a href="javascript:jbdListings.contactCompany(<?php echo $showData ? "1" : "0" ?>)" class="btn btn-primary">
											<i class="la la-envelope"></i> <?php echo !empty($this->company->userId)?JText::_("LNG_CONTACT_COMPANY"):JText::_("LNG_REQUEST_MORE_INFO") ?>
										</a>
									</div>
								<?php } ?>

								<?php if ($this->showListLinkButton && $appSettings->enable_linked_listings) { ?>
									<div>
										<br />
										<a href="javascript:jbdListings.joinListing(<?php echo count($this->joinedCompanies); ?>,<?php echo $this->company->id ?>,<?php echo ($user->ID == 0) ? 0 : 1 ?>,<?php echo count($this->userCompanies) < 2 ? false : true; ?>)" class="btn btn-primary">
											<i class="la la-user"></i> <span class="ui-button-text"><?php echo JText::_("LNG_JOIN_LEAVE") ?></span>
										</a>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if (((isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
						&& !empty($this->company->email) && $appSettings->enable_request_quote)
					|| $appSettings->enable_reporting
					|| !empty($appSettings->content_responsible)
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<?php if (
									$showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
									&& !empty($this->company->email) && $appSettings->enable_request_quote
								) { ?>
									<div class="info-detail">
										<a href="javascript:jbdListings.showQuoteCompany('<?php echo $this->company->id ?>','<?php echo $showData ? "1" : "0" ?>')" class="">
											<i class="la la-envelope"></i> <span class="ui-button-text"><?php echo JText::_("LNG_REQUEST_QUOTE") ?></span>
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
					</div>
				<?php } ?>

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

				<!-- Business Social Networks -->
				<?php if (($showData && (isset($this->package->features) && in_array(SOCIAL_NETWORKS, $this->package->features) || !$appSettings->enable_packages)
					&& ((!empty($this->company->linkedin) || !empty($this->company->youtube) || !empty($this->company->facebook) || !empty($this->company->twitter)
						|| !empty($this->company->linkedin) || !empty($this->company->skype) || !empty($this->company->instagram) || !empty($this->company->pinterest || !empty($this->company->whatsapp)))))) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<h3><i class="la la-share-alt"></i> <?php echo JText::_("LNG_SOCIAL_NETWORK"); ?></h3>
								<?php require_once 'listing_social_networks.php'; ?>
							</div>
						</div>
					</div>
				<?php } ?>

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

				<!-- Business Hours -->
				<?php if ((isset($this->package->features) && in_array(OPENING_HOURS, $this->package->features) || !$appSettings->enable_packages)
					&& (!empty($this->company->business_hours) && $this->company->enableWorkingStatus) || $this->company->opening_status != COMPANY_OPEN_BY_TIMETABLE
				) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<?php require_once 'listing_hours.php'; ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- Business Trail Weeks -->
				<?php if (!empty($appSettings->trail_weeks_dates) && !empty($this->company->trail_weeks_hours) && $this->company->trail_weeks_status == 1) { ?>
					<div class="company-style-box">
						<div class="row">
							<div class="col-md">
								<?php require_once 'listing_trail_weeks.php'; ?>
							</div>
						</div>
					</div>
				<?php } ?>

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

<form name="tabsForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory' . $menuItemId) ?>" id="tabsForm" method="post">
	<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>" />
	<input type="hidden" name="task" value="companies.displayCompany" />
	<input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId ?>" />
	<input type="hidden" name="view" value="companies" />
	<input type="hidden" name="layout2" id="layout2" value="" />
	<input type="hidden" name="companyId" value="<?php echo $this->company->id ?>" />
	<input type="hidden" name="controller" value="companies" />
	<input type="hidden" name="facebook_url" id="facebook_url" value="cmsjunkie" />
</form>

<script>
	window.addEventListener('load', function() {
		<?php if (
			$showData && (isset($this->package->features) && in_array(GOOGLE_MAP, $this->package->features) || !$appSettings->enable_packages)
			&& !empty($this->company->latitude) && !empty($this->company->longitude)
		) { ?>
			loadDetailsViewMap();
		<?php }	?>
	});
</script>

<?php require_once 'listing_util.php'; ?>