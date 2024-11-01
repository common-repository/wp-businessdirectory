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
<div class="listing-announcement">
    <div class="row">
        <?php foreach($this->companyAnnouncements as $index=>$item) { ?>
            <div class="col-md-12">
                <div class="announcement-container ">
                    <i class="<?php echo $item->icon ?>"></i>
                    <p>
                        <strong><?php echo $item->title ?></strong>
                        <span><?php echo $item->description ?></span>
                    </p>
                    <?php if(!empty($item->button_link) && !empty($item->button_text)){ ?>
                        <a target="_blank" href="<?php echo $item->button_link ?>" class="announcement-btn btn-success"><?php echo $item->button_text ?></a>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>