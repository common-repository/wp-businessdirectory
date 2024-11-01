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
<div class="listing-info">
    <?php
        switch($appSettings->listings_display_info) { 
            case OPENING_HOURS:
                if ($appSettings->show_open_status  && ($company->enableWorkingStatus || $company->opening_status != COMPANY_OPEN_BY_TIMETABLE) && (!$appSettings->enable_packages || isset($company->packageFeatures) && in_array(OPENING_HOURS, $company->packageFeatures))) {
                    if ($company->enableWorkingStatus) { 
                        if ($company->workingStatus) { ?>
                            <div class="badge badge-success"><span><?php echo JText::_("LNG_OPEN") ?></span></div>
                            <?php    
                                $dayIndex = JBusinessUtil::getCurrentDayIndex($company->time_zone);
                                echo JBusinessUtil::renderOpeningDay($company->business_hours[$dayIndex]);
                            ?>
                        <?php } else { ?>
                            <div class="badge badge-danger"><span><?php echo JText::_("LNG_CLOSED") ?></span></div>
                        <?php } ?>
                <?php } else {
                        $statusInfo = JBusinessUtil::getOpeningStatus($company->opening_status); ?>
                        <div class="badge badge-<?php echo $statusInfo->class ?>"><span><?php echo $statusInfo->status ?></span></div>
            <?php } ?>
            <?php } 
            break;
            case MEMBERSHIPS:
                if (!empty($company->memberships)){ ?> 
                    <?php $company->memberships = array_slice($company->memberships, 0, 4); ?>
                    <div class="grid-memberships">
                        <?php 
                            if (isset($company->memberships)) {
                                foreach ($company->memberships as $membership) {
                                    echo '<div title="'.$membership[1].'"><img src="'.BD_PICTURES_PATH.$membership[3].'"> <span>'.$membership[1].'</span></div>';
                                }
                            } 
                        ?>
                    </div>
                <?php }
                break;
            case SOCIAL_NETWORKS:
                require "listing_social_network.php";
                break;
        } 
    ?>
</div>