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

$listingLayout = JFactory::getApplication()->input->get('listing_layout');

?>
<div id="jbd-container" class="jbd-container listing-details">
    <?php
    $newTab = ($this->appSettings->open_listing_on_new_tab)?" target='_blank'":"";
    $companyView = $this->appSettings->company_view;
    $enableBusinessViewType = $this->appSettings->allow_business_view_style_change;

    if(!empty($this->company->companyView)){
        $companyViewType = explode(',',$this->company->companyView);
        if(!empty($companyViewType[0])){
            $companyView = $companyViewType[0];
        }
    }

    if($enableBusinessViewType && !empty($this->company->company_view) && $this->company->company_view != "0") {
        $companyView = $this->company->company_view;
    }
    
    if(!empty($listingLayout)) {
        $companyView = $listingLayout;
    }
    
    $tpl = '';   

    if(!empty($companyView)){            
        $tpl = $this->loadTemplate("style_".$companyView);
    }else{
        $tpl = $this->loadTemplate("style_8");
    }    
        
        
        $user = JBusinessUtil::getUser();
        
        if(empty($this->company) || ($user->ID!=$this->company->userId || empty($user->ID)) && (empty($this->company) || $this->company->state == 0
            || $this->company->approved== COMPANY_STATUS_DISAPPROVED
            || ($this->company->approved== COMPANY_STATUS_CREATED && ($this->appSettings->enable_item_moderation=='0' || ($this->appSettings->enable_item_moderation=='1' && $this->appSettings->show_pending_approval == '1'))==false )
            || ($this->appSettings->enable_packages && empty($this->package))
            || (!JBusinessUtil::checkDateInterval($this->company->publish_start_date, $this->company->publish_end_date, null, true, true)))){
            $tpl = $this->loadTemplate("inactive");
        }
        
        echo $tpl;
    ?>
</div>

<?php require_once JPATH_COMPONENT_SITE . '/include/bookmark_utils.php'; ?>

<script>
    window.addEventListener("load", function () {
        jQuery("#content-responsible-link").click(function () {
            jQuery("#content_responsible_text").toggle();
        });

        jQuery(".chosen-select").chosen({width:"95%" , placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});

        jQuery('#whatsapp-link').click(function() {
            jbdUtils.increaseShareClicks(<?php echo $this->company->id ?>, <?php echo STATISTIC_ITEM_BUSINESS ?>);
        });

        jbdListings.saveCookieLastViewed(<?php echo $this->company->id ?>);
    });
</script>