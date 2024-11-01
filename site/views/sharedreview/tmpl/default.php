<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$companyName = JFactory::getApplication()->input->getString('listingName');
$title = JText::_('LNG_REVIEW_FOR'). ' ' .$companyName .' | '. $this->review->subject;

$picturePath = "";
if(!empty($this->review->pictures)){
    $picturePath = $this->review->pictures[0]->picture_path;
}

JBusinessUtil::setMetaData($title, $this->review->description, array(),false);
JBusinessUtil::setFacebookMetaData($title, $this->review->description, $picturePath, $url);

$langageTab = JBusinessUtil::getLanguageTag();
$langageTab = str_replace("-", "_", $langageTab);
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/<?php echo $langageTab?>/sdk.js#xfbml=1&version=v3.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>


<div class="jbd-container listing-details">
    <ul id="reviews" itemprop="review" itemscope itemtype="https://schema.org/Review">
        <li class="review">
            <div class="review-content">
                <div class="row-fluid" id="socials">
                    <div class="span4">
                        <h4 itemprop="name"><?php echo $this->escape($this->review->subject) ?></h4>
                    </div>
                    <div class="span6 share" style="float:right">
                        <ul style="line-height: initial">
                            <li>
                                <div class="fb-share-button"
                                     data-href="<?php echo htmlspecialchars($url, ENT_QUOTES)?>"
                                     data-layout="button_count">                            </li>
                            <li>
                                <a href="https://twitter.com/share" class="twitter-share-button" data-hashtags="<?php echo $companyName ?>" data-text="<?php echo JText::_('LNG_NEW_REVIEW_ADDED_ON'). ' ' .$companyName .' | '. $this->review->subject.' ';  ?>">Tweet</a>
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                            </li>
                            <li>
                                <script src="https://platform.linkedin.com/in.js" type="text/javascript"> lang: <?php echo JBusinessUtil::getLanguageTag()?></script>
                                <script type="IN/Share" data-title="<?php echo JText::_('LNG_NEW_REVIEW_ADDED_ON'). ' ' .$companyName .' | '. $this->review->name.' ';  ?>" data-counter="right"></script>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="review-author">
                    <p class="review-by-content">
                        <span class="reviewer-name" itemprop="author"> <?php echo $this->escape($this->review->name) ?> </span>,
                        <span class="review-date" itemprop="datePublished"><?php echo JBusinessUtil::getDateGeneralFormat($this->review->creationDate) ?></span>
                    </p>
                </div>

                <div class="rating-block">
                    <?php if(!empty($this->review->scores) && !empty($this->reviewCriterias)){ ?>
                        <div class="review-rating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="reviewRating">
                            <?php echo number_format((float)$this->review->rating,1) ?>
                            <span style="display:none;">
                                <span itemprop="ratingValue"><?php echo number_format((float)$this->review->rating,1) ?></span>
                                <span itemprop="worstRating">0</span>
                                <span itemprop="bestRating">5</span>
                                <span itemprop="ratingCount"><?php echo count($this->reviewCriterias)?></span>
                            </span>
                        </div>
                        <?php
                        if(isset($this->review->criteriaIds)) {
                            foreach ($this->review->criteriaIds as $key => $value) {
                                if (empty($this->reviewCriterias[$value]))
                                    continue;
                                ?>
                                <div class="review-criteria">
                                    <span class="review-criteria-name"><?php echo $this->reviewCriterias[$value]->name ?></span>
                                    <span title="<?php echo $this->review->scores[$key] ?>" class="rating-review"></span>
                                </div>
                            <?php }
                        }?>

                    <?php }else{?>
                        <div>
                            <span title="<?php echo $this->review->rating ?>" class="rating-review"></span>
                        </div>
                    <?php } ?>
                    <div class="clear"></div>
                </div>
                <div class="review-questions" id="<?php echo $this->review->id; ?>">
                    <?php if(!empty($this->review->answerIds) && !empty($this->reviewQuestions) && !empty($this->reviewAnswers)) { ?>
                        <a style="display:none" href="javascript:void(0)" id="show-questions<?php echo $this->review->id; ?>" onclick="jbdListings.showReviewQuestions('<?php echo $this->review->id; ?>')"><?php echo JText::_('LNG_SHOW_REVIEW_QUESTIONS'); ?>	</a>
                        <div id="review-questions<?php echo $this->review->id; ?>">
                            <?php if(!empty($this->review->questionIds)) { ?>
                                <?php foreach($this->review->questionIds as $key=>$value){
                                    if(!isset($this->reviewQuestions[$value]))
                                        continue;
                                    $question = $this->reviewQuestions[$value];
                                    $answer = $this->reviewAnswers[$this->review->answerIds[$value]];
                                    ?>
                                    <?php if(isset($answer->answer)) { ?>
                                        <div class="review-question"><strong><?php echo $question->name?></strong></div>
                                        <?php
                                        if($question->type == 1) {
                                            if ($answer->answer == 0)
                                                $answer->answer = JText::_('LNG_NO');
                                            else if ($answer->answer == 1)
                                                $answer->answer = JText::_('LNG_YES');
                                        }
                                        ?>
                                        <?php if($question->type != 2) { ?>
                                            <div class="review-question-answer review-text" id="question-answer<?php echo $answer->id ?>"><?php echo $answer->answer ?></div>
                                        <?php }
                                        else { ?>
                                            <div id="question-answer<?php echo $answer->id ?>" class="review-question-answer star-rating"></div>
                                            <input type="hidden" id="star-rating-score<?php echo $answer->id ?>" value="<?php echo $answer->answer ?>" />
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="review-description" itemprop="description">
                    <?php echo $this->escape($this->review->description) ?>
                </div>
                <?php if(isset($this->review->responses) && count($this->review->responses)>0) {
                    foreach ($this->review->responses as $response) {
                        ?>
                        <div class="review-response">
                            <strong><?php echo JText::_('LNG_REVIEW_RESPONSE') ?></strong><br/>
                            <span class="bold"><?php echo $this->escape($response->firstName) ?> </span>
                            <p><?php echo $this->escape($response->response) ?></p>
                        </div>
                        <?php
                    }
                }
                require 'review_gallery.php';
                ?>
            </div>
            <div class="clear"></div>
        </li>
    </ul>
</div>


<script>

    window.addEventListener('load', function() {
        jbdListings.magnifyImages('gallery');

        <?php if($this->appSettings->enable_ratings) { ?>
            var averageRating = '<?php echo JFactory::getApplication()->input->get('review_score') ?>';
            jbdListings.renderAverageRating(averageRating);
            jbdListings.renderReviewRating();
    <?php } ?>
        jQuery('.gallery-review >li').css("width", "unset");
    });

    <?php if(!empty($this->reviewAnswers) && count($this->reviewAnswers) > 0) { ?>
    window.addEventListener('load', function(){
        jQuery('.review-questions').each(function(){
            jbdListings.showReviewQuestions(jQuery(this).attr('id'));
        });
    });
    <?php } ?>

</script>