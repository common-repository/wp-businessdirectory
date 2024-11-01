<?php 
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');

//$language = JFactory::getLanguage();
//$language->load('com_users');

$showOnlyLogin = JFactory::getApplication()->input->get('showOnlyLogin');
$showRegistration = empty($showOnlyLogin) && get_option( 'users_can_register' );
?>

<div class="jbd-container jbd-edit-container">
    <?php if(!empty($this->filter_package)){?>
        <?php echo JBusinessUtil::renderProcessSteps(2)?>
    <?php } ?>	
    
    <div id="user-details" class="user-details">
    	<div class="row">
        	<?php if ($showRegistration){ ?>
        		<div class="col-md-6 js-user-registration">
        			<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.addUser'); ?>" method="post" name="registration-form" id="registration-form" >
        				<fieldset>
        					<h3><?php echo JText::_("LNG_USER_REGISTRATION_DETAILS")?></h3>
        					<p>
        						<?php echo JText::_("LNG_USER_REGISTRATION_DETAILS_TXT")?>
        					</p>

							<div class="row">
								<div class="col-md">
									<div class="form-group">
										<label for="firstname"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
										<div class="outer_input">
											<input type="text" name="firstname" id="firstname" size="50" class="form-control validate[required]">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<label for="lastname"><?php echo JText::_('LNG_LAST_NAME') ?></label>
										<div class="outer_input">
											<input type="text" name="lastname" id="lastname" size="50" class="form-control validate[required]">
										</div>
									</div>
								</div>
							</div>

        					<div class="form-group">
        						<label for="username"><?php echo JText::_('LNG_USERNAME') ?></label>
        						<div class="outer_input">
        							<input type="text" name="username" id="username" size="50" onkeyup="jbdUtils.checkUserByUsername('username')" class="form-control validate[required]">        						
									<p class="display_nameWarning" style="color:red"><p>
								</div>
        					</div>
        					<div class="form-group">
        						<label for="email"><?php echo JText::_('LNG_EMAIL') ?></label>
        						<div class="outer_input">
        							<input type="text" name="email" id="email" size="50" onkeyup="jbdUtils.checkUserByEmail('email')" class="form-control validate[required,custom[email]]">
								    <p class="emailWarning" style="color:red"><p>
        						</div>
        					</div>
        					<div class="form-group">
        						<label for="password"><?php echo JText::_('LNG_PASSWORD') ?></label>
        						<div class="outer_input">
        							<input type="password" name="password" id="password" size="50" class="form-control validate[required,minSize[6]]">
        						</div>
        					</div>

							<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
								<?php if ($fieldset->name === 'captcha' && $this->captchaEnabled) : ?>
									<?php continue; ?>
								<?php endif; ?>
								<?php $fields = $this->form->getFieldset($fieldset->name); ?>
								<?php if (count($fields)) : ?>
									<fieldset>
										<?php // If the fieldset has a label set, display it as the legend. ?>
										<?php if (isset($fieldset->label) && false) : ?>
											<legend><?php echo JText::_($fieldset->label); ?></legend>
										<?php endif; ?>
										<?php echo $this->form->renderFieldset($fieldset->name); ?>
									</fieldset>
								<?php endif; ?>
							<?php endforeach; ?>


        					<?php if($this->appSettings->captcha){?>
        						<div class="form-group">
        							<?php 
        							$namespace="jbusinessdirectory.contact";
        							$class=" required";
        							
        							$captcha = JCaptcha::getInstance("recaptcha");
        																
        							if(!empty($captcha)){	
        								echo $captcha->display("captcha", "captcha-div-registration", $class);
        							}
        							?>
        						</div>
        					<?php } ?>
        					<div class="control-group">
								<div class="form-group">
									<div class="">
										<?php echo JText::_("LNG_ALREADY_HAVE_ACCOUNT")?> <a href="javascript:switchFrame('login')"><?php echo JText::_("LNG_SIGN_IN")?></a>
									</div>
								</div>
							</div>

        					<div class="control-group">
            					<div class="form-group">
									<?php echo JBusinessUtil::renderTermsAndConditions(); ?>
        						</div>		
        					</div>	
        							
        					<div class="control-group">
            					<div class="form-group">
            						<div class="controls">
            							<button class="btn btn-success" type="submit"><?php echo JText::_('LNG_CREATE_ACCOUNT') ?></button>
            						</div>
            					</div>		
        					</div>
                            <input type="hidden" name="serviceType" id="serviceType" value="<?php echo $this->serviceType ?>" />
                            <input type="hidden" name="filter_package" id="filter_package" value="<?php echo $this->filter_package ?>" />
                            <input type="hidden" name="packageType" id="packageType" value="<?php echo !empty($this->package)?$this->package->package_type:"" ?>" />
        					<input type="hidden" name="claim_listing_id" id="claim_listing_id" value="<?php echo $this->claimListing ?>" />
							<input type="hidden" name="editor_listing_id" id="editor_listing_id" value="<?php echo $this->editorListingId ?>" />
							<input type="hidden" name="orderId" id="orderId" value="<?php echo $this->orderId ?>" />
        					<?php echo JHtml::_('form.token'); ?>
        				</fieldset>
        			</form>
        		</div>
        	<?php } ?>
        	<div class="col-md-6  js-user-login" style="<?php echo $showRegistration?'display:none':''?>">
        		<div class="">
        			<h3><?php echo JText::_("LNG_LOGIN")?></h3>
        			<p>
        				<?php echo JText::_("LNG_ALREADY_HAVE_ACCOUNT_TXT")?>
        			</p>
        			
        			<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.loginUser'); ?>" method="post" id="form-login" name="login-form">
        				<fieldset>
            				<div class="control-group">
            					<div class="form-group">
            						<label for="display_name"><?php echo JText::_('LNG_USERNAME') ?></label>
            						<div class="outer_input">
            							<input type="text" name="display_name" id="display_name" size="50" class="form-control validate[required]">
            						</div>
            					</div>
            					<div class="form-group">
            						<label for="password"><?php echo JText::_('LNG_PASSWORD') ?></label>
            						<div class="outer_input">
            							<input type="password" name="password" id="password" size="50" class="form-control validate[required]">
            						</div>
            					</div>
        					</div>						
        					<div class="control-group">
        						<div class="controls">
        							<button class="btn btn-success" type="submit"><?php echo JText::_('LNG_LOG_IN') ?></button>
        						</div>
        					</div>		
        					
        					<div class="control-group small">
        						<div>
        							<a href="<?php echo wp_lostpassword_url() ?>">
        							<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
        						</div>

								<?php if($showRegistration){?>
									<div class="pt-2">
										<div><?php echo JText::_("COM_USERS_LOGIN_REGISTER") ?></div>
										<a href="javascript:switchFrame('register')"><?php echo JText::_("LNG_CREATE_NEW_ACCOUNT")?></a>
									</div>
								<?php } ?>
        					</div>
        				</fieldset>
						
        				<input type="hidden" name="filter_package" id="filter_package" value="<?php echo $this->filter_package ?>" />
                        <input type="hidden" name="claim_listing_id" id="claim_listing_id" value="<?php echo $this->claimListing ?>" />
						<input type="hidden" name="editor_listing_id" id="editor_listing_id" value="<?php echo $this->editorListingId ?>" />
						<input type="hidden" name="packageType" id="packageType" value="<?php echo !empty($this->package)?$this->package->package_type:"" ?>" />
                        <input type="hidden" name="serviceType" id="serviceType" value="<?php echo $this->serviceType ?>" />
						<input type="hidden" name="orderId" id="orderId" value="<?php echo $this->orderId ?>" />
        				<?php echo JHtml::_('form.token'); ?>
        			</form>
        		</div>
        	</div>
			<?php if(!empty($this->package)){?>
            	<div class="col-md-4 offset-md-2">
            		<div class="featured-product-col" >
            			<?php
            				$package = $this->package;
            				require  JPATH_COMPONENT_SITE."/views/packages/tmpl/default_package.php"
            			?>
            		</div>
            	</div>
        	<?php }?>
        </div>
    </div>
</div>
	
<script>

window.addEventListener('load', function(){
	jQuery("#registration-form").validationEngine('attach');
	jQuery("#form-login").validationEngine('attach');
});

function switchFrame(type){

	if(type=='login'){
		jQuery(".js-user-login").show();
		jQuery(".js-user-registration").hide();
	}else{
		jQuery(".js-user-login").hide();
		jQuery(".js-user-registration").show();

	}
}
</script>