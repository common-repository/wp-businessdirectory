<?php // no direct access
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
?>

<div class="jbd-container">
    <div class="successful-container row justify-content-center">
        <div class="col-md-8">
            <div class="successful-wrapper">
                <div class="successful-icon-wrapper">
                    <i class="successful-icon thumbs-up"></i>
                </div>
                <h4><?php echo JText::_('LNG_LISTING_COMPLETED_SUCCESSFULLY'); ?></h4><br/>

                <?php if(empty($this->onlyContribute)){ ?>
                    <div class="successful-text"><?php echo JText::_('LNG_LISTING_COMPLETED_SUCCESSFULLY_TXT'); ?></div>
                <?php }else{ ?>
                    <div class="successful-text"><?php echo JText::_('LNG_LISTING_COMPLETED_SUCCESSFULLY_TXT'); ?></div>
                    <br/>
                    <div class="successful-text"><?php echo JText::_('LNG_USER_REGISTERED_MESSAGE'); ?></div>
                    <br/>
                <?php } ?>

                <?php if(!empty($this->userCreated)){ ?>
                    <br/>
                    <div class="successful-text"><?php echo JText::_('LNG_USER_REGISTERED_MESSAGE'); ?></div>
                <?php } ?>
               
            </div>
            
            <?php if(empty($this->onlyContribute)){ ?>
                <div class="row justify-content-center options">
                    <div class="col-md-3 text-center">
                        <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false) ?>">
                        <i class="la la-tachometer "></i><br/>
                            <span><?php echo JText::_("LNG_CONTROL_PANEL") ?></span>
                        </a>
                    </div>
                    <div class="col-md-3 text-center">
                        <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=managecompany.edit&'.JSession::getFormToken().'=1&id='. $this->listingId )?>">
                            <i class="la la-pencil"></i><br/>
                            <span><?php echo JText::_("LNG_EDIT_LISTING") ?></span>
                        </a>
                    </div>
                    <div class="col-md-3 text-center">
                        <a href="<?php echo JBusinessUtil::getWebsiteURL(true).('index.php?option=com_jbusinessdirectory&view=companies&companyId='.$this->listingId) ?>">
                            <i class="la la-eye"></i><br/>
                            <span><?php echo JText::_("LNG_VIEW") ?></span>
                        </a>
                    </div>
                </div>
            <?php } ?>

            <br/><br/>
            <div class="successful-payment-wrapper">
                <a href="index.php"><i class="la la-reply"></i> <?php echo JText::_('LNG_BACK_TO_HOME') ?></a>
            </div>
        </div>
	</div>
</div>