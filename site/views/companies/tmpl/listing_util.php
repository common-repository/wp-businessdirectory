<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>
<?php JBusinessUtil::includeValidation(); ?>

<?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || $showData && !$appSettings->enable_packages) && !empty($company->email)){
    if ($user->ID > 0) {
        $userNameDetails = explode(' ', $user->name);
        $firstName = $userNameDetails[0];
        $lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
    }
    ?>
<div id="company-contact" class="jbd-container jbd-edit-container" style="display:none;">
	<form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
    	<div class="jmodal-sm">
            <div class="jmodal-header listing-contact-header">
                <div class="jmodal-header-background" style="background-image:<?php echo !empty($this->company->business_cover_image)?("url('".BD_PICTURES_PATH.$this->company->business_cover_image." ')"):BD_PICTURES_PATH.$appSettings->default_bg_listing; ?>">
                    <div class="dir-overlay"></div>
                </div>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                <div class="jmodal-header-contact">
                    <div class="item-header-photo">
                        <img src="<?php echo !empty($this->company->logoLocation) ? BD_PICTURES_PATH.$this->company->logoLocation : BD_PICTURES_PATH.$appSettings->no_image ?>" alt="">
                    </div>
                    <div class="item-header-content">
                        <p class="head-text"><?php echo !empty($this->company->userId)?JText::_('LNG_SEND_MESSAGE_TO'):JText::_('LNG_REQUEST_INFO_FROM') ?></p>
                        <div class="item-header-title">
                            <?php echo $this->company->name ?>
                        </div>
                        <?php if ($this->appSettings->enable_ratings && false) { ?>
                            <div class="rating d-flex align-items-center">
                                <span class="rating-average-review" id="rating-average-review" title="<?php echo $this->company->review_score ?>" alt="<?php echo $this->company->id ?>" style="display: block;"></span>
                                <div class="header-reviews-count"><?php echo count($this->totalReviews) . " " .((count($this->totalReviews)>1)? JText::_("LNG_REVIEWS"):JText::_("LNG_REVIEW")); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
    		</div>
    		<div class="jmodal-body">  
                    <!-- First Step -->
                <div class="form-step-1">
                    <div class="row">
                        <div class="col-12">
                            <div class="">      
                                <label for="description" class="font-weight-bold"><?php echo JText::_('LNG_MESSAGE')?>:</label>
                                <textarea rows="14" name="description" id="description" class="form-control validate[required]" placeholder="<?php echo JText::_('LNG_CONTACT_TEXT')?>..." required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                    <!-- Second Step -->
		        <div class="form-step-2" style="display:none">
                    <?php if(!empty($this->companyContactsEmail)){?>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <label for="jinput-cnt"><?php echo JText::_('LNG_COMPANY_CONTACT') ?></label>
                                    <select name="contact_id" id="jinput-cnt" class="inputbox" required="">
                                        <option value=""><?php echo JText::_('LNG_GENERAL');?></option>
                                        <?php echo JHtml::_('select.options', $this->companyContactsEmail, 'id', 'contact_name');?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="">
                                <label for="jinput-fn"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                                <input class="validate[required]" id="jinput-fn" name="firstName" type="text" value="<?php echo $user->ID>0?$firstName:""?>" required="" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="">
                                <label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label> 
                                <input class="validate[required]" id="lastName" type="text" name="lastName" value="<?php echo $user->ID>0?$lastName:""?>" required="">
                            </div>
                        </div>
                    </div>	
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <label for="jinput-email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>    
                                <input type="text" name="email" id="jinput-email" class="validate[required,custom[email]]" value="<?php echo $user->ID>0?$user->email:""?>" required="">
                            </div>
                        </div> 
                    </div> 
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <label for="jinput-phone"><?php echo JText::_('LNG_PHONE') ?></label>    
                                <input type="text" name="phone" id="jinput-phone" class="validate[required]" value="" required="">
                            </div>
                        </div> 
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="jbd-checkbox justify-content-end">
                                <label for="copy-me"><?php echo JText::_('LNG_COPY_ME')?></label>
                                <input type="checkbox" name="copy-me" value="1" /> 
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>
                        </div>
                    </div>
            
                    <?php if($this->appSettings->captcha){?>
                        <div class="form-item">
                            <?php 
                                $namespace="jbusinessdirectory.contact";
                                $class=" required";
                                
                                $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
                                                                    
                                if(!empty($captcha)){	
                                    echo $captcha->display("captcha", "captcha-div-contact", $class);
                                }
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
    		<div class="jmodal-footer">
    			<div class="btn-group" role="group" aria-label="">                
                    <div class="btn-step-1">
                        <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close();"><?php echo JText::_("LNG_CANCEL")?></button>
                        <button type="button" class="jmodal-btn jbd-commit jbd-btn-next" disabled><?php echo JText::_("LNG_NEXT")?></button> 
                    </div>
                    <div class="btn-step-2" style="display:none">
                        <button type="button" class="jmodal-btn jmodal-btn-outline jbd-btn-back"><?php echo JText::_("LNG_BACK")?></button>
                        <button type="button" class="jmodal-btn jbd-commit" onclick="jbdUtils.saveForm('contactCompanyFrm')"><?php echo JText::_("LNG_SEND")?></button>
                    </div>    	 			 
                </div>
    		</div>
    	</div>
    	
    	<?php echo JHTML::_( 'form.token' ); ?>
		<input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
		<input type='hidden' name='task' id="task" value='companies.contactCompany'/>
		<input type='hidden' name='userId' value='<?php echo $user->ID?>'/>
		<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
	</form>
</div>
<?php } ?>


<?php if((isset($this->package->features) && in_array(CONTACT_FORM,$this->package->features) || $showData && !$appSettings->enable_packages) && !empty($company->email)){
    if ($user->ID > 0) {
        $userNameDetails = explode(' ', $user->name);
        $firstName = $userNameDetails[0];
        $lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
    }
    ?>
<div id="company-claim" class="jbd-container" style="display:none;">
	<form id="claimCompanyFrm" name="claimCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
    	<div class="jmodal-sm">
    		<div class="jmodal-header">
    			<p class="jmodal-header-title"><?php echo JText::_('LNG_CLAIM_COMPANY') ?></p>
    			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
    		</div>
    		<div class="jmodal-body listing-contact-header">  
                
                <!-- First Step -->
                <div class="form-step-1">
                    <div class="row">
                        <div class="col-12">
                            <p class="head-text font-weight-bold mb-4"><?php echo JText::_('LNG_CLAIMED_COMPANY') ?></p>:</p>
                            <div class="d-flex">
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
                                            <div class="header-reviews-count"><?php echo count($this->totalReviews) . " " .((count($this->totalReviews)>1)? JText::_("LNG_REVIEWS"):JText::_("LNG_REVIEW")); ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="">      
                                <?php echo JText::_('LNG_CLAIM_COMPANY_DESCRIPTION') ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Step -->
		        <div class="form-step-2" style="display:none">
                    <?php if(!empty($this->companyContactsEmail)){?>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="">
                                    <label for="jinput-cnt"><?php echo JText::_('LNG_COMPANY_CONTACT') ?></label>
                                    <select name="contact_id" id="jinput-cnt" class="inputbox" required="">
                                        <option value=""><?php echo JText::_('LNG_GENERAL');?></option>
                                        <?php echo JHtml::_('select.options', $this->companyContactsEmail, 'id', 'contact_name');?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="">
                                <input class="validate[required]" id="jinput-fn" name="firstName" type="text" value="<?php echo $user->ID>0?$firstName:""?>" required="" >
                                <label for="jinput-fn"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="">
                                <input class="validate[required]" id="lastName" type="text" name="lastName" value="<?php echo $user->ID>0?$lastName:""?>" required="">
                                <label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label> 
                            </div>
                        </div>
                    </div>	
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <input type="text" name="email" id="jinput-email" class="validate[required,custom[email]]" value="<?php echo $user->ID>0?$user->email:""?>" required="">
                                <label for="jinput-email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                            </div>
                        </div> 
                    </div> 
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <input type="text" name="phone" id="jinput-phone" class="validate[required]" value="" required="">
                                <label for="jinput-phone"><?php echo JText::_('LNG_PHONE') ?></label>
                            </div>
                        </div> 
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="jbd-checkbox justify-content-end">
                                <label for="claim-company-agreament"><?php echo JText::_('LNG_COMPANY_CLAIM_DECLARATION')?></label>
                                <input type="checkbox" name="claim-company-agreament" id="claim-company-agreament" value="1" class="validate[required]"> 
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>
                        </div>
                    </div>
            
                    <?php if($this->appSettings->captcha){?>
                        <div class="form-item">
                            <?php 
                                $namespace="jbusinessdirectory.contact";
                                $class=" required";
                                
                                $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
                                                                    
                                if(!empty($captcha)){	
                                    echo $captcha->display("captcha", "captcha-div-contact", $class);
                                }
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
    		<div class="jmodal-footer">
    			<div class="btn-group" role="group" aria-label="">                
                    <div class="btn-step-1">
                        <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close();"><?php echo JText::_("LNG_CANCEL")?></button>
                        <button type="button" class="jmodal-btn jbd-commit jbd-btn-next"><?php echo JText::_("LNG_NEXT")?></button> 
                    </div>
                    <div class="btn-step-2" style="display:none">
                        <button type="button" class="jmodal-btn jmodal-btn-outline jbd-btn-back"><?php echo JText::_("LNG_BACK")?></button>
                        <button type="button" class="jmodal-btn jbd-commit" onclick="jbdUtils.saveForm('contactCompanyFrm')"><?php echo JText::_("LNG_SEND")?></button>
                    </div>    	 			 
                </div>
    		</div>
    	</div>
    	
    	<?php echo JHTML::_( 'form.token' ); ?>
		<input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
		<input type='hidden' name='task' id="task" value='companies.contactCompany'/>
		<input type='hidden' name='userId' value='<?php echo $user->ID?>'/>
		<input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
	</form>
</div>
<?php } ?>


<?php if($showData && (isset($this->package->features) && in_array(CONTACT_FORM, $this->package->features) || !$appSettings->enable_packages)
    && !empty($this->company->email) && $appSettings->enable_request_quote){ ?>
    <div id="company-quote" class="jbd-container" style="display:none">
        <form id="quoteCompanyFrm" name="quoteCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
            <div class="jmodal-sm">
                <div class="jmodal-header">
                    <div style="width: 95%">
                        <p class="jmodal-header-title"><?php echo JText::_('LNG_QUOTE_COMPANY') ?></p>
                    </div>
                    <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                </div>

                <div class="jmodal-body">
                    <p class="jmodal-body-text"> 
                        <?php echo JText::_('LNG_COMPANY_QUTE_TEXT') ?>
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="firstName-quote" name="firstName" type="text" value="" required="" >
                                <label for="firstName-quote"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="lastName-quote" type="text" name="lastName" value="" required="">
                                <label for="lastName-quote"><?php echo JText::_('LNG_LAST_NAME') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="email" id="email-quote" class="validate[required,custom[email]]" required="">
                                <label for="email-quote"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="phone" id="phone-quote" class="validate[required]" required="">
                                <label for="phone-quote"><?php echo JText::_('LNG_PHONE') ?></label>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <select name="category" id="category">
                                    <option value="0"><?php echo JText::_("LNG_ALL_CATEGORIES") ?></option>
                                    <?php echo JHtml::_('select.options', $this->categoryOptions, 'text', 'text', null);?>
                                </select>
                            </div>
                        </div>
                    </div> -->

                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <textarea rows="5" name="description" id="description-quote" cols="50" class="form-control validate[required]" required=""></textarea>
                                <label for="description-quote"><?php echo JText::_('LNG_CONTACT_TEXT')?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="jbd-checkbox justify-content-end">
                                <label for="age-agreement"><?php echo JText::_('LNG_AGE_USER_AGREEMENT')?></label>
                                <input type="checkbox"  name="company-quote-age-agreement" id="company-quote-age-agreement" value="1" class="validate[required]">
                            </div>                        
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php echo JBusinessUtil::renderTermsAndConditions(); ?>
                        </div>
                    </div>  

                    <?php if($this->appSettings->captcha){?>
                        <div class="form-item">
                            <?php
                            $namespace="jbusinessdirectory.contact.quote";
                            $class=" required";

                            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                            if(!empty($captcha)){
                                echo $captcha->display("captcha", "captcha-div-quote", $class);
                            }
                            ?>

                        </div>
                    <?php } ?>

                    <?php echo JHTML::_( 'form.token' ); ?>
                    <input type='hidden' name='task' id="task" value='companies.contactCompany'/>
                    <input type='hidden' name='userId' value=''/>
                    <input type="hidden" id="companyId" name="companyId" value="" />
                </div>
                <div class="jmodal-footer">
                    <div class="btn-group" role="group" aria-label="">
                        <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                        <button type="button" class="jmodal-btn jbd-commit" onclick="jbdListings.requestQuoteCompany()"><?php echo JText::_("LNG_REQUEST_QUOTE")?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } ?>

<div id="login-notice" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INFO') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p>
                <?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
            </p>
            <p>
                <a href="<?php echo JBusinessUtil::getLoginUrl($url); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
            </p>
		</div>
	</div>
</div>

<?php if($appSettings->enable_reporting){?>
    <div id="reportAbuseEmail" class="jbd-container" style="display:none">
        <form id="report-listing" name="report-listing" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
            <div class="jmodal-sm">
                <div class="jmodal-header">
                    <p class="jmodal-header-title"><?php echo JText::_('LNG_REPORT_ABUSE') ?></p>
                    <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                </div>

                <div class="jmodal-body">
                    <p>
                        <?php echo JText::_('LNG_REPORT_ABUSE_EXPLANATION') ?>
                    </p>
                        <div class="row">
                            <div class="col-12">
                                <div class="jinput-outline">
                                    <label for="jinput-cnt"><?php echo JText::_('LNG_CAUSE_REPORT') ?></label>
                                    <div class="outer_input">
                                        <div class="radio radio-show">
                                            <label><input style="width: unset" type="radio" name="report-cause" value="Outdated Information" checked> <?php echo JText::_('LNG_OUTDATED_INFORMATION') ?></label>
                                        </div>
                                        <div class="radio radio-show">
                                            <label><input style="width: unset" type="radio" name="report-cause" value="Offensive Material"> <?php echo JText::_('LNG_OFFENSIVE_MATERIAL') ?></label>
                                        </div>
                                        <div class="radio radio-show">
                                            <label><input style="width: unset" type="radio" name="report-cause" value="Inaccurate/Incorrect Information"> <?php echo JText::_('LNG_INCORRECT_INFORMATION') ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="jinput-outline jinput-hover">
                                    <input type="text" name="reporterEmail" id="jinput-reporterEmail" class="validate[required,custom[email]]" value="<?php echo $user->ID>0?$user->email:"" ?>" required="">
                                    <label for="jinput-reporterEmail"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="jinput-outline jinput-hover">
                                    <textarea rows="5" name="abuseMessage" id="abuseMessage" cols="50" class="form-control validate[required]" required=""></textarea>
                                    <label for="abuseMessage"><?php echo JText::_('LNG_MESSAGE')?>:</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>   
                            </div>
                        </div>

                        <?php if($this->appSettings->captcha){?>
                            <div class="form-item">
                                <?php
                                        $namespace="jbusinessdirectory.contact";
                                        $class=" required";

                                        $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                                        if(!empty($captcha)){
                                            echo $captcha->display("captcha", "captcha-div-report", $class);
                                        }
                                    ?>
                            </div>
                        <?php } ?>

                        <?php echo JHTML::_( 'form.token' ); ?>
                        <input type='hidden' name="option" value="com_jbusinessdirectory"/>
                        <input type='hidden' name='task' value='companies.reportListing'/>
                        <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />

                </div>
                <div class="jmodal-footer">
                    <div class="btn-group" role="group" aria-label="">
                        <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                        <button type="button" class="jmodal-btn jbd-commit" onclick="jbdUtils.saveForm('report-listing')"><?php echo JText::_("LNG_SEND")?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } ?>

<div id="listing-login-warning" class="listing-login-warning" style="display: none">
	<span><i class="la la-info"></i><?php echo JText::_('LNG_LOGIN_TO_VIEW_ALL');?></span>
</div>

<?php if($user->ID>0) { ?>
    <div id="company-list" class="jbd-container" style="display:none">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_SELECT_COMPANIES') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <div class="dialogContentBody" style="padding-bottom:30px;" id="dialogContentBody">
                    <p><?php echo JText::_('LNG_SELECT_COMPANIES_TO_ASSOCIATE_WITH_COMPANY'); ?></p>
                    <select name="associatedCompanies[]" id="userAssociatedCompanies" multiple
                            title="<?php echo JText::_('LNG_JOPTION_SELECT_COMPANY'); ?>"
                            class="chosen-select validate[required]">
						<?php echo JHtml::_('select.options', $this->userCompanies, 'id', 'name', $this->joinedCompanies); ?>
                    </select>
                </div>
            </div>
            <div class="jmodal-footer associated-buttons">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline"
                            onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL") ?></button>
                    <button type="submit" class="jmodal-btn jbd-commit-associated"
                            onclick="jbdListings.joinCompany(<?php echo $this->company->id ?>,true)"><?php echo JText::_("LNG_SUBMIT") ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php require_once JPATH_COMPONENT_SITE.'/include/bookmark_utils.php'; ?>

<script>
        
        window.addEventListener("load", function () {  

        jbdUtils.renderContactFormSteps();
        jbdUtils.renderClaimFormSteps();

        <?php if($this->appSettings->enable_ratings) { ?>
        jbdListings.renderAverageRating(<?php echo $this->company->review_score ?>);
            //renderUserRating(<?php echo isset($this->rating->rating) ? $this->rating->rating : '0' ?>, <?php echo $showNotice ?>, '<?php echo $this->company->id ?>');
        <?php } ?>
        jbdUtils.renderReviewAverageRating(<?php echo $this->company->review_score ?>);
        jbdListings.renderReviewRating();

        <?php if(!$showData) { ?>
        	jQuery("#listing-login-warning").fadeIn(500);
            setTimeout(function(){jQuery("#listing-login-warning").hide(500)},5000);
	  	<?php } ?>    
        
    });
</script>
