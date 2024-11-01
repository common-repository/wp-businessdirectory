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
<div id="dir-listing-description" >
    <div class="dir-listing-description">
		<?php if (!empty($this->company->description) && (isset($this->package->features) && in_array(HTML_DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
			<?php echo JHTML::_("content.prepare", $this->company->description); ?>
		<?php }else if (!empty($this->company->description) && (isset($this->package->features) && in_array(DESCRIPTION, $this->package->features) || !$appSettings->enable_packages)) { ?>
			<?php echo strip_tags($this->company->description); ?>
		<?php } ?>
     </div>
    
	<div class="listing-details">
		<?php if(!empty($this->company->typeName)){ ?>
			<div class="listing-detail">
    			<div class="listing-detail-header"><?php echo JText::_('LNG_TYPE')?></div>
    			<span><?php echo $this->company->typeName?></span>
			</div>
		<?php } ?>
	
		<?php if(!empty($this->company->keywords)){?>
    		<div class="listing-detail">
    			<div class="listing-detail-header"><?php echo JText::_('LNG_KEYWORDS')?></div>
    			<ul class="business-categories">
    				<?php 
    				$keywords =  explode(',', $this->company->keywords);
    				for($i=0; $i<count($keywords); $i++) { ?>
    					<li>
    						<a  href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&searchkeyword='.$keywords[$i].$menuItemId) ?>"><?php echo $keywords[$i]?><?php echo $i<(count($keywords)-1)? ',&nbsp;':'' ?></a>
    					</li>
    				<?php 
    				} ?>
    			</ul>
    		</div>
		<?php } ?>
		
		<?php if(!empty($this->company->establishment_year)){?>
			<div class="classification">
				<span><?php echo " ".JText::_('LNG_ESTABLISHMENT_YEAR') ?>: <?php echo " ".$this->company->establishment_year;?></span>
			</div>				
		<?php }?>

		<?php if(!empty($this->company->employees)){?>
			<div class="classification">
				<span>
					<?php echo " ".JText::_('LNG_EMPLOYEES') ?>: <?php echo " ".$this->company->employees;?>
				</span>
			</div>
		<?php }?>
		
		<?php if($showData && $appSettings->enable_attachments && (isset($this->package->features) && in_array(ATTACHMENTS, $this->package->features) || !$appSettings->enable_packages)) { ?>
			<?php if(!empty($this->company->attachments)) { ?>
				<div class="listing-detail">
    				<div class="listing-detail-header"><?php echo JText::_('LNG_ATTACHMENTS')?></div>
    				<?php require "listing_attachments.php" ?>
    			</div>
			<?php } ?>
		<?php } ?>

        <div class="classification">
            <?php require_once 'listing_attributes.php'; ?>
        </div>
        
        <?php if((isset($this->package->features) && in_array(RELATED_COMPANIES,$this->package->features) || !$appSettings->enable_packages)
			&& isset($this->realtedCompanies) && count($this->realtedCompanies)){
			?>
				<div class="listing-detail related">
    				<div class="listing-detail-header"><?php echo JText::_('LNG_RELATED_COMPANIES')?></div>
					<?php require_once 'related_business.php';?>
				</div>
		<?php } ?>
        
        <div class="clear"></div>
	</div>
</div>