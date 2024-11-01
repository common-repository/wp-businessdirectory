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
<?php if((isset($this->package->features) && in_array(MEMBERSHIPS,$this->package->features) || !$appSettings->enable_packages) && !empty($this->memberships)) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php $type = $this->memberships[0]->type ?>
            <h4><?php echo JText::_("LNG_MEMBERSHIP_TYPE_" . $type) ?></h4>
            <div class="row">
            <?php 
                $index = 0;
                foreach ($this->memberships as $membership) {
                    $newType = $membership->type;
                    if ($newType != $type) {
                    ?>
                        </div>
                        <br/>
                        <h4><?php echo JText::_("LNG_MEMBERSHIP_TYPE_" . $newType) ?></h4>
                        <div class="row">
                    <?php } ?>
                    
                    <div class="membership-item col-md-4">
                        <a href="<?php echo  !empty($membership->url) ? $membership->url : 'javascript:void(0);' ?>" title="<?php echo $membership->name ?>">
                            <img title="<?php echo !empty($membership->image_title) ? $membership->image_title : $membership->name ?>"
                                 src="<?php echo BD_PICTURES_PATH . $membership->logo_location ?>"
                                 alt="<?php echo $membership->name ?>" class="img-responsive"/>
                        </a>
                    </div>
                	<?php $type = $newType; ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>