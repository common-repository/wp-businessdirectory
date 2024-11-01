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

<?php
if($this->appSettings->enable_ratings) {
	require_once 'review_scorecard.php';
} else { ?>
    <a class="add-review-link btn btn-sm btn-success" href="javascript:void(0)" onclick="jbdListings.showReviewForm(<?php echo ($this->appSettings->enable_reviews_users && $user->ID ==0)?"1":"0"; ?>);event.stopPropagation();"><?php echo JText::_("LNG_ADD_REVIEW") ?> <i class="la la-plus"></i></a>
<?php }?>

<?php
    if(GET_DATA_FROM_YELP) {
        $reviews = $this->reviews;
        $this->reviews = isset($this->reviews->error) ? array() : $this->reviews->reviews;
    }
?>

<?php if(count($this->totalReviews)==0){ ?>
	<p><?php echo JText::_('LNG_NO_REVIEWS') ?></p>
<?php }elseif (!GET_DATA_FROM_YELP){ ?>
<ul id="reviews" itemprop="review" itemscope itemtype="https://schema.org/Review">
<?php $i = 1; ?>        
	<?php foreach($this->reviews as $review){?>
		<li class="review">
			<div class="review-content">
                <div class="review-header">
                    <?php if(!empty($review->scores) && !empty($this->reviewCriterias)){ ?>
                        <div class="pr-2">
                            <div class="rating-block">
                                <div class="review-rating" itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="reviewRating">
                                    <?php echo number_format((float)$review->rating,1) ?>
                                    <span style="display:none;">
                                            <span itemprop="ratingValue"><?php echo number_format((float)$review->rating,1) ?></span>
                                            <span itemprop="worstRating">0</span>
                                            <span itemprop="bestRating">5</span>
                                            <span itemprop="ratingCount"><?php echo count($this->reviewCriterias)?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div>
                        <div class="review-title" itemprop="name"><?php echo $this->escape($review->subject) ?></div>
                        
                        <div class="review-author">
                            <p class="review-by-content">
                            <span class="reviewer-name" itemprop="author"> <?php echo $this->escape($review->name) ?> </span>,
                                <span class="review-date" itemprop="datePublished"><?php echo JBusinessUtil::getDateGeneralFormat($review->creationDate) ?></span>
                            </p>
                        </div>

                        <?php if(empty($review->scores) || empty($this->reviewCriterias)){ ?>
                            <div>
                                <span title="<?php echo $review->rating ?>" class="rating-review"></span>
                            </div>	
                        <?php } ?>
                    </div>
                </div>    
                
                <?php if(!empty($review->scores) && !empty($this->reviewCriterias)){ ?>
                    <div class="review-criterias">
                        <?php
                            if(isset($review->criteriaIds)) {
                                foreach ($review->criteriaIds as $key => $value) {
                                    if (empty($this->reviewCriterias[$value]))
                                        continue;
                                    ?>
                                    <div class="review-criteria">
                                        <span class="review-criteria-name"><?php echo $this->reviewCriterias[$value]->name ?></span>
                                        <span title="<?php echo $review->scores[$key] ?>" class="rating-review"></span>
                                    </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>

				<div class="review-questions" id="<?php echo $review->id; ?>">
					<?php if(!empty($review->answerIds) && !empty($this->reviewQuestions) && !empty($this->reviewAnswers)) { ?>
						<a style="display:none" href="javascript:void(0)" id="show-questions<?php echo $review->id; ?>" onclick="jbdListings.showReviewQuestions('<?php echo $review->id; ?>')"><?php echo JText::_('LNG_SHOW_REVIEW_QUESTIONS'); ?>	</a>
						<div id="review-questions<?php echo $review->id; ?>">
                            <?php if(!empty($review->questionIds)) { ?>
                            <?php foreach($review->questionIds as $key=>$value){
                                if(!isset($this->reviewQuestions[$value]))
                                    continue;
                                $question = $this->reviewQuestions[$value];
                                $answer = $this->reviewAnswers[$review->answerIds[$value]];
                            ?>
                            <?php if(isset($answer->answer)) { ?>
                                <div class="review-question"><strong><?php echo $question->name?></strong><?php echo ($this->appSettings->edit_ratings && isset($answer->user_id) && $user->ID==$answer->user_id && $user->ID!=0) ? ' <i class="la la-pencil" style="cursor:pointer;" onClick="jbdListings.editAnswer('.$answer->id.','.$question->type.')"></i>' : ''; ?></div>
                                <?php
                                if($question->type == 1) {
                                    if ($answer->answer == 0)
                                        $answer->answer = JText::_('LNG_NO');
                                    else if ($answer->answer == 1)
                                        $answer->answer = JText::_('LNG_YES');
                                }
                                $enableEditing = ($this->appSettings->edit_ratings && isset($answer->user_id) && $user->ID==$answer->user_id && $user->ID!=0) ? 'ondblclick="jbdListings.editAnswer('.$answer->id.','.$question->type.')"' : '';
                                $editClass = (isset($answer->user_id) && $user->ID==$answer->user_id && $user->ID!=0) ? 'question-answer' : '';
                                ?>
                                <?php if($question->type != 2) { ?>
                                    <div <?php echo $enableEditing ?> class="review-question-answer review-text <?php echo $editClass ?>" id="question-answer<?php echo $answer->id ?>"><?php echo $answer->answer ?></div>
                                <?php }
                                else { ?>
                                    <div id="question-answer<?php echo $answer->id ?>" class="review-question-answer star-rating <?php echo $editClass ?>"></div>
                                    <input type="hidden" id="star-rating-score<?php echo $answer->id ?>" value="<?php echo $answer->answer ?>" />
                                <?php } ?>
                                <?php } ?>
                                <?php } ?>
                            <?php } ?>
						</div>
					<?php } ?>
				</div>
				
				<div class="review-description" itemprop="description">
					<?php echo $this->escape($review->description) ?>
				</div>
				<?php require 'review_gallery.php';	?>

                <div class="review-actions-container">
                    <div class="rate-review">
                        <ul>
                            <li class="thumbup">
                                <a
                                    id="increaseLike<?php echo $review->id ?>"
                                    href="javascript:void(0)" onclick="jbdReviews.increaseReviewLikeCount(<?php echo $review->id ?>)"><?php echo JText::_("LNG_THUMB_UP")?>
                                </a> <span class="count"><span id="like<?php echo $review->id ?>"><?php echo $review->likeCount ?></span></span>
                            </li>
                            <!-- <li class="thumbdown">
                                <a
                                    id="decreaseLike<?php echo $review->id ?>"
                                    href="javascript:void(0)" onclick="jbdReviews.increaseReviewDislikeCount(<?php echo $review->id ?>)"><?php echo JText::_("LNG_THUMB_DOWN")?>
                                </a>
                                <span class="count"><span id="dislike<?php echo $review->id ?>"><?php echo $review->dislikeCount ?></span></span>
                            </li> -->
                            <li class="review-love">
                                <a
                                    id="increaseLove<?php echo $review->id ?>"
                                    href="javascript:void(0)" onclick="jbdReviews.increaseReviewLoveCount(<?php echo $review->id ?>)"><?php echo JText::_("LNG_LIKE")?>
                                </a>
                                <span class="count"><span id="love<?php echo $review->id ?>"><?php echo $review->loveCount ?></span></span>
                            </li>

                        </ul>
                    </div>
                    
                    <?php if ($this->appSettings->show_verified_review_badge) { ?>
                        <div class="verified-review-policy-container" onclick="jQuery('#verified-review-policy').jbdModal();"> 
                            <img src="<?php echo BD_ASSETS_FOLDER_PATH.'/images/verified.svg' ?>" alt="verified_review" width="20px" height="20px">
                            <div class="verified-review-policy-title">
                                <?php echo JText::_('LNG_VERIFIED_REVIEW_BADGE_TEXT')?>
                            </div>     
                        </div>
                    <?php } else { ?>
                        <div class="review-actions">
                            <ul>
                                <li class="review-report">
                                    <a href="javascript:jbdReviews.reportReviewAbuse(<?php echo ($appSettings->enable_reviews_users && $user->ID ==0)?"1":"0"; ?>,'<?php echo $review->id?>')"><?php echo JText::_('LNG_REPORT_ABUSE') ?></a>
                                </li>
                                <?php if ($this->allowReviewResponse){ ?>
                                    <li class="review-reply">
                                    <a href="javascript:jbdReviews.respondToReview(<?php echo ($appSettings->enable_reviews_users && $user->ID ==0)?"1":"0"; ?>,'<?php echo $review->id?>')"><?php echo JText::_('LNG_RESPOND_TO_REVIEW') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($appSettings->share_reviews){?>
                                    <li class="review-share">
                                        <a class="status-badge badge-success" href="javascript:shareReview(<?php echo $review->id ?>)"><?php echo JText::_('LNG_SHARE_THIS_REVIEW') ?></a>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>

                <?php if(isset($review->responses) && count($review->responses)>0) {
					foreach ($review->responses as $response) {
				?>
						<div class="review-response">
                            <strong><?php echo JText::_('LNG_REVIEW_RESPONSE') ?></strong><br/>
                            <div class="review-author">
                                <p class="review-by-content">
                                <span class="reviewer-name" itemprop="author"> <?php echo $this->escape($response->firstName)." ". $this->escape($response->lastName) ?> </span>,
                                    <span class="review-date" itemprop="datePublished"><?php echo JBusinessUtil::getDateGeneralFormat($response->created) ?></span>
                                </p>
                            </div>
                            <p class="review-description"><?php echo $this->escape($response->response) ?></p>
						</div>
					<?php } ?>
                <?php } ?>
			</div>
		</li>
        <?php $i++; ?>
        <?php if ($i == REVIEWS_LIMIT) break;  ?>
	<?php } ?>
</ul>
<?php if(count($this->reviews) == REVIEWS_LIMIT){ ?>
    <div id="load-more-btn" class="row">
        <div class="col-12 text-center mt-4">
            <button type="button" class="btn btn-outline-primary" onclick="jbdReviews.loadMoreReviews()" ><?php echo JText::_("LNG_LOAD_MORE")?></button>
        </div>
    </div>
    <?php $start = REVIEWS_LIMIT - 1; ?>
    <input type="hidden" id="start" value="<?php echo $start ?>"/>
    <input type="hidden" id="company" name="company" value="<?php echo $this->company->id ?>"/>
<?php } ?>
<?php }elseif (GET_DATA_FROM_YELP) {
    require_once 'listing_yelp_reviews.php';
}?>

<div class="clear"></div>

<div id="report-abuse" class="jbd-container" style="display:none">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" id="reportAbuse" name="reportAbuse"  method="post">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_REPORT_ABUSE') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>

            <div class="jmodal-body">
                <p>
                    <?php echo JText::_("LNG_ABUSE_INFO");?>
                </p>
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <input type="text" name="email" id="jinput-email-abuse" class="validate[required,custom[email]]" value="" required="">
                            <label for="jinput-email-abuse"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <textarea rows="5" name="description" id="description-abuse" cols="50" class="form-control validate[required]" required=""></textarea>
                            <label for="description-abuse"><?php echo JText::_('LNG_REPORT_ABUSE_BECAUSE')?>:</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php echo JBusinessUtil::renderTermsAndConditions('reviews'); ?>
                    </div>
                </div>

                <?php if($this->appSettings->captcha){?>
                    <div class="form-item">
                        <?php
                        $namespace="jbusinessdirectory.contact";
                        $class=" required";

                        $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                        if(!empty($captcha)){
                            echo $captcha->display("captcha", "captcha-div-review-abuse", $class);
                        }

                        ?>
                    </div>
                <?php } ?>
                <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                <input type='hidden' name='task' value='companies.reportAbuse'/>
                <input type='hidden' name='view' value='companies' />
                <input type="hidden" name="reviewId" value="" />
                <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
            </div>
            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="button" class="jmodal-btn jbd-commit" onclick="jbdUtils.saveForm('reportAbuse')"><?php echo JText::_("LNG_SUBMIT")?></button>
                </div>
            </div>
        </div>
    </form>
</div>


<div id="new-review-response" class="jbd-container" style="display:none">
    <form id="reviewResponseFrm" name ="reviewResponseFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory'.$menuItemId) ?>" method="post">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_RESPOND_REVIEW') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>

            <div class="jmodal-body">
                <p><?php echo JText::_('LNG_RESPOND_REVIEW_INFO') ?></p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="jinput-outline jinput-hover">
                            <input type="text" name="firstName" id="firstName-respond" class="validate[required]" required="">
                            <label for="firstName-respond"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="jinput-outline jinput-hover">
                            <input type="text" name="lastName" id="lastName-respond" class="validate[required]" required="">
                            <label for="lastName-respond"><?php echo JText::_('LNG_LAST_NAME') ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <input type="text" name="email" id="email-respond" class="validate[required,custom[email]]" required="">
                            <label for="email-respond"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <textarea rows="5" name="response" id="response" cols="50" class="form-control validate[required]" required=""></textarea>
                            <label for="response"><?php echo JText::_('LNG_REVIEW_RESPONSE')?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php echo JBusinessUtil::renderTermsAndConditions('reviews'); ?>
                    </div>
                </div>                

                <?php if($this->appSettings->captcha){?>
                    <div class="form-item">
                        <?php
                            $namespace="jbusinessdirectory.contact";
                            $class=" required";

                            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                            if(!empty($captcha)){
                                echo $captcha->display("captcha", "captcha-div-review-response", $class);
                            }
                        ?>
                    </div>
                <?php } ?>

                <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                <input type='hidden' name='task' value='companies.saveReviewResponse'/>
                <input type='hidden' name='view' value='companies' />
                <input type="hidden" name="reviewId" value="" />
                <input type="hidden" name="companyId" value="<?php echo $this->company->id?>" />
            </div>
            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="button" class="jmodal-btn jbd-commit" onclick="jbdUtils.saveForm('reviewResponseFrm')"><?php echo JText::_("LNG_SUBMIT")?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php if ($this->appSettings->share_reviews){?>
<div id="review_content_frame" class="jbd-container" style="display: none; max-width: 660px">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <iframe style="width: 100%;height: 480px;" id="reviewIfr" class="review_content_frame" src="about:blank">
            </iframe>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($this->appSettings->show_verified_review_badge){?>
    <div class="jbd-container" id="verified-review-policy" style="display:none; max-width: 660px;">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_VERIFIED_REVIEW_POLICY') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <img src="<?php echo BD_ASSETS_FOLDER_PATH.'/images/verified.svg' ?>" alt="verified_review" class="mx-auto mb-4" width="120px">
                        </div>    
                        <div class="review-policy-title mb-3">
                            <strong><?php echo JText::_('LNG_GENUINE_REVIEW') ?></strong>
                        </div>
                        <div class="review-policy-text">
                            <?php echo JText::_('LNG_VERIFIED_REVIEW_POLICY_TEXT') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script>
	<?php if(count($this->reviewAnswers) > 0) { ?>
        window.addEventListener('load', function(){
            jQuery('.review-questions').each(function(){
                jbdListings.showReviewQuestions(jQuery(this).attr('id'));
            });
        });
	<?php } ?>

    <?php if ($this->appSettings->share_reviews){?>
        function shareReview(reviewId) {
            var companyId = '<?php echo $this->company->id ?>';
            var type = '<?php echo REVIEW_TYPE_BUSINESS ?>';
            var averageRating = '<?php echo $this->company->review_score ?>';
            var listingName = '<?php echo $this->company->name ?>';
            var url = '<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=sharedreview&tmpl=component'); ?>';
            var urlReviewShare = url + "&review_id=" + reviewId+ "&companyId=" + companyId + "&type="+ type + "&review_score=" + averageRating + "&listingName="+listingName;
            jQuery("#reviewIfr").attr("src", urlReviewShare);

            var options = {
                modalClass: "jbd-modal jbd-invoice"
            };

            jQuery('#review_content_frame').jbdModal(options);
            jQuery("#reviewIfr").ready(function() {
                document.getElementById('reviewIfr').style.height = document.getElementById('reviewIfr').contentWindow.document.body.scrollHeight + 'px';
            });
        }

        <?php } ?>

</script>