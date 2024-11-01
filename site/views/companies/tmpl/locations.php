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

<div id="company-locations">
    <?php $address = JBusinessUtil::getAddressText($this->company); ?>
    <?php if (!empty($address)) { ?>
        <label><?php echo JText::_('LNG_PRIMARY_LOCATIONS'); ?></label>
        <div class="company-location" id="location">
            <?php echo $address; ?>
        </div>
        <br/>
    <?php } ?>
    <?php if(!empty($this->company->locations)){?>
        <label><?php echo JText::_('LNG_SECONDARY_LOCATIONS'); ?></label>
        <fieldset>
        	<?php foreach ($this->company->locations as $location) {
                if (isset($company->publish_only_city) && $company->publish_only_city) {
                    $location->publish_only_city = 1;
                }
        		?>
        		<div class="company-location" id="location-<?php echo $location->id ?>">
        			<i class="icon map-marker"></i>&nbsp;<?php echo (!empty($location->name) ? strtoupper($location->name) . " - " : "") . JBusinessUtil::getAddressText($location); ?>
        			<?php echo !empty($location->phone) ? "&nbsp;&nbsp;&nbsp;<i class='la la-phone'></i> " . $location->phone : ''; ?>
        		</div>
        		<?php
        	} ?>
        </fieldset>
    <?php }?>
</div>
