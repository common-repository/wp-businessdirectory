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

<span style="font-weight: 600;"><?php echo JText::_("LNG_TOTAL_REVIEWS").": ". $reviews->total ?></span>
<ul id="reviews" itemprop="review" itemscope itemtype="https://schema.org/Review">
    <?php foreach ($reviews->reviews as $review) { ?>
        <li class="review">
            <div class="review-content">
                <h4 itemprop="name"><?php echo $review->user->name ?></h4>

                <div class="review-author">
                    <p class="review-by-content">
                            <span class="review-date"
                                  itemprop="datePublished"><?php echo JBusinessUtil::getDateGeneralFormat($review->time_created) ?></span>
                    </p>
                </div>

                <div class="rating-block">
                    <div>
                        <span title="<?php echo $review->rating ?>" class="rating-review"></span>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="review-description" itemprop="description">
                        <pre class="review-text"><?php echo $review->text ?>
                            <a target="_blank" href="<?php echo $review->url ?>">
                                <?php echo JText::_("LNG_READ_MORE") ?>
                            </a>
                        </pre>
                </div>
            </div>
        </li>
    <?php } ?>
    <a target="_blank" style="float: right;" href="<?php echo $reviews->reviews[0]->url ?>">
        <?php echo JText::_("LNG_VIEW_ALL_REVIEWS") ?>
    </a>
</ul>