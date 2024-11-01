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

<div class="row">
    <div class="col-md">
        <div class="ad-container">
            <?php if (!empty($this->company->ad_image)) {?>
                <div class="item-image">
                    <img title="<?php echo $this->escape($this->company->name)?>" alt="<?php echo $this->escape($this->company->name)?>" src="<?php echo BD_PICTURES_PATH.$this->company->ad_image ?>" itemprop="contentUrl">
                </div>
            <?php } ?>
            <p class="ad-caption">
                <?php echo $this->company->ad_caption ?>
            </p>
        </div>
    </div>
</div>
