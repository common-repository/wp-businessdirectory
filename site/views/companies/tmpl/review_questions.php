<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$user = JBusinessUtil::getUser();
?>

<div class="review-questions">
<?php if(!empty($this->reviewQuestions)){?>
	<?php foreach($this->reviewQuestions as $reviewQuestion) { ?>
	    <?php if($reviewQuestion->published) { ?>
	        <?php if($reviewQuestion->type == 0) { ?>
				<div class="row">
                	<div class="col-12">
					<div class="form-group">
							<label for="user_rating"><?php echo $reviewQuestion->name ?></label>
							<div class="outer_input">
								<textarea class="<?php echo $reviewQuestion->is_mandatory?'validate[required]':'' ?>" name='question-<?php echo $reviewQuestion->id ?>' id='question-<?php echo $reviewQuestion->id ?>'></textarea>
							</div>
						</div>
					</div>
				</div>
	        <?php } else if($reviewQuestion->type == 1) { ?>
               <div class="form-group">
                   <label id="question-<?php echo $reviewQuestion->id; ?>-lbl" for="question-<?php echo $reviewQuestion->id; ?>" title=""><?php echo $reviewQuestion->name; ?></label>
                    <div class="outer_input controlls">
                        <fieldset id="question-<?php echo $reviewQuestion->id; ?>_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio" name="question-<?php echo $reviewQuestion->id; ?>" id="question-<?php echo $reviewQuestion->id; ?>1" value="1" style="display: none" <?php echo $reviewQuestion->is_mandatory?'checked="checked"' :"" ?> />
                            <label class="btn" for="question-<?php echo $reviewQuestion->id; ?>1"><?php echo JText::_('LNG_YES')?></label>
                            <input type="radio" name="question-<?php echo $reviewQuestion->id; ?>" id="question-<?php echo $reviewQuestion->id; ?>0" value="0" style="display: none" />
                            <label class="btn" for="question-<?php echo $reviewQuestion->id; ?>0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
	        <?php } else if($reviewQuestion->type == 2) { ?>
				<div class="row">
                	<div class="col-12">
						<div class="form-group">
							<div class="user-rating clearfix">
								<label for="user_rating"><?php echo $reviewQuestion->name ?></label><div class="rating-question"></div>
								<input type="hidden" class="<?php echo $reviewQuestion->is_mandatory?'validate[required]':'' ?>" name="question-<?php echo $reviewQuestion->id ?>" id="review-question" value="">
							</div>
						</div>
					</div>
				</div>
	        <?php } ?>
	    <?php } ?>
	<?php } ?>
<?php } ?>
    <input type="hidden" name="user_id" id="user_id" value="<?php echo $user->ID ?>" />
</div>
