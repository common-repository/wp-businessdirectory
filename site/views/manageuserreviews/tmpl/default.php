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

JBusinessUtil::checkPermissions("directory.access.reviews", "manageuserreviews");

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

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageuserreviews'.$menuItemId);?>" method="post" name="adminForm" id="adminForm">

    <?php if(empty($this->items) && empty($filterType)) {
        echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_REVIEW"), JText::_("LNG_REVIEWS"));
    ?>
    </form>
    
    <?php
        return;
    } ?>

	<div class="row">
		<div class="col-md-3">
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
                                            <?php echo $item->user_name ?>
                                        </div>
                                        <div class="d-flex">
                                            <div class="jitem-header-rating mr-2">
                                                <i class="la la-star"></i>
                                                <i class="la la-star"></i>
                                                <i class="la la-star"></i>
                                                <i class="la la-star-half-o"></i>
                                                <i class="la la-star"></i>
                                            </div>
                                            <?php echo $item->rating; ?> | <?php echo JBusinessUtil::convertTimestampToAgo($item->creationDate) ?> | <?php echo $item->listingName; ?> | <?php echo $item->likeCount ?> |  <?php echo $item->dislikeCount ?>
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
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
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

<script>

</script>