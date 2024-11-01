<?php 
    JBusinessUtil::includeValidation(); 
    $user = JBusinessUtil::getUser();
    if ($user->ID > 0) {
        $userNameDetails = explode(' ', $user->name);
        $firstName = $userNameDetails[0];
        $lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
    }
?>
<div id="company-contact" class="jbd-container" style="display: none">
	<form id="contactCompanyFrm" name="contactCompanyFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
    	<div class="jmodal-sm">
    		<div class="jmodal-header">
    			<p class="jmodal-header-title"><?php echo JText::_('LNG_CONTACT_COMPANY') ?></p>
    			<a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
    		</div>
    		<div class="jmodal-body">
				<!-- First Step -->
				<div class="form-step-1">
                    <div class="row">
                        <div class="col-12">
                        <p class="head-text font-weight-bold mb-4">To:</p>
                            <div class="d-flex">
                                <div class="item-header-photo">
                                    <img src="" alt="">
                                </div>
                                <div class="item-header-content">
                                    <div class="item-header-title"></div>
                                    <?php if ($appSettings->enable_ratings) { ?>
                                        <div class="rating d-flex align-items-center">
                                            <span class="rating-average-review" id="rating-average-review" title="" alt="" style="display: block;"></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
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
								<input class="validate[required]" id="jinput-fn" name="firstName" type="text" value="<?php echo $user->ID>0?$firstName:""?>" required="" >
								<label for="jinput-fn"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
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
								<input type="text" name="email" id="jinput-email" class="validate[required,custom[email]]" value="<?php echo $user->ID>0?$user->email:""?>" required="">
								<label for="jinput-email"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
							</div>
						</div> 
					</div>

					<div class="row">
                        <div class="col-12">
                            <div class="jinput-outline jinput-hover">
                                <input type="text" name="phone" id="jinput-phone" class="validate[required]" value="" required="">
                                <label for="jinput-phone"><?php echo JText::_('LNG_PHONE') ?></label>
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
			
					<?php if($appSettings->captcha){?>
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
						<button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
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
		<input type="hidden" name="companyId" value="" />
	</form>
</div>

<script> 
    window.addEventListener("load", function() {
        jbdUtils.renderContactFormSteps();
    });    
</script>