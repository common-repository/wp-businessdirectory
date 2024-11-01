<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

;
JHtml::_('behavior.multiselect');
JBusinessUtil::initializeChosen();

use MVC\Factory;
use MVC\HTML\HTMLHelper;
use MVC\Language\Multilanguage;
use MVC\Language\Text;


$app		= JFactory::getApplication();
$user		= JBusinessUtil::getUser();
$userId		= $user->get('ID');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$saveOrder 	= ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');

$saveOrderingUrl = JBusinessUtil::addSorting($saveOrder, $listDirn);

$sortFields = $this->getSortFields();

$type = $this->state->get('filter.type');
$jinput = JFactory::getApplication()->input;
if ($jinput->get('type')) {
	$type = $jinput->get('type');
}
?>
<script type="text/javascript">
    window.addEventListener("load", function() {

        JBD.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		}
		else {
			dirn = direction.options[direction.selectedIndex].value;
		}
            JBD.tableOrdering(order, dirn, '');
        };

        JBD.submitbutton = function(task) {
            if (task != 'categories.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>')) {
                JBD.submitform(task);
            }
        }

    })
	
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
		    <?php
		    // Search tools bar
		    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' =>JBusinessUtil::setFilterVisibility($this->state))));
		    ?>
        </div>
        <div class="clr clearfix"></div>

	    <?php if (empty($this->items)) { ?>
            <div class="alert alert-warning">
			    <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
	    <?php } else { ?>
                <table class="jtable" id="categoryList">
                    <thead class="jtable-head">
                        <tr class="jtable-head-row">
                            <td class="jtable-head-row-data"></td>

                            <th class="jtable-head-row-data" width="3%">
                                <?php echo JHtml::_('searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                            </th>

                            <th class="jtable-head-row-data" width="3%">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="jradio">
                                        <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                        <label for="jradio-2"></label>
                                    </div>
                                </div>
                            </th>

                            <th class="jtable-head-row-data" width="3%">
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="jtable-head-row-data-title">#</span>
                                </div>
                            </th>

                            <th class="jtable-head-row-data">
                                <?php echo JText::_("LNG_IMAGE"); ?></th>
                            <th class="jtable-head-row-data">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
                            </th>
                            <th class="jtable-head-row-data">
                                <?php echo JText::_('LNG_TYPE'); ?>
                            </th>
                            <th class="jtable-head-row-data">
                                <?php echo JText::_('LNG_ICON'); ?>
                            </th>
                            <th class="jtable-head-row-data">
                                <?php echo JText::_('LNG_COLOR'); ?>
                            </th>
                            
                             <th class="jtable-head-row-data">
                                <?php echo JText::_('LNG_MAP_MARKER'); ?>
                            </th>
                            <th class="jtable-head-row-data text-center">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                            </th>
                            <th class="jtable-head-row-data">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="15">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody <?php if ($saveOrder) {?> class="jtable-body js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php }else{echo "class='jtable-body'"; }; ?>>
                        <?php
                        $nrcrt = 1;
                        foreach ($this->items as $i => $item) : ?>
                            <?php
                            $orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
                            $canEdit    = true;
                            $canEditOwn = true;
                            $canChange  = true;

                            // Get the parents of item for sorting
                            if ($item->level > 1) {
                                $parentsStr = "";
                                $_currentParentId = $item->parent_id;
                                $parentsStr = " " . $_currentParentId;
                                for ($i2 = 0; $i2 < $item->level; $i2++) {
                                    foreach ($this->ordering as $k => $v) {
                                        $v = implode("-", $v);
                                        $v = "-" . $v . "-";
                                        if (strpos($v, "-" . $_currentParentId . "-") !== false) {
                                            $parentsStr .= " " . $k;
                                            $_currentParentId = $k;
                                            break;
                                        }
                                    }
                                }
                            }
                            else {
                                $parentsStr = "";
                            } ?>

                            <tr class="jtable-body-row" data-draggable-group="<?php echo $item->parent_id; ?>" data-item-id="<?php echo $item->id ?>" data-parents="<?php echo $parentsStr ?>" data-level="<?php echo $item->level ?>">
                                <td class="jtable-body-row-data">
                                    <div class="d-flex align-items-center">
                                        <div id="item-status-<?php echo $item->id?>" class="jtable-body-status <?php echo $item->published == 1?"bg-success":"bg-danger" ?> "></div>
                                    </div>
                                </td>

                                <td class="order jtable-body-row-data">
                                    <?php
                                    $iconClass = '';
                                    if (!$canChange) {
                                        $iconClass = ' inactive';
                                    }
                                    elseif (!$saveOrder) {
                                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                                    } ?>
                                    <span class="sortable-handler<?php echo $iconClass ?>">
                                        <i class="la la-ellipsis-v"></i>
                                    </span>
                                    <?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey; ?>" />
                                    <?php endif; ?>
                                </td>

                                <td class="jtable-body-row-data px-3">
                                    <?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
                                </td>

                                <td class="jtable-body-row-data" align="center"><?php echo $nrcrt++?></td>


                                <td class="jtable-body-row-data">
                                    <div class="item-image">
                                        <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=category.edit&id='.$item->id)?>">
                                            <?php if (!empty($item->imageLocation)) { ?>
                                                <img src="<?php echo BD_PICTURES_PATH.$item->imageLocation ?>" class="img-circle"/>
                                                </a>
                                            <?php } else { ?>
                                                <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" class="img-circle" />
                                            <?php } ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="jtable-body-row-data">
                                    <?php
                                    if($item->level > 1){
                                        echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1);
                                    } ?>
                                    <?php if ($canEdit || $canEditOwn) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=category.edit&id=' . $item->id ); ?>">
                                            <?php echo $this->escape($item->name); ?></a>
                                    <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                    <?php endif; ?>
                                    <span class="small">
                                        <?php if (empty($item->note)) : ?>
                                            <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                        <?php else : ?>
                                            <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="jtable-body-row-data">
                                    <?php if($item->type == CATEGORY_TYPE_BUSINESS) { ?>
                                        <?php echo JText::_('LNG_COMPANY'); ?>
                                    <?php } else if($item->type == CATEGORY_TYPE_OFFER) { ?>
                                        <?php echo JText::_('LNG_OFFER'); ?>
                                    <?php } else if($item->type == CATEGORY_TYPE_EVENT) { ?>
                                        <?php echo JText::_('LNG_EVENT'); ?>                                   
                                    <?php } else if($item->type == CATEGORY_TYPE_CONFERENCE) { ?>
                                        <?php echo JText::_('LNG_CONFERENCE'); ?>
                                    <?php } else if($item->type == CATEGORY_TYPE_VIDEO) { ?>
                                        <?php echo JText::_('LNG_VIDEO'); ?>
                                    <?php } else { } ?>
                                </td>
                                <td class="jtable-body-row-data">
                                    <?php if($item->icon) { ?>
                                        <span class="la la-<?php echo $item->icon; ?> la-2x"></span>
                                    <?php } ?>
                                </td>
                                 <td class="jtable-body-row-data">
                                    <?php if($item->color) { ?>
                                        <div style="background-color: <?php echo$item->color ?>; border-radius: 50%; width: 17px; height: 17px"></div>
                                    <?php } ?>
                                </td>
                                <td class="jtable-body-row-data">
                                    <?php if($item->markerLocation) { ?>
                                         <img class="marker-preview" src="<?php echo BD_PICTURES_PATH.$item->markerLocation ?>"/>
                                    <?php } ?>
                                </td>
                                <td class="jtable-body-row-data">
                                    <?php echo HTMLHelper::_('jbdgrid.published', $item->published,  $i, 'categories.', true, 'cb', true, true, $item->id); ?>
                                </td>
                                <td class="jtable-body-row-data">
                                    <span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
                                        <?php echo (int) $item->id; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
         <?php } ?>

        <input type="hidden" name="catType" id="catType" value="" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
<?php // Load the batch processing form. ?>
<?php echo $this->loadTemplate('export'); ?>
<?php echo $this->loadTemplate('import'); ?>
<?php echo $this->loadTemplate('duplicate'); ?>
