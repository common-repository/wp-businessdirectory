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

<div class="business-team">
    <?php if (!empty($this->teamMembers['leadership'])){?>
        <div class="pt-md" style="text-align:left">
            <div class="team-title"><?php echo JText::_("LNG_LEADERSHIP") ?></div>
            <div class="row">
                <?php foreach ($this->teamMembers['leadership'] as $leader) {?>
                    <div class="col-md-4 col-sm-2 col-12">
                        <div class="member-image" style="background-image: url('<?php echo !empty($leader->image)?BD_PICTURES_PATH.$leader->image:BD_PICTURES_PATH.'/no_image.jpg' ?>')">
                        </div>
                        <div class="member-name"><?php echo $leader->name; ?></div>
                        <div class="member-title"><?php echo $leader->title; ?></div>
                        <div class="member-description"><?php echo $leader->description; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($this->teamMembers['team'])){ ?>
            <div class="pt-md" style="text-align:left">
                <div class="team-title"><?php echo JText::_("LNG_TEAM") ?></div>
                <div class="row">
                <?php foreach ($this->teamMembers['team'] as $i=>$member) {?>
                    <div class="col-md-4 col-sm-2 col-12">
                        <div class="member-image" style="background-repeat: no-repeat; background-image: url('<?php echo !empty($member->image)?BD_PICTURES_PATH.$member->image:BD_PICTURES_PATH.'/no_image.jpg' ?>')">
                        </div>
                        <div class="member-name"><?php echo $member->name; ?></div>
                        <div class="member-title"><?php echo $member->title; ?></div>
                        <div class="member-description"><?php echo $member->description; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
