<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

;
JHtml::_('behavior.multiselect');
 JBusinessUtil::initializeChosen();

use MVC\Factory;
use MVC\HTML\HTMLHelper;
use MVC\Language\Multilanguage;
use MVC\Language\Text;
use MVC\Layout\LayoutHelper;
use MVC\Router\Route;
use MVC\Session\Session;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$type = $this->state->get('filter.type_id');

?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'reviews.delete' || confirm('<?php echo JText::_('COM_JBUSINESSDIRECTORY_REVIEW_CONFIRM_DELETE', true);?>')) {
			JBD.submitform(task);
		}
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=reviews');?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
            <?php
            // Search tools bar
            echo
            JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' =>JBusinessUtil::setFilterVisibility($this->state))));
            ?>
        </div>
        <div class="clr clearfix"></div>

        <?php if (empty($this->items)) { ?>
            <div class="alert alert-warning">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php } else { ?>
            <table class="jtable" id="itemList">
                <thead class="jtable-head">
                    <tr class="jtable-head-row">

                        <td class="jtable-head-row-data"></td>
                        <th class="jtable-head-row-data">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="jradio">
                                    <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                    <label for="jradio-2"></label>
                                </div>
                            </div>
                        </th>
                        <th class="jtable-head-row-data">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="jtable-head-row-data-title"> # </span>
                            </div>
                        </th>
                        <th class="jtable-head-row-data">
                            <?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'cr.name', $listDirn, $listOrder); ?>
                        </th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_SUBJECT', 'cr.subject', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_DESCRIPTION'); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_RATING', 'cr.rating', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_EMAIL'); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_LIKE_COUNT', 'cr.likeCount', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_DISLIKE_COUNT', 'cr.dislikeCount', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo $type == REVIEW_TYPE_OFFER?HTMLHelper::_('searchtools.sort', 'LNG_OFFER', 'of.subject', $listDirn, $listOrder):HTMLHelper::_('searchtools.sort', 'LNG_COMPANY', 'bc.name', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_CREATION_DATE', 'cr.creationDate', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_STATE', 'cr.state', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_APROVED', 'cr.approved', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_IP_ADDRESS'); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'cr.id', $listDirn, $listOrder); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="15">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
                <tbody class="jtable-body">
                    <?php $nrcrt = 1; $i=0;
                    foreach($this->items as $review) { ?>
                        <TR class="jtable-body-row">
                            <td class="jtable-body-row-data">
                                <div class="d-flex align-items-center">
                                    <div id="item-status-<?php echo $review->id?>" class="jtable-body-status <?php echo $review->state == 1 && $review->approved==REVIEW_STATUS_APPROVED?"bg-success":"bg-danger" ?> "></div>
                                </div>
                            </td>
                            <td class="jtable-body-row-data px-3">
                                <?php echo HTMLHelper::_('jbdgrid.id', $i, $review->id); ?>
                            </td>
                            <td class="jtable-body-row-data" align="center"><?php echo $nrcrt++?></td>
                            <td class="jtable-body-row-data">
                                <?php echo $review->name?>
                                <?php if(!empty($review->display_name)){ ?>
                                	<span class="jtable-body-row-data-allias"><?php echo JText::_("LNG_USER").": ".$review->display_name?></span>
                                <?php } ?>
                            </td>
                            <td class="jtable-body-row-data">
                                <a href='<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=review.edit&id='. $review->id )?>'
                                    title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
                                    <B><?php echo $review->subject?></B>
                                </a>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo JBusinessUtil::truncate($review->description,TEXT_LENGTH_LIST_VIEW)?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $review->rating?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $review->email?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $review->likeCount?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $review->dislikeCount?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php if($type == REVIEW_TYPE_OFFER) {
                                    if(!empty($review->offerName) && !empty($review->offer_id)) { ?>
                                        <a target="_blank" href="<?php echo JBusinessUtil::getOfferLink($review->offer_id, $review->offer_alias) ?>">
                                            <?php echo $review->offerName; ?>
                                        </a>
                                    <?php } ?>
                                <?php } else { 
                                    if(!empty($review->companyName) && !empty($review->company_id)) { ?>
							            <a target="_blank" href="<?php echo JBusinessUtil::getCompanyDefaultLink($review->company_id) ?>">
								            <?php echo $review->companyName; ?>
							            </a>
						            <?php } ?>
						        <?php } ?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo JBusinessUtil::getDateGeneralFormatWithTime($review->creationDate)?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo HTMLHelper::_('jbdgrid.published', $review->state, $i, 'reviews.', true, 'cb', true, true, $review->id); ?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php
                                $text   = "";
                                $action = "";
                                switch ($review->approved) {
                                    case REVIEW_STATUS_CREATED:
                                        $text   = JText::_("LNG_NEEDS_CREATION_APPROVAL");
                                        $action = "aprove";
                                        break;
                                    case REVIEW_STATUS_DISAPPROVED:
                                        $text   = JText::_("LNG_DISAPPROVED");
                                        $action = "aprove";
                                        break;
                                    case REVIEW_STATUS_APPROVED:
                                        $text   = JText::_("LNG_APPROVED");
                                        $action = "disaprove";
                                        break;
                                }?>
                                <?php if($review->approved == REVIEW_STATUS_CREATED){?>
                                <span><?php echo $text ?></span>
                                <div class="d-flex align-items-center">
                                    <div class="jmaterial-btn-icon-sm approve" onclick="document.location.href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=review.aprove&id='. $review->id )?>'">
                                        <i class="la la-thumbs-up"></i>
                                    </div>

                                    <div class="jmaterial-btn-icon-sm dissaprove bg-danger" onclick="document.location.href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=review.disaprove&id='. $review->id )?>'">
                                        <i class="la la-thumbs-down"></i>
                                    </div>
                                </div>
                                <?php
                                }else {
                                echo HTMLHelper::_('jbdgrid.approve', $action, $review->approved, $i, 'review.', true, 'cb', true, true, $review->id);
                                }?>
                            </td>
                            <td class="jtable-body-row-data">
                                <div class="d-flex align-items-center">
                                    <?php echo $review->ip_address; ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $review->id?>
                            </td>
                        </TR>
                    <?php
                        $i++;
                    } ?>
                </tbody>
            </table>
        <?php } ?>

        <input type="hidden" name="option"value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>

        <?php // Load the batch processing form. ?>
        <?php echo $this->loadTemplate('batch'); ?>
    </form>
</div>