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
<div id="company-contact" class="jbd-container jbd-edit-container" style="display: none">
    <?php
	if ($user->ID > 0) {
		$userNameDetails = explode(' ', $user->name);
		$firstName = $userNameDetails[0];
		$lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
	}
	?>
	<form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
    	<div class="jmodal-sm">
    		<div class="jmodal-header  listing-contact-header">
                <div class="jmodal-header-background">
                    <div class="dir-overlay"></div>
                </div>
    			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                <div class="jmodal-header-contact">
                    <div class="item-header-photo">
                        <img src="" alt="">
                    </div>
                    <div class="item-header-content mb-0">
                        <p class="head-text"><?php echo JText::_('LNG_REQUEST_INFO_FROM') ?></p>
                        <div class="item-header-title">
                        </div>
                        <?php if ($this->appSettings->enable_ratings) { ?>
                            <div class="rating d-flex align-items-center">
                                <span class="rating-average-review" id="rating-average-review" title="" alt="" style="display: block;"></span>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="firstName" name="firstName" type="text" value="<?php echo $user->ID>0?$firstName:""?>" required="" >
                                <label for="firstName"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="lastName" type="text" name="lastName" value="<?php echo $user->ID>0?$lastName:""?>" required="">
                                <label for="lastName"><?php echo JText::_('LNG_LAST_NAME') ?></label> 
                            </div>
                        </div>
                    </div>	
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="email" id="email" class="validate[required,custom[email]]" value="<?php echo $user->ID>0?$user->email:""?>" required="">
                                <label for="email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="phone" id="phone" class="validate[required]" value="" required="">
                                <label for="phone"><?php echo JText::_('LNG_PHONE') ?></label>
                            </div>
                        </div> 
                    </div>  	
                    
                    <div class="row">
                        <div class="col-12">
                            <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>
                        </div>
                    </div>           
            
                    <?php if ($this->appSettings->captcha) {?>
                        <div class="form-item">
                            <?php
                                $namespace="jbusinessdirectory.contact";
                                $class=" required";
                                
                                $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
                                                                    
                                if (!empty($captcha)) {
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
                        <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                        <button type="button" class="jmodal-btn jbd-commit jbd-btn-next" disabled><?php echo JText::_("LNG_NEXT")?></button>                                
                    </div>
                    <div class="btn-step-2" style="display:none">
                        <button type="button" class="jmodal-btn jmodal-btn-outline jbd-btn-back" ><?php echo JText::_("LNG_BACK")?></button>
                        <button type="button" class="jmodal-btn contact-submit-button" onclick="jbdListings.contactCompanyList('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>')"><?php echo JText::_("LNG_SEND")?></button>
                    </div>
                </div>
            </div>
        </div>    	

		<?php echo JHTML::_('form.token'); ?>
		<input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
		<input type='hidden' name='task' id="contact_company_task" value='companies.contactCompany'/>
		<input type='hidden' name='userId' value=''/>
		<input type="hidden" id="companyId" name="companyId" value="" />
	</form>
</div>

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
                <p>
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

                <?php if ($this->appSettings->captcha) {?>
                    <div class="form-item">
                        <?php
						$namespace="jbusinessdirectory.contact";
						$class=" required";

						$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

						if (!empty($captcha)) {
							echo $captcha->display("captcha", "captcha-div-quote", $class);
						}
						?>

                    </div>
                <?php } ?>

                <?php echo JHTML::_('form.token'); ?>
                <input type='hidden' name='task' id="task" value='companies.contactCompany'/>
                <input type='hidden' name='userId' value=''/>
                <input type="hidden" id="companyId" name="companyId" value="" />
            </div>
            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="button" class="jmodal-btn quote-submit-button" onclick="jbdListings.requestQuoteCompany()"><?php echo JText::_("LNG_REQUEST_QUOTE")?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script> 
    window.addEventListener("load", function() {
        jbdUtils.renderContactFormSteps();  
    });    
    var contactListUrl = '<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>';
</script>