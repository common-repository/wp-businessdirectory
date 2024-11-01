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
<div class="contact-persons">
	<?php if(!empty($this->companyContacts)){ ?>
		<?php foreach($this->companyDepartments as $department) {?>
			<?php if(!empty($department->contact_department)){?>
    	    	<div class="contact-department"><?php echo $department->contact_department ?></div>
			<?php } ?>
    	    <?php foreach($this->companyContacts as $contact) {?>
    	       <?php if (trim($department->contact_department) == trim($contact->contact_department)){ ?>
    	        <div id="contact-person-details<?php echo $contact->id?>" class="contact-person-details" onclick="jQuery(this).toggleClass('open')">
					<div class="contact-header">
						<div>
							<div class="contact-name"><?php echo $this->escape($contact->contact_name); ?></div>
							<?php if(!empty($contact->contact_job_title)) {?>
								<div class="job-title"><?php echo $this->escape($contact->contact_job_title); ?></div>
							<?php }?>
						</div>
						<div>
							<i class="contact-arrow la"></i>
						</div>
					</div>
    	          	
    	          	<div class="contact-item">
    		              <?php if(!empty($contact->contact_email) && $showData && $appSettings->show_email) { ?>
    		                <div>
    		                	<i class="icon envelope"></i> <a href="mailto:<?php echo $this->escape($contact->contact_email) ?>"><?php echo $this->escape($contact->contact_email) ?></a>
    		                </div>
    		              <?php }?>
    		
    		              <?php if(!empty($contact->contact_fax)) {?>
    		              		<div><i class="icon fax"></i> <?php echo $this->escape($contact->contact_fax); ?></div>
    		              <?php }?>
    		
    		              <?php if(!empty($contact->contact_phone)) { ?>
    		              	<div><i class="icon phone"></i> <a href="tel:<?php echo $this->escape($contact->contact_phone); ?>"><?php echo $this->escape($contact->contact_phone); ?></a></div>
    		           	 <?php } ?>
    	           	 </div>
    	        </div>
    	      <?php } ?>
    	    <?php }?>
		<?php  } ?>
	<?php }?>
</div>
