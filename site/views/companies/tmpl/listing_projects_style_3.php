<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
?>

<div id="company-projects-container" class="projects-container project-style-2">
    <div class="row">
		<?php
        foreach($this->companyProjects as $index=>$project){ ?>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card jitem-card project-card">
                    <div class="jitem-img-wrap small">
                        <a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);" href="javascript:void(0)"></a>
						<?php if(!empty($project->picture_path)){?>
                            <img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.$project->picture_path ?>">
						<?php }else{ ?>
                            <img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>">
						<?php } ?>
                    </div>

	                <?php if (!empty($project->pictures) && $appSettings->projects_show_images == 1) { ?>
                        <div class="d-flex project-thumbs">
			                <?php
                                $picturesCount = count($project->pictures);
                                $displayPicturesCount = $picturesCount > 3 ? 3 : $picturesCount;
                                $lastPic = false;
                                for ($i = 0; $i < $displayPicturesCount; $i++) {
                                    $picture = $project->pictures[$i];
                                    if ($i == $displayPicturesCount) {
                                        $lastPic = true;
                                    }
			                ?>
                                <div class="project-img">
                                    <a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);" href="javascript:void(0)">
                                        <img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.$picture[3] ?>">
                                    </a>
                                </div>
			                <?php } ?>
                        </div>
	                <?php } ?>

                    <div class="jitem-body">
                        <div class="jitem-body-content">
                            <div class="jitem-title">
                                <a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);" href="javascript:void(0)">
                                    <?php echo $project->name?>
                                </a>
                            </div>
                            <div class="jitem-desc text-small">
                                <div class="jitem-desc-content">
                                    <p><?php echo $project->nrPhotos . " ". JText::_("LNG_PHOTOS");?></p>
                                    <!-- p> <?php echo JBusinessUtil::truncate( strip_tags($project->description), 140); ?></p-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		<?php } ?>
    </div>
</div>

<div id="popup-gallery-full" class="popup-gallery-full jbd-container" style="display:none">
    <div class="close"><i class="la la-close"></i></div>
    <div class="gallery-wrapper">
        <div class="row h-100">
            <div class="col-12 col-lg-9">
                <div id="popup-header">
                    <div id="project-name" class="title"><?php echo $this->popupTitle ?></div>
                    <div id="project-description" class="py-3"></div>
                </div>
                <div class='picture-container h-100' id="project-gallery">
                </div>
            </div>
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar">
                    <div class="gallery-side-bar">
                        <div style="display:none">
                            <div class="d-flex listing-contact-header">
                                <div class="item-header-photo">
                                    <img src="<?php echo !empty($this->company->logoLocation) ? BD_PICTURES_PATH.$this->company->logoLocation : BD_PICTURES_PATH.$appSettings->no_image ?>" alt="">
                                </div>
                                <div class="item-header-content">
                                    <div class="item-header-title">
                                        <?php echo $this->company->name ?>
                                    </div>
                                    <?php if ($this->appSettings->enable_ratings) { ?>
                                        <div class="rating d-flex align-items-center">
                                            <span class="rating-average-review" id="rating-average-review" title="<?php echo $this->company->review_score ?>" alt="<?php echo $this->company->id ?>" style="display: block;"></span>
                                            <div class="header-reviews-count"><?php echo count($this->reviews) . " " .((count($this->reviews)>1)? JText::_("LNG_REVIEWS"):JText::_("LNG_REVIEW")); ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
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
                                <div class="listing-style-9">
                                    <div class="content-box">
                                        <div class="content-box-body">
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
                                </div>
                            <?php } ?>

                        </div>
                        <div class="gallery-side-bar-wrapper">
                            <form id="contactCompanyFrmPopop" name="contactCompanyFrmPopop" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <p class="header-title"><strong><?php echo JText::_('LNG_CONTACT') ?></strong></p>

                                <div class="form-group">
                                    <label for="description"><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
                                    <div class="outer_input">
                                        <textarea rows="7" name="description" id="description" cols="50" class="form-control validate[required]" required=""></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstName"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                                            <div class="outer_input">
                                                <input type="text" name="firstName" id="firstName" class="validate[required]" required="">
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label>
                                            <div class="outer_input">
                                                <input type="text" name="lastName" id="lastName" class="validate[required]" required="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="jinput-email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                                    <div class="outer_input">
                                        <input type="text" name="email" id="jinput-email" class="validate[required,custom[email]]" required="">
                                    </div>
                                </div>                   

                                <div class="row">
                                    <div class="col-12">
                                        <div class="jbd-checkbox justify-content-end">
                                            <input type="checkbox"  name="copy-me" value="1">
                                            <label for="copy-me"><?php echo JText::_('LNG_COPY_ME')?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>
                                    </div>

                                    <?php if ($this->appSettings->captcha) { ?>
                                        <div class="form-item">
                                            <?php
                                            $namespace = "jbusinessdirectory.contact";
                                            $class = " required";

                                            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                                            if (!empty($captcha)) {
                                                echo $captcha->display("captcha", "captcha-div-contact", $class);
                                            }
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="btn-group" role="group" aria-label="">
                                    <button type="button" class="btn btn-success" onclick="jbdUtils.saveForm('contactCompanyFrmPopop')"><?php echo JText::_("LNG_CONTACT_COMPANY")?></button>
                                </div>
              
                                <?php echo JHTML::_( 'form.token' ); ?>
                                <input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
                                <input type='hidden' name='task' id="task" value='companies.contactCompany'/>
                                <input type='hidden' name='userId' value='<?php echo $user->ID?>'/>
                                <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       
    <div class="overlay"></div>
</div>


<script>
    var unitegalleryprojects = null;
    window.addEventListener('load', function() {
        jQuery(".overlay").click(function(){
            jQuery('#popup-gallery-full').hide();
            jQuery("body").removeClass("fixed");
        });

        jQuery(".close").click(function(){
            jQuery('#popup-gallery-full').hide();
            jQuery("body").removeClass("fixed");
        });

        jQuery('#popup-gallery-full').appendTo(document.body);
    });
</script>