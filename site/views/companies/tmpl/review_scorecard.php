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

<div class="review-scorecard">
    <div class="d-flex justify-content-between">
        <div class="">
            <div class="d-flex align-items-center">
                <div>
                    <span class="heading"><?php echo count($this->totalReviews) . " " . ((count($this->totalReviews)>1)? JText::_("LNG_REVIEWS"):JText::_("LNG_REVIEW")); ?></span>
                </div>
                <div>
                    <div class="rating">
                        <span class="user-rating-avg rating-average-review" id="rating-average-review" title="<?php echo $this->company->review_score ?>" alt="<?php echo $this->company->id ?>" style="display: block;"></span>
                    </div>
                </div>
            </div>
            <?php if(!empty($this->company->review_score)){ ?>
                <p class="pt-2"><?php echo $this->company->review_score." ". JText::_("LNG_AVERAGE_BASED_ON")." ". count($this->totalReviews)." ".JText::_("LNG_REVIEWS") ?></p>
            <?php } ?>
        </div>
        <div class="">
            <a class="add-review-link btn btn-sm btn-success" href="javascript:void(0)" onclick="jbdListings.showReviewForm(<?php echo ($appSettings->enable_reviews_users && $user->ID == 0) ? "1" : "0"; ?>);event.stopPropagation();"><?php echo JText::_("LNG_ADD_NEW") ?> <i class="la la-plus"></i></a>
        </div>
    </div>
    <hr style="border:3px solid #f1f1f1">
    <div class="row align-center">
        <div class="col-md-2">
            <div><?php echo JText::_("LNG_5_STAR"); ?></div>
        </div>
        <div class="col-md-8">
            <div class="bar-container">
                <div class="bar-5" style="width: <?php echo (count($this->reviewsStatistics[5]) > 0) ? (count($this->reviewsStatistics[5]) / count($this->totalReviews)) * 100 : 0 ?>%"></div>
            </div>
        </div>
        <div class="col-md-2 right">
            <div><?php echo count($this->reviewsStatistics[5]) ?></div>
        </div>
        <div class="col-md-2">
            <div><?php echo JText::_("LNG_4_STAR"); ?></div>
        </div>
        <div class="col-md-8">
            <div class="bar-container">
                <div class="bar-4" style="width: <?php echo (count($this->reviewsStatistics[4]) > 0) ? (count($this->reviewsStatistics[4]) / count($this->totalReviews)) * 100 : 0 ?>%"></div>
            </div>
        </div>
        <div class="col-md-2 right">
            <div><?php echo count($this->reviewsStatistics[4]) ?></div>
        </div>
        <div class="col-md-2">
            <div><?php echo JText::_("LNG_3_STAR"); ?></div>
        </div>
        <div class="col-md-8">
            <div class="bar-container">
                <div class="bar-3" style="width: <?php echo (count($this->reviewsStatistics[3]) > 0) ? (count($this->reviewsStatistics[3]) / count($this->totalReviews)) * 100 : 0 ?>%"></div>
            </div>
        </div>
        <div class="col-md-2 right">
            <div><?php echo count($this->reviewsStatistics[3]) ?></div>
        </div>
        <div class="col-md-2">
            <div><?php echo JText::_("LNG_2_STAR"); ?></div>
        </div>
        <div class="col-md-8">
            <div class="bar-container">
                <div class="bar-2" style="width: <?php echo (count($this->reviewsStatistics[2]) > 0) ? (count($this->reviewsStatistics[2]) / count($this->totalReviews)) * 100 : 0 ?>%"></div>
            </div>
        </div>
        <div class="col-md-2 right">
            <div><?php echo count($this->reviewsStatistics[2]) ?></div>
        </div>
        <div class="col-md-2">
            <div><?php echo JText::_("LNG_1_STAR"); ?></div>
        </div>
        <div class="col-md-8">
            <div class="bar-container">
                <div class="bar-1" style="width: <?php echo (count($this->reviewsStatistics[1]) > 0) ? (count($this->reviewsStatistics[1]) / count($this->totalReviews)) * 100 : 0 ?>%"></div>
            </div>
        </div>
        <div class="col-md-2 right">
            <div><?php echo count($this->reviewsStatistics[1]) ?></div>
        </div>
    </div>

</div>