<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');

$user = JBusinessUtil::getUser();

JBusinessUtil::includeValidation();
$app = JFactory::getApplication();
$data = $app->getUserState("com_jbusinessdirectory.add.review.data");

$allowedNr = isset($this->appSettings->max_review_images)?$this->appSettings->max_review_images:6;
$allowedNr=($allowedNr<0)?0:$allowedNr;

// used for js purposes
$includeReviewCriterias = !empty($this->reviewCriterias)?1:0;
?>

<div id="add-review" class="jbd-container jbd-edit-container">
	<a id="reviews"></a>
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=companies'.$menuItemId); ?>" method="post" name="addReview" id="addReview">
		<h3 class="title size-3">
			<?php echo JText::_('LNG_WRITE_A_REVIEW') ?>
		</h3>
		
		<div class="add-review" >
            <fieldset>
                <div class="form-container">

                    <div class="row mb-3">
                        <div class="col-12">
                            <?php if(!empty($this->reviewCriterias)){?>
                                <?php foreach($this->reviewCriterias as $reviewCriteria){?>
                                <div class="form-item">
                                    <label for="user_rating"><?php echo $reviewCriteria->name ?></label><div class="rating-criteria"></div>
                                    <input type="hidden" class="review-criterias" name="criteria-<?php echo $reviewCriteria->id ?>" id="criteria-<?php echo $reviewCriteria->name ?>" value="">
                                </div>
                                <?php }?>
                            <?php }else{?>
                                <?php if($this->appSettings->enable_ratings) { ?>
                                    <div class="form-item mb-3">
                                        <label for="rating"><?php echo JText::_('LNG_REVIEW_RATING_TEXT') ?></label>
                                        <div class="rating-criteria"></div>
                                        <input type="text" name="rating" id="rating" value="" class="validate[required]" style=" visibility: hidden; min-height:0; height: 0; width: 100px">
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name"><?php echo JText::_('LNG_NAME')?> </label>
                                <input type="text"	name="name" id="name" class="input_txt form-control text-input validate[required]" value="<?php echo $user->ID>0?$this->escape($user->name):""?>" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"><?php echo JText::_('LNG_EMAIL')?> </label>
                                <input type="text"	name="email" id="email" class="input_txt form-control text-input validate[required,custom[email]]" value="<?php echo $user->ID>0?$this->escape($user->email):""?>" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="subject"><?php echo JText::_('LNG_NAME_YOUR_REVIEW')?> </label>
                                <input type="text" name="subject" id="subject" class="input_txt form-control text-input validate[required]" value="<?php echo $this->escape(isset($data["subject"])?$data["subject"]:"") ?>" >
                            </div>
                        </div>
                    </div>

                    <?php if(empty($this->reviewQuestions)){?>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description"><?php echo JText::_('LNG_REVIEW_DESCRIPTION_TXT')?></label>
                                    <textarea rows="10" name="description" id="description" class="input_txt form-control text-input validate[required]" ><?php echo  $this->escape(isset($data["description"])?$data["description"]:"") ?></textarea><br>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(!empty($this->reviewQuestions))
                        require_once 'review_questions.php';
                    ?>

                    <?php if($allowedNr!=0) { ?>
                        <div class="form-item">
                            <label><?php echo JText::_('LNG_ADD_REVIEW_IMAGE_TEXT')?>:</label>
                            <p class="small"><?php echo JText::_('LNG_ADD_REVIEW_NOTICE')?></p>
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

                                    </ul>

                                    <div class="dropzone dropzone-previews" id="file-upload">
                                        <div id="actions" style="margin-left:-15px;" class="row">
                                            <div class="col-lg-12">
                                                <!-- The fileinput-button span is used to style the file input field as button -->
                                                <span class="btn btn-success fileinput-button dz-clickable">
                                                    <span><?php echo JText::_('LNG_ADD_FILES'); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php echo JBusinessUtil::renderTermsAndConditions('reviews'); ?>

                    <?php if($this->appSettings->captcha){?>
                        <div class="form-item">
                            <?php
                            $namespace="jbusinessdirectory.contact";
                            $class=" required";

                            $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                            if(!empty($captcha)){
                                echo $captcha->display("captcha", "captcha-div-review", $class);
                            }
                            ?>
                        </div>
                    <?php } ?>
                    <div class="clearfix clear-left">
                        <div class="pt-3">
                            <button type="button" class="btn btn-success" onclick="jbdReviews.saveReview('addReview')">
                                <i class="la la-pencil"></i> <?php echo JText::_("LNG_SAVE_REVIEW")?>
                            </button>
                            <!--button type="button" class="btn btn-dark" onclick="jbdReviews.cancelSubmitReview()">
                                <i class="la la la-close"></i> <?php echo JText::_("LNG_CANCEL_REVIEW")?>
                            </button-->
                        </div>
                    </div>
                </div>
            </fieldset>
		</div>
		<input type="hidden" name="option" value="com_jbusinessdirectory" />
	 	<input type="hidden" name="task" value="companies.saveReview" />
		<input type="hidden" name="review_type" id="review_type" value="<?php echo REVIEW_TYPE_BUSINESS ?>">
		<input type="hidden" name="tabId" id="tabId" value="<?php echo $this->tabId?>" /> 
		<input type="hidden" name="userId" value="<?php echo $user->ID;?> " />
        <input type="hidden" name="itemId" value="<?php echo $this->company->id?>" />
        <input type="hidden" name="itemUserId" value="<?php echo $this->company->userId?>" />
		<input type="hidden" name="ratingId" value="<?php echo isset($this->rating->id)?$this->rating->id:0 ?>" />
	</form>
</div>

<?php JBusinessUtil::loadUploadScript(false, true); ?>

<script>
    var allowedPictures = '<?php echo $allowedNr ?>';
	var reviewFolder = '<?php echo BD_REVIEW_PICTURES_PATH.(0)."/" ?>';
	var removePath = '<?php echo JBusinessUtil::getUploadUrl('remove') ?>&_path_type=2&_filename=';

	var uploadInstance;
	
	window.addEventListener("load", function () {

        uploadInstance = JBDUploadHelper.getUploadInstance({
            'maxPictures': allowedPictures,
            'removePath': removePath
        });

        jQuery('#pictures-list').jbdList({
            statusCallback: uploadInstance.changePictureStatus,
            deleteCallback: uploadInstance.deletePicture,
            statusSelector: 'picture_enable_',
            deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE') ?>"
        });

        jQuery( "#sortable" ).sortable();
		jQuery( "#sortable" ).disableSelection();
        uploadInstance.checkNumberOfPictures();
        uploadInstance.imageUploaderDropzone("#file-upload", '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&_path_type=1&picture_type=<?php echo PICTURE_TYPE_GALLERY?>&_target=<?php echo urlencode(BD_REVIEW_PICTURES_PATH.(0)."/")?>',".fileinput-button","<?php echo JText::_('LNG_DRAG_N_DROP',true); ?>", reviewFolder , <?php echo $allowedNr ?>,"addPicture");

        jbdListings.renderRatingCriteria(<?php echo $includeReviewCriterias ?>, '<?php echo $this->company->id ?>');
        jbdListings.renderRatingQuestions();

        uploadInstance.btn_removefile();
    });
</script>
