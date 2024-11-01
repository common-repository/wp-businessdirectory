<?php if (!empty($this->reviews)) { ?>
    <?php $idx = 1; ?>
	<?php foreach($this->reviews as $review) { ?>
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
                                <div class="review-question"><strong><?php echo $question->name?></strong><?php echo ($this->appSettings->edit_ratings && isset($answer->user_id) && $this->user->ID==$answer->user_id && $this->user->ID!=0) ? ' <i class="la la-pencil" style="cursor:pointer;" onClick="jbdListings.editAnswer('.$answer->id.','.$question->type.')"></i>' : ''; ?></div>
                                <?php
                                if($question->type == 1) {
                                    if ($answer->answer == 0)
                                        $answer->answer = JText::_('LNG_NO');
                                    else if ($answer->answer == 1)
                                        $answer->answer = JText::_('LNG_YES');
                                }
                                $enableEditing = ($this->appSettings->edit_ratings && isset($answer->user_id) && $this->user->ID==$answer->user_id && $this->user->ID!=0) ? 'ondblclick="jbdListings.editAnswer('.$answer->id.','.$question->type.')"' : '';
                                $editClass = (isset($answer->user_id) && $this->user->ID==$answer->user_id && $this->user->ID!=0) ? 'question-answer' : '';
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
                                    <a href="javascript:jbdReviews.reportReviewAbuse(<?php echo ($this->appSettings->enable_reviews_users && $this->user->ID ==0)?"1":"0"; ?>,'<?php echo $review->id?>')"><?php echo JText::_('LNG_REPORT_ABUSE') ?></a>
                                </li>
                                <?php if ($this->allowReviewResponse){ ?>
                                    <li class="review-reply">
                                    <a href="javascript:jbdReviews.respondToReview(<?php echo ($this->appSettings->enable_reviews_users && $this->user->ID ==0)?"1":"0"; ?>,'<?php echo $review->id?>')"><?php echo JText::_('LNG_RESPOND_TO_REVIEW') ?></a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->appSettings->share_reviews){?>
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
        <?php $idx++; ?>
        <?php if ($idx == REVIEWS_LIMIT) break;  ?>
	<?php } ?>
<?php } ?>