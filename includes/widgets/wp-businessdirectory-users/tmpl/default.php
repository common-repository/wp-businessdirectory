<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0.en.html; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('UsersHelperRoute', JPATH_SITE . '/components/com_users/helpers/route.php');

JHtml::_('behavior.keepalive');
$user = JBusinessUtil::getUser();
$createView = $appSettings->enable_packages?"packages":"managecompany";
global $wp;
$currentUrl = home_url();

?>

<div class="jbd-mod-user">
	<?php if(!$user->ID){ ?>
    <div class="user-area signin-area">
		<i class="fa fa-user"></i>
        <a id="jbd-user-login-btn" href="#"><?php echo JText::_("MOD_JBUSINESS_USER_SIGN_IN")?></a>
        <?php if ($params->get('showregistration')) { ?>
			<span><?php echo JText::_("MOD_JBUSINESS_USER_OR")?></span>
			<a id="jbd-user-registration-btn" href=""><?php echo JText::_("MOD_JBUSINESS_USER_REGISTER")?></a>
		<?php } ?>
    </div>
	<?php }else{ ?>
    	<div class="jbd-user-menu">
			<ul class="user-menu menu-animation-fade-up">
				<li class="menu-item has-child"><a href="#" class="jbd-username"><i class="la la-user"></i> <span class="jbd-user-name"><?php echo $user->name?></span></a>
					<div class="dropdown dropdown-main menu-right">
        				<div class="dropdown-inner">
            				<ul class="dropdown-items">
            					<li class="menu-item">
            						<a href="<?php echo $dashboardLink ?>"><?php echo JText::_("MOD_JBUSINESS_USER_DASHBOARD")?></a>
            					</li>
            					<li class="menu-item">
            						<a href="<?php echo admin_url( 'profile.php' )   ?>"><?php echo JText::_("MOD_JBUSINESS_USER_PROFILE")?></a>
            					</li>
            					<li class="menu-item">
									<a href="<?php echo wp_logout_url($currentUrl)?>"><?php echo JText::_("MOD_JBUSINESS_USER_LOGOUT")?></a>
            					</li>
            				</ul>
            			</div>
            		</div>
        		</li>
        	</ul>
    	</div>
   <?php } ?>
   
   <?php if ($params->get('showcart')) { ?>
       <div class="jbd-view-cart">
       	   <a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=cart")?>" title="View your shopping cart">
    	   	   <i class="fa fa-shopping-basket">
    	   	   <?php if(!empty($cartItemsCount)){?>
    	   	   	<span class="cart-badge"><?php echo $cartItemsCount?></span>
    	   	   <?php } ?>
    	   	   </i>
       	   </a>
       </div>		
   <?php } ?>							
   
   <?php if ($params->get('showcreatelisting')) { ?>
       <div class="jbd-button-container">
       	  <a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=$createView") ?>" class="button-create">
          	<i class="fa fa-plus"></i> <?php echo JText::_("MOD_JBUSINESS_USER_ADD_LISTING")?>
          </a>
       </div>
   <?php } ?>		
    <div id="jbd-user-login" class="jbd-container" style="display:none"> 
		<form action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post" id="loginform" name="loginform" class="form-inline">
			<input type="hidden" name="action" value="login" />
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($currentUrl); ?>" />
        	<div class="jmodal-sm">
        		<div class="jmodal-header">
        			<p class="jmodal-header-title"><?php echo JText::_("MOD_JBUSINESS_USER_SIGN_IN") ?></p>
        			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        		</div>
        		<div class="jmodal-body">
            		<?php if ($params->get('pretext')){ ?>
            			<div class="pretext">
            				<p><?php echo $params->get('pretext'); ?></p>
            			</div>
            		<?php } ?>
            		
            		<div class="row">
						<div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="jinput-display_name" name="log" type="text" required="" >
                                <label for="jinput-display_name"><?php echo JText::_('MOD_JBUSINESS_USER_VALUE_USERNAME') ?></label>
                            </div>
                        </div>
    				</div>	
            		<div class="row">
						<div class="col-md-12">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" type="password" name="pwd" id="modlgn-passwd" required="">
                                <label for="modlgn-passwd"><?php echo JText::_('MOD_JBUSINESS_USER_PASSWORD') ?></label>
                            </div>
                        </div>
    				</div>	
                    <div class="row">
                        <div class="col-md-12">
                            <div class="jbd-checkbox justify-content-end">
                            </div>
                        </div>
                    </div>

            		<div class="row">
    					<div class="col-md-6">
							<div class="jbd-checkbox justify-content-end">
								<label for="modlgn-remember"><?php echo JText::_('MOD_JBUSINESS_USER_REMEMBER_ME')?></label>
								<input type="checkbox" name="remember" id="modlgn-remember" value="1" value="yes" /> 
							</div>
    					</div>
                        <div class="col-md-6 text-right">
                            <a href="<?php echo wp_lostpassword_url() ?>">
						        <?php echo JText::_('MOD_JBUSINESS_USER_FORGOT_YOUR_PASSWORD'); ?></a>
                        </div>           			
               		</div>
               		
               		<div class="row">
               			<div class="col-md-12 text-center my-3">
    						<button type="submit" tabindex="0" name="Submit" class="btn btn-primary login-button"><?php echo JText::_('JLOGIN'); ?></button>
    					</div>
    				</div>

    				<?php if ($params->get('showregistration')) { ?>
        				<div class="row">
                   			<div class="col-md-12 text-center">
                         		<?php echo JText::_('MOD_JBUSINESS_USER_NOT_MEMBER'); ?><a href="#jbd-user-registration" rel="modal:open">
                				<?php echo JText::_('MOD_JBUSINESS_USER_REGISTER_HERE'); ?> </a>
                        	</div>
                        </div>
                    <?php } ?>
                    
                    <?php if ($params->get('socialloging')) { ?>
                    <div class="row">
               			<div class="col-md-12 text-center">
               				<div class="social-network-wrapper">
               					<span><?php echo JText::_("MOD_JBUSINESS_USER_FASTER_LOGIN")?></span>
               					<div class="social-login-button">
                       				<div class="row d-flex justify-content-center align-content-center"> 
										<?php if (!empty($appSettings->google_client_id) && !empty($appSettings->google_client_secret)) { ?>
											<div class="col-md-4">
												<a class="social-btn jbd-google-signin" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=userprofile.oauthCallback&type=google&redirect='.$currentUrl) ?>">
													<i class="la la-google"></i>
													<span><?php echo JText::_("MOD_JBUSINESS_USER_SIGN_GOOGLE")?></span>
												</a>
											</div>
										<?php } ?>
										<?php if (!empty($appSettings->facebook_client_id) && !empty($appSettings->facebook_client_secret)) { ?>
											<div class="col-md-4">
												<a class="social-btn jbd-facebook-signin" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=userprofile.oauthCallback&type=facebook&redirect='.$currentUrl) ?>">
													<i class="la la-facebook-official"></i>
													<?php echo JText::_("MOD_JBUSINESS_USER_SIGN_FACEBOOK")?>
												</a>
											</div>
										<?php } ?>
										<?php if (!empty($appSettings->linkedin_client_id) && !empty($appSettings->linkedin_client_secret)) { ?>
											<div class="col-md-4">
												<a class="social-btn jbd-linkedin-signin" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=userprofile.oauthCallback&type=linkedin&redirect='.$currentUrl) ?>">
													<i class="la la-linkedin"></i>
													<?php echo JText::_("MOD_JBUSINESS_USER_SIGN_LINKEDIN")?>
												</a>
											</div>
										<?php } ?>
                       				</div>
                   				</div>
                   			</div>
               			</div>
               		</div>
               		<?php } ?>
               		
               		<?php if ($params->get('posttext')) { ?>
            			<div class="posttext">
            				<p><?php echo $params->get('posttext'); ?></p>
            			</div>
					<?php } ?>
               		
        			<input type="hidden" name="option" value="com_users" />
        			<input type="hidden" name="task" value="user.login" />
        			<input type="hidden" name="return" value="<?php echo $return; ?>" />
        			<?php echo JHtml::_('form.token'); ?>
        		</div>
        	</div>	
    	</form>
    </div>
	
	<div id="jbd-user-registration" class="jbd-container" style="display:none">
		<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=businessuser.addUser'); ?>" method="post" class="form-validate form-horizontal well" enctype="multipart/form-data"> 
    		<div class="jmodal-sm">
        		<div class="jmodal-header">
        			<p class="jmodal-header-title"><?php echo JText::_("MOD_JBUSINESS_USER_REGISTRATION") ?></p>
        			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        		</div>
        		<div class="jmodal-body">
					<div class="row">
						<div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="firstname" name="firstname" type="text" required="" >
                                <label for="firstname"><?php echo JText::_('MOD_JBUSINESS_USER_FIRST_NAME') ?></label>
                            </div>
                        </div>
						<div class="col-md">
                            <div class="jinput-outline jinput-hover">
                                <input class="validate[required]" id="lastname" name="lastname" type="text" required="" >
                                <label for="lastname"><?php echo JText::_('MOD_JBUSINESS_USER_LAST_NAME') ?></label>
                            </div>
                        </div>
    				</div>	
					<div class="row">
    					<div class="col-md-12">
    						<div class="jinput-outline jinput-hover">
                				<input class="validate[required]" id="username" name="username" type="text"  required=""> 
                				<label for="username"><?php echo JText::_('MOD_JBUSINESS_USER_USERNAME') ?></label>
								<p class="usernameWarning" style="color:red"><p>
                    		</div>
    					</div>
    				</div>
    				
    				<div class="row">
    					<div class="col-md-12">
    						<div class="jinput-outline jinput-hover">
                				<input class="validate[required,minSize[6]]" id="user_password" name="password" type="password" required="" > 
                				<label for="user_password"><?php echo JText::_('MOD_JBUSINESS_USER_PASSWORD') ?></label>
                    		</div>
    					</div>
    				</div>
    				
    				<div class="row">
    					<div class="col-md-12">
    						<div class="jinput-outline jinput-hover">
                				<input class="validate[required,custom[email]]" id="user_email" name="user_email" type="text" required=""> 
                				<label for="user_email"><?php echo JText::_('MOD_JBUSINESS_USER_EMAIL') ?></label>
								<p class="emailWarning" style="color:red"><p>
                    		</div>
    					</div>
					</div>

					<?php if ($params->get('showbusinessownercheck')) { ?>
						<div class="row">
							<div class="col-md-12">
								<div class="jbd-checkbox justify-content-end">
									<label for="business_owner"><?php echo JText::_('MOD_JBUSINESS_USER_I_AM_BUSINESS_OWNER')?> </label>
									<input type="checkbox" name="business_owner" id="business_owner" value="1">
								</div>
							</div>
						</div>
					<?php } ?>

                    <div class="row">
                        <div class="col-md-12">
							<?php echo JBusinessUtil::renderTermsAndConditions(); ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                        	<?php 
                        		if($appSettings->captcha){
        							$namespace="registration";
        							$class=" required";
        							
        							$captcha = JCaptcha::getInstance("recaptcha");
        																
        							if(!empty($captcha)){	
        								echo $captcha->display("captcha", "captcha-div-registration", $class);
        							}
	        					} 
	        				?>
            			</div>
            		</div>

    				<div class="row">
    					<div class="col-md-12 text-center my-3">
    						<button type="submit" class="btn btn-primary validate">
            					<?php echo JText::_('JREGISTER'); ?>
            				</button>
            			</div>
            		</div>
            	
            		<div class="jbd-user-has-account">
    					<?php echo JText::_('MOD_JBUSINESS_USER_HAVE_ACCOUNT'); ?> <a href="#jbd-user-login" rel="modal:open"><?php echo JText::_('MOD_JBUSINESS_USER_LOGIN'); ?></a>
    				</div>
    			</div>
				<?php echo JHtml::_('form.token'); ?>	
			</div>
		</form>
	</div>

</div>

<script>

	
	window.addEventListener('load', function(){
		jQuery("#member-registration").validationEngine('attach');

			jQuery("#jbd-user-login-btn").click(function(e) {
			e.preventDefault();
			jQuery("#jbd-user-login").jbdModal({
				clickClose: false
			});
		}); 

		jQuery("#jbd-user-registration-btn").click(function(e) {
			e.preventDefault();
			jQuery("#jbd-user-registration").jbdModal({
				clickClose: false
			});
		}); 

	});

	function checkUserByUsername(){
        var username = jQuery('#jform_username').val();
        let url = jbdUtils.getAjaxUrl('checkUserByUsernameAjax', 'businessuser');
		if(username){
            jQuery.ajax({
                url: url,
                dataType: 'json',
                data: {username: username},
                success: function(data) {
                    jQuery('#availability').html(data.message);
                }
            })
        }
	}

</script>