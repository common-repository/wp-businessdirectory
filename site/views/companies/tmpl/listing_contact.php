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
<div class="jbd-container">
    <?php
	if ($user->ID > 0) {
		$userNameDetails = explode(' ', $user->name);
		$firstName = $userNameDetails[0];
		$lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
	}
	?>

    <div class="listing-contact" id="listing-contact" >	
     <strong><h4>Contact Business</h4></strong>
     <form id="contactListingFrm" name="contactListingFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
        <div class="form-head">
            <div class="form-head-column form-head-column--1">
                <div class="item-header-photo">
                    <img src="<?php echo !empty($company->logoLocation) ? BD_PICTURES_PATH.$company->logoLocation : BD_PICTURES_PATH.$appSettings->no_image ?>" alt="">
                </div>
            </div>
            <div class="form-head-column form-head-column--2">
                <h4><?php echo $company->name ?></h4>
                <?php if(!empty($company->email) && $showData && $appSettings->show_email) { ?>
                    <a  class="text-fluid" href="mailto:<?php echo $company->email ?>"><?php echo $company->email ?></a>
                <?php } ?>
            </div>
        </div>
        <div class="form-item">
            <input type="text" placeholder="<?php echo JText::_("LNG_NAME")?>" name="firstName" id="firstName" class="validate[required]">
        </div>

        <div class="form-item">
            <input type="text" placeholder="<?php echo JText::_("LNG_EMAIL")?>" name="email" id="email" class="validate[required,custom[email]]">
        </div>

        <div class="form-item">
            <input type="text" placeholder="<?php echo JText::_("LNG_PHONE")?>" name="phone" id="phone" class="validate[required]">
        </div>

        <div class="form-item">
            <textarea class="message-area validate[required]"  name="description" id="description" placeholder="<?php echo JText::_("LNG_YOUR_MESSAGE")?>"></textarea>
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

        <div class="form-submit">
            <button class="button-submit" type="button" onclick="jbdListings.contactBusinessListing('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>')"><span><?php echo JText::_("LNG_FINISHED")?></span></button>
        </div>

        <?php echo JHTML::_('form.token'); ?>
		<input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
		<input type='hidden' name='task' id="contact_company_task" value='companies.contactCompany'/>
		<input type='hidden' name='userId' value=''/>
		<input type="hidden" id="companyId" name="companyId" value="<?php echo $company->id ?>" />
        </form>    
    </div>
</div>


<script>
    var contactListUrl = '<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.contactCompanyAjax', false); ?>';

</script>