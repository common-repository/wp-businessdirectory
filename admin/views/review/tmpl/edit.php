<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior.
JHtml::_('formbehavior.chosen', 'select');
$maxPictures = isset($this->appSettings->max_review_images)?$this->appSettings->max_review_images:6;
$nrPictures = count($this->pictures);
$allowedNr = (int)$maxPictures - $nrPictures;
$allowedNr=($allowedNr<0)?0:$allowedNr;
$allowedNr = ($allowedNr == 0)?$maxPictures:$allowedNr;
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    jQuery("#item-form").validationEngine('detach');
        JBD.submitbutton = function(task) {
            if (task == 'review.cancel' || !jbdUtils.validateCmpForm(true, false)) {
                JBD.submitform(task, document.getElementById('item-form'));
            }
        };
    jQuery("#item-form").validationEngine('attach');
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id=' . (int)$this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">
                            <h3> <?php echo JText::_('LNG_EDIT_REVIEW');?></h3>
                            <div class="form-container label-w-100">
                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_NAME')?> </label>
                                    <input type="text"	name="name" id="name" class="form-control" value="<?php echo $this->escape($this->item->name) ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo JText::_('LNG_EMAIL')?> </label>
                                    <input type="text"	name="email" id="email" class="form-control validate[custom[email]]" value="<?php echo $this->escape($this->item->email) ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="item_id"><?php echo JText::_('LNG_ID')?> </label>
                                    <input type="text"	name="item_id" id="item_id" class="form-control" value="<?php echo $this->escape($this->item->id) ?>"  maxLength="100" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="date_created"><?php echo JText::_('LNG_CREATION_DATE')?> </label>
                                    <input type="text"	name="date_created" id="date_created" class="form-control input_txt text-input key" value="<?php echo $this->item->creationDate ?>"  maxLength="100" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="state"><?php echo JText::_('LNG_STATE')?></label>
                                    <select class="form-control input-medium validate[required]" name="state" id="state">
                                        <?php foreach ($this->states as $allstates){?>
                                            <option value = '<?php echo $allstates->value?>' <?php echo $allstates->value==$this->item->state? "selected" : ""?>> <?php echo $allstates->text?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="subject"><?php echo JText::_('LNG_SUBJECT')?> </label>
                                    <input type="text"	name="subject" id="subject" class="form-control" value="<?php echo $this->escape($this->item->subject) ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="description"><?php echo JText::_('LNG_DESCRIPTION')?></label>
                                    <textarea name="description" id="description" class="form-control  h-auto" cols="75" rows="10" ><?php echo $this->item->description ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="rating"><?php echo JText::_('LNG_RATING')?> </label>
                                    <input type="text"	name="rating" id="rating" class="form-control" value="<?php echo $this->item->rating ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="subject"><?php echo JText::_('LNG_LIKE_COUNT')?> </label>
                                    <input type="text"	name="likeCount" id="likeCount" class="form-control" value="<?php echo $this->item->likeCount ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="subject"><?php echo JText::_('LNG_DISLIKE_COUNT')?> </label>
                                    <input type="text"	name="dislikeCount" id="dislikeCount" class="form-control" value="<?php echo $this->item->dislikeCount ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="subject"><?php echo JText::_('LNG_LOVE_COUNT')?> </label>
                                    <input type="text"	name="loveCount" id="loveCount" class="form-control" value="<?php echo $this->item->loveCount ?>"  maxLength="100">
                                </div>
                            </div>
                        </fieldset>

                        <?php if (!empty($this->reviewCriteriasAnswer)) { ?>
                            <fieldset class="boxed">
                                <div class="form-container label-w-100">
                                    <div class="form-group">
                                        <h3><label id="criteria-lbl" for="criteria" class="title-section" class="hasTooltip" data-toggle="tooltip" title=""><?php echo JText::_("LNG_CRITERIAS") ?></label></h3>
                                        <div class="control-group">
                                            <fieldset>
                                                <?php foreach ($this->reviewCriteriasAnswer as $criteria) { ?>
                                                    <div class="control-group">
                                                        <div class="control-label">
                                                            <label class="key">
                                                                <?php echo $criteria->criteria_name; ?>
                                                            </label>
                                                        </div>
                                                        <div class="controls">
                                                            <select name="criteria[<?php echo $criteria->criteria_id; ?>]"  id="criteria-<?php echo $criteria->criteria_id; ?>">
                                                                <?php for ($i = 0; $i <= 5; $i = $i + 0.5) { ?>
                                                                    <option value="<?php echo $i ?>" <?php echo $criteria->score == $i ? 'selected' : "" ?> ><?php echo $i ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <?php if (!empty($this->reviewQuestionAnswer)) { ?>
                            <fieldset class="boxed">
                                <div class="form-group">
                                    <h3><label id="criteria-lbl" for="answer" class="title-section" class="hasTooltip" data-toggle="tooltip" title=""><?php echo JText::_("LNG_QUESTION_ANSWER") ?></label></h3>

                                    <div class="control-group">

                                        <?php foreach ($this->reviewQuestionAnswer as $questionAnswer) { ?>
                                            <?php if ($questionAnswer->type == 0) { ?>
                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label class="key"> <?php echo $questionAnswer->review_question; ?></label>
                                                    </div>
                                                    <div class="controls control-group">
                                                        <textarea name="answer[<?php echo $questionAnswer->question_id; ?>]" id="answer_question-<?php echo $questionAnswer->question_id; ?>" style="width:auto;height:35px" cols="50"><?php echo $questionAnswer->answer; ?></textarea>
                                                    </div>

                                                <?php
                                            } elseif ($questionAnswer->type == 1) { ?>
                                                <div class="control-group">

                                                    <div class="control-label">
                                                        <label class="key"> <?php echo $questionAnswer->review_question; ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <select name="answer[<?php echo $questionAnswer->question_id; ?>]" id="answer-<?php echo $questionAnswer->question_id; ?>"
                                                                class="inputbox input-medium">
                                                            <option
                                                                value="1" <?php echo $questionAnswer->answer == 1 ? 'selected' : "" ?> ><?php echo JText::_('LNG_YES') ?></option>
                                                            <option
                                                                value="0" <?php echo $questionAnswer->answer == 0 ? 'selected' : "" ?> ><?php echo JText::_('LNG_NO') ?></option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <?php
                                            } else { ?>
                                                <div class="control-group">
                                                    <div class="control-label">
                                                        <label class="key"> <?php echo $questionAnswer->review_question; ?></label>
                                                    </div>
                                                    <div class="controls">
                                                        <select name="answer[<?php echo $questionAnswer->question_id; ?>]" id="answer-<?php echo $questionAnswer->question_id; ?>" class="inputbox input-medium">
                                                            <?php for ($i = 0; $i <= 10; $i = $i + 0.5) { ?>
                                                                <option value="<?php echo $i ?>" <?php echo $questionAnswer->answer == $i ? 'selected' : "" ?> ><?php echo $i ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                </div>

                                            <?php } ?>
                                        <?php } ?>

                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>

                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_REVIEW_PICTURES');?> </h2>
                            <p> <?php echo JText::_('LNG_REVIEW_PICTURES_INFORMATION_TEXT');?>.</p>
                            <a class="btn btn-outline-danger" id="remove-pictures" href="javascript:void(0);" onclick="uploadInstance.removeAllPicture()"><?php echo JText::_('LNG_DELETE_ALL')?></a>
                            <input type='button' name='btn_removefile' id='btn_removefile' value='x' style='display:none'>
                            <input type='hidden' name='crt_pos' id='crt_pos' value=''>
                            <input type='hidden' name='crt_path' id='crt_path' value=''>

                            <div class="jupload" id="pictures-list">
                                <div class="jupload-header">
                                    <div class="jupload-header-title"></div>
                                    <div class="jupload-header-desc"></div>
                                </div>
                                <div class="jupload-body">
                                    <ul id="sortable" class="jbd-item-list">
                                        <?php
                                        if (!empty($this->pictures)) {
                                            foreach ($this->pictures as $picture ) { ?>
                                                <li class="jbd-item" id="jbd-item-<?php echo $picture['id'] ?>">
                                                    <div class="jupload-files">
                                                        <div class="jupload-files-img">
                                                            <img src='<?php echo BD_PICTURES_PATH.$picture['picture_path']?>'>
                                                        </div>
                                                        <div class="jupload-files-info">
                                                            <div class="jupload-filename">
                                                                <p><?php echo substr(basename($picture['picture_path']),0,30)?></p>
                                                                <input id="jupload-filename-<?php echo $picture['id'] ?>" type="text"
                                                                       name="picture_info[]" value="<?php echo $picture['picture_info']?>">
                                                            </div>
                                                            <div class="jupload-actions jbd-item-actions">
                                                                <label for="jupload-filename-<?php echo $picture['id'] ?>">
                                                                    <i class="la la-pencil"></i>
                                                                </label>

                                                                <input type="hidden" name="picture_enable[]" id="picture_enable_<?php echo $picture['id'] ?>" value="<?php echo $picture['picture_enable'] ?>" />
                                                                <input type='hidden' name='picture_path[]' id='picture_path_<?php echo $picture['id'] ?>' value='<?php echo $this->escape($picture['picture_path'])?>' />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                <?php
                                            }
                                        } ?>
                                    </ul>

                                    <div class="dropzone dropzone-previews container-fluid" id="file-upload">
                                        <div id="actions" style="margin-left:-15px;" class="row">
                                            <div class="col d-flex justify-content-center">
                                                <!-- The fileinput-button span is used to style the file input field as button -->
                                                <span class="btn btn-success fileinput-button dz-clickable mr-1">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            <span><?php echo JText::_('LNG_ADD_FILES'); ?></span>
                                                        </span>
                                                <button  class="btn btn-primary start" id="submitAll">
                                                    <i class="glyphicon glyphicon-upload"></i>
                                                    <span><?php echo JText::_('LNG_UPLOAD_ALL'); ?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5 ml-lg-auto">
                <div class="metainfo-container">
                    <fieldset class="boxed approved-label">
                        <div class="form-container">
                            <div ><label id="approved" for="approved" title=""><?php echo JText::_('LNG_APPROVED'); ?></label></div>
                            <div class="form-group">
                                <fieldset id="approved" class="radio btn-group btn-group-yesno">
                                    <label class="btn" id="label_approved1" for="approved1"><?php echo JTEXT::_("LNG_APPROVED")?></label>
                                    <input type="radio" class="" onclick="" name="approved" id="approved1" value="<?php echo REVIEW_STATUS_APPROVED  ?>" <?php echo $this->item->approved==REVIEW_STATUS_APPROVED? 'checked="checked"' :""?> />
                                    <input type="radio" class="" onclick="" name="approved" id="approved2" value="<?php echo REVIEW_STATUS_DISAPPROVED  ?>"  <?php echo $this->item->approved==REVIEW_STATUS_DISAPPROVED? 'checked="checked"' :""?> />
                                    <label class="btn btn-danger" id="label_approved2" for="approved2"><?php echo JText::_('LNG_DISAPPROVED')?></label>
                                </fieldset>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="id" value="<?php echo $this->item->id ?>"/>

        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>

<?php JBusinessUtil::loadUploadScript(false, true); ?>

<script>
    var maxPicturesReview = '<?php echo isset($this->appSettings->max_review_images)?$this->appSettings->max_review_images:6; ?>';
	var reviewFolder = '<?php echo REVIEW_BD_PICTURES_PATH.(0)."/" ?>';
	var removePath = '<?php echo JBusinessUtil::getUploadUrl('remove') ?>&_path_type=2&_filename=';

    var uploadInstance;    

    window.addEventListener("load", function () {
        uploadInstance = JBDUploadHelper.getUploadInstance({
            'maxPictures': maxPicturesReview,
            'removePath': removePath,
            'setIsBack': true
        });

        uploadInstance.checkNumberOfPictures();
		jQuery( "#sortable" ).sortable();
		jQuery( "#sortable" ).disableSelection();
        uploadInstance.imageUploaderDropzone("#file-upload", '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_GALLERY?>&_path_type=1&_target=<?php echo urlencode(REVIEW_BD_PICTURES_PATH.(0)."/")?>',".fileinput-button","<?php echo JText::_('LNG_DRAG_N_DROP',true); ?>", reviewFolder , <?php echo $allowedNr ?> ,"addPicture");
        uploadInstance.btn_removefile();

        jQuery('#pictures-list').jbdList({
            statusCallback: uploadInstance.changePictureStatus,
            deleteCallback: uploadInstance.deletePicture,
            statusSelector: 'picture_enable_',
            deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE') ?>"
        });
    });

</script>