<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="company-contact" class="jbd-container" style="display: none">
    <?php
	if ($user->ID > 0) {
		$userNameDetails = explode(' ', $user->name);
		$firstName = $userNameDetails[0];
		$lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
	}
	?>
	<form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
    	<div class="jmodal-sm">
    		<div class="jmodal-header">
    			<p class="jmodal-header-title"><?php echo JText::_('LNG_CONTACT_COMPANY') ?></p>
    			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
    		</div>
    		<div class="jmodal-body">
				<p>
					<?php echo JText::_('LNG_COMPANY_CONTACT_TEXT') ?>
				</p>
		
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
            				<textarea rows="5" name="description" id="description" class="form-control validate[required]" required=""></textarea>
    						<label><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
        				</div>
    				</div>
    			</div>        		
        		
        		<div class="row">
					<div class="col-12">
                		<div class="jbd-checkbox justify-content-end">
                			<label for="terms-conditions"><?php echo JText::_('LNG_TERMS_AGREAMENT')?> (<a href="javascript:void(0)" id="terms-conditions-link"><?php echo JText::_('LNG_VIEW')?></a>)</label>
    						<input type="checkbox"  name="terms-conditions" id="terms-conditions" value="1" class="validate[required]">
    					</div>
                    <?php if ($this->appSettings->show_privacy == 1) { ?>
                        <div id="privacy_policy" class="jbd-checkbox justify-content-end mb-3">
                            <label for="accept_privacy"><?php echo JText::_('LNG_POLICY_AGREEMENT')?> (<a href="javascript:void(0)" id="company-privacy-policy-link"><?php echo JText::_('LNG_VIEW')?></a>)</label>
                            <input type="checkbox"  name="accept_privacy" id="accept_privacy" value="1" class="validate[required]">
                        </div>
                    <?php } ?>
					</div>
        		</div>
	
				<div id="term_conditions_text" style="display: none;">
					<?php echo $this->appSettings->contact_terms_conditions ?>
				</div>

                <div id="company_privacy_policy_text" style="display: none;">
                    <h5><?php echo JText::_('LNG_PRIVACY_POLICY')?></h5>
                    <?php echo $this->appSettings->privacy_policy ?>        
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
    		<div class="jmodal-footer">
    			<div class="btn-group" role="group" aria-label="">
    				<button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="button" class="jmodal-btn contact-submit-button" onclick="jbdListings.contactCompanyList('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>')"><?php echo JText::_("LNG_CONTACT_COMPANY")?></button>
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
                            <select name="category" id="category">
                                <option value="0"><?php echo JText::_("LNG_ALL_CATEGORIES") ?></option>
                                <?php echo JHtml::_('select.options', $this->categoryOptions, 'text', 'text', null);?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <textarea rows="5" name="description" id="description-quote" cols="50" class="form-control validate[required]" required=""></textarea>
                            <label><?php echo JText::_('LNG_CONTACT_TEXT')?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jbd-checkbox justify-content-end">
                            <label for="terms-conditions"><?php echo JText::_('LNG_TERMS_AGREAMENT')?> (<a href="javascript:void(0)" id="company-quote-terms-conditions-link"><?php echo JText::_('LNG_VIEW')?></a>)</label>
                            <input type="checkbox"  name="company-quote-terms-conditions" id="company-quote-terms-conditions" value="1" class="validate[required]">
                        </div>
                        <?php if ($this->appSettings->show_privacy == 1) { ?>
                        <div id="privacy_policy" class="jbd-checkbox justify-content-end mb-3">
                            <label for="accept_privacy"><?php echo JText::_('LNG_POLICY_AGREEMENT')?> (<a href="javascript:void(0)" id="company-quote-privacy-policy-link"><?php echo JText::_('LNG_VIEW')?></a>)</label>
                                <input type="checkbox"  name="accept_privacy" id="accept_privacy" value="1" class="validate[required]">
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div id="company_quote_term_conditions_text" style="display: none;">
                <h5><?php echo JText::_('LNG_TERMS_AND_CONDITIONS')?></h5>
                    <?php echo $this->appSettings->terms_conditions ?>
                </div>
                <div id="company_quote_privacy_policy_text" style="display: none;">
                    <h5><?php echo JText::_('LNG_PRIVACY_POLICY')?></h5>
                    <?php echo $this->appSettings->privacy_policy ?>        
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
    window.addEventListener("load", function () {
        jQuery("#terms-conditions-link").click(function () {
            jQuery("#term_conditions_text").toggle();
        });

        jQuery("#company-quote-terms-conditions-link").click(function () {
            jQuery("#company_quote_term_conditions_text").toggle();
        });

        jQuery("#company-privacy-policy-link").click(function () {
            jQuery("#company_privacy_policy_text").toggle();
        });

        jQuery("#company-quote-privacy-policy-link").click(function () {
            jQuery("#company_quote_privacy_policy_text").toggle();
        });
    });

    var contactListUrl = '<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>';

</script>