<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$app = JFactory::getApplication();
$user = JBusinessUtil::getUser();

$menuItemId = JBusinessUtil::getActiveMenuItem();

$company = $this->company;
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

//set metainfo
$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();

$appSettings = JBusinessUtil::getApplicationSettings();

$title = JText::_('LNG_CLAIM_COMPANY')." ".stripslashes($company->name)." - ".$config->sitename;
$description = stripslashes($company->name)." - ".JText::_('LNG_CLAIM_COMPANY_TEXT');
$keywords = "";

JBusinessUtil::setMetaData($title, $description, $keywords, false);
JBusinessUtil::setFacebookMetaData($title, $description, $this->company->logoLocation, $url);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task)
	{	
		JBD.submitform(task, document.getElementById('claimCompanyFrm'));
	}
});
</script>

<div class="jbd-container">	
     <fieldset class="boxed auto">
        <form id="claimCompanyFrm" name="claimCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post">
    		<p>
    			<?php echo JText::_('LNG_COMPANY_CLAIM_TEXT') ?>
    		</p>
    		<div class="form-container">
        		<fieldset>
        			<div class="form-group">
        				<label><?php echo JText::_('LNG_FIRST_NAME') ?></label>
        				<div class="outer_input">
        					<input type="text" name="firstName" id="firstName-claim" class="form-control validate[required]" value="<?php echo $user->ID>0?$user->name:""?>">
        				</div>
        			</div>
        
        			<div class="form-group">
        				<label><?php echo JText::_('LNG_LAST_NAME') ?></label>
        				<div class="outer_input">
        					<input type="text" name="lastName" id="lastName-claim" class="form-control  validate[required]" >
        				</div>
        			</div>
        
        			<div class="form-group">
        				<label><?php echo JText::_('LNG_PHONE') ?></label>
        				<div class="outer_input">
        					<input type="text" name="phone" id="phone-claim" class="form-control  validate[required]">
        				</div>
        			</div>
        
        			<div class="form-group">
        				<label><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
        				<div class="outer_input">
        					<input type="text" name="email" id="email-claim" class="form-control  validate[required,custom[email]]" <?php echo $user->ID>0?$user->email:""?>>
        				</div>
        			</div>
        
        			<div class="form-group">
        				<div class="jbd-checkbox justify-content-end">
                            <label for="claim-company-agreament"><?php echo JText::_('LNG_COMPANY_CLAIM_DECLARATION')?></label>
                            <input type="checkbox" name="claim-company-agreament" id="claim-company-agreament" value="1" class="validate[required]"> 
                        </div>
        			</div>
        
        			<div class="form-group">
						<?php echo JBusinessUtil::renderTermsAndConditions(); ?>
        			</div>
        			
        			<?php if($this->appSettings->captcha){?>
        				<div class="form-group">
        					<?php 
        						$namespace="jbusinessdirectory.contact";
        						$class=" required";
        						
        						$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));
        															
        						if(!empty($captcha)){	
        							echo $captcha->display("captcha", "captcha-div-claim", $class);
        						}
        					?>
        				</div>
        			<?php } ?>
        
        			<div class="clearfix clear-left">
        				<div class="pt-3">
        					<button type="submit" class="btn btn-success jbd-commit" onclick="jbdUtils.saveForm('claimCompanyFrm')">
        							<span class="ui-button-text"><?php echo JText::_("LNG_CLAIM_COMPANY")?></span>
        					</button>
        					<button type="button" class="btn btn-dark" onclick="JBD.submitbutton('claimlisting.cancel');">
        							<span class="ui-button-text"><?php echo JText::_("LNG_CANCEL")?></span>
        					</button>
        				</div>
        			</div>
        		</fieldset>
    		</div>
    		
    		<?php echo JHTML::_( 'form.token' ); ?>
    		<input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
    		<input type='hidden' name='userId' value='<?php echo $user->ID?>'/>
    		<input type="hidden" name="claim_listing_id" value="<?php echo $this->claimListing?>" />
    		<input type='hidden' name="task" id="task" value="claimlisting.claimListing"/>
    	</form>
    </fieldset>
</div>

<script>
	window.addEventListener("load", function () {
        jQuery("#agreementLink").click(function () {
            jQuery("#termAgreement").toggle();
        });
	});
</script>
