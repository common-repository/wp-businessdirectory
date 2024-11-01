<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$menuItemId = JBusinessUtil::getActiveMenuItem();

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = base64_encode($base_url . $_SERVER["REQUEST_URI"]);

JBusinessUtil::checkPermissions("directory.access.reviews", "managereviews");

$isProfile = true;
$filterType = $this->state->get('filter.type_id');
?>
<script>
    var isProfile = true;
</script>
<style>
    #header-box, #control-panel-link {
        display: none;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managereviews'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">
	<?php if(empty($this->items) && empty($filterType)) {
		echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_REVIEW"), JText::_("LNG_REVIEWS"));
	?>
	</form> <?php return; } ?>

	<div class="row">
		<div class="col-lg-4">
            <select name="filter_type_id" id="filter_type_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('LNG_JOPTION_ALL_REVIEWS');?></option>
                <?php echo JHtml::_('select.options', $this->types, 'value', 'text', $filterType);?>
            </select>
		</div>
	</div>

    <?php if (empty($this->items)) { ?>
        <div style="margin: 20px 0;" class="alert alert-warning">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php } else { ?>
        <?php if(!empty($this->items)) { ?>
            <?php foreach($this->items as $i=>$item) {?>
                <div class="row">
                    <div class="col-12">
                        <div class="jitem-card card-shadow card-plain card-round icon">
                            <div class="jitem-icon">
                                <i class="la la-user"></i>
                            </div>
                            <div class="jitem-wrapper">
                                <div class="jitem-header">
                                    <div class="jitem-title">
                                        <?php echo $item->name ?>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="jitem-header-rating mr-2">
                                            <p class="rating-average" title="<?php echo $item->rating ?>"></p>
                                        </div>
                                        <?php echo $item->rating; ?> | <?php echo JBusinessUtil::convertTimestampToAgo($item->creationDate) ?> | <?php echo $item->listingName; ?> | <?php echo JText::_("LNG_LIKES")." ".$item->likeCount ?> |  <?php echo JText::_("LNG_DISLIKES")." ". $item->dislikeCount ?>
                                    </div>
                                </div>
                                <div class="jitem-body">
                                    <div class="jitem-title text-bold">
                                        <span><?php echo $item->subject; ?></span>
                                    </div>
                                    <div class="jitem-desc">
                                        <?php echo $item->description; ?>
                                    </div>
                                </div>
                                
                                <?php if (empty($item->review_response)){ ?>
                                    <a class="btn btn-outline-primary" href="javascript:jbdReviews.respondToReview(0,'<?php echo $item->id?>')"><?php echo JText::_('LNG_REPLY') ?></a>
                                <?php }else{ ?>
                                    <div class="quote text-right">
                                        <div><strong><?php echo JText::_("LNG_RESPONSE") ?></strong></div>
                                        <?php echo $item->review_response ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
        <?php echo $this->pagination->getListFooter(); ?>
        <div class="clear"></div>
    </div>
    <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="type" id="type" value="<?php echo $filterType ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>

<div id="new-review-response" class="jbd-container" style="display:none">
    <form id="reviewResponseFrm" name ="reviewResponseFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=managereviews'.$menuItemId) ?>" method="post">
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
                <input type='hidden' name='task' id="task" value='companies.saveReviewResponse'/>
                <input type='hidden' name='view' value='managereviews' />
                <input type='hidden' name='redirect' value='<?php echo $url ?>' />
                <input type="hidden" id="reviewId" name="reviewId" value="" />
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


<script>
    window.addEventListener('load', function() {
        jbdListings.renderListAverageRating();
    });
</script>