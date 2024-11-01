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

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= true;
//$saveOrder	= $listOrder == 'a.ordering';
$saveOrder 	= ($listOrder == 'a.ordering' && strtolower($listDirn) == 'asc');
$saveOrderingUrl = JBusinessUtil::addSorting($saveOrder, $listDirn);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'attributes.delete' || confirm('<?php echo JText::_("COM_JBUSINESS_DIRECTORY_ATTRIBUTES_CONFIRM_DELETE", true);?>')) {
			JBD.submitform(task);
		}
    }
})
</script>
<div id="jbd-container" class="jbd-container">
<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=attributes');?>" method="post" name="adminForm" id="adminForm">
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

	<table class="jtable" id="itemList">
		<thead class="jtable-head">
			<tr class="jtable-head-row">
                <td class="jtable-head-row-data"></td>
                <th class="jtable-head-row-data" width="1%"><?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?></th>
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
                <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'a.name', $listDirn, $listOrder);  ?></th>
                <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_TYPE', 'at.name', $listDirn, $listOrder); ?></th>
				<th class="jtable-head-row-data"><?php echo JText::_('LNG_ATTRIBUTE_TYPE');?></th>
				<th class="jtable-head-row-data"><?php echo JText::_('LNG_GROUP');?></th>
				<th class="jtable-head-row-data"><?php echo JText::_('LNG_MANDATORY');?></th>
				<th class="jtable-head-row-data"><?php echo JText::_('LNG_ONLY_FOR_ADMIN');?></th>
                <th class="jtable-head-row-data"><?php echo JText::_('LNG_SHOW_IN_FRONT');?></th>
                <th class="jtable-head-row-data"><?php echo JText::_('LNG_SHOW_ICONS');?></th>
                <th class="jtable-head-row-data"><?php echo JText::_('LNG_SHOW_NAME');?></th>
				<?php if($this->state->get("filter.attribute_type") == ATTRIBUTE_TYPE_OFFER && $this->appSettings->enable_offer_selling) { ?>
					<th class="jtable-head-row-data"><?php echo JText::_('LNG_USE_FOR_SELLING');?></th>
				<?php } ?>	
                <th class="jtable-head-row-data"><?php echo JText::_('LNG_OPTIONS');?></th>
                <th class="jtable-head-row-data text-center" width="5%"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_STATUS', 'a.status', $listDirn, $listOrder); ?></th>
                <th class="jtable-head-row-data" width="1%"><?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>
            </tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
        <tbody <?php if ($saveOrder) :?> class="jtable-body js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
            <?php $count = count($this->items); ?>
			<?php $nrcrt = 1;
			foreach ($this->items as $i => $item) :
				$ordering  = ($listOrder == 'a.ordering');
				$canCreate = true;
				$canEdit   = true;
				$canChange = true;
				?>

            <tr class="jtable-body-row" data-draggable-group="3"
                onmouseover="this.style.cursor='hand';this.style.cursor='pointer'"
                onmouseout="this.style.cursor='default'">

                <td class="jtable-body-row-data">
                    <div class="d-flex align-items-center">
                        <div id="item-status-<?php echo $item->id?>" class="jtable-body-status <?php echo $item->status == 1 ? "bg-success" : "bg-danger" ?> "></div>
                    </div>
                </td>

                <td class="jtable-body-row-data">
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
                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo !empty($item->ordering)?$item->ordering : $nrcrt; ?>" />
		            <?php endif; ?>
                </td>

                <td class="jtable-body-row-data px-3">
		            <?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
                </td>

                <td class="jtable-body-row-data"><?php echo $nrcrt++?></td>

                <td class="jtable-body-row-data jtable-body-name">
                    <span class="jtable-body-row-data-title">
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=attribute.edit&id='.$item->id);?>">
                                <?php echo $this->escape($item->name); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $this->escape($item->name); ?>
                        <?php endif; ?>
                    </span>
                </td>

                <td class="jtable-body-row-data">
		            <?php
		            if ($item->attribute_type == 1)
			            echo JText::_('LNG_COMPANY');
		            if ($item->attribute_type == 2)
			            echo JText::_('LNG_OFFER');
		            if ($item->attribute_type == 3)
                        echo JText::_('LNG_EVENT');
                    if ($item->attribute_type == 4)
			            echo JText::_('LNG_VIDEO');
		            ?>
                </td>

                <td class="jtable-body-row-data">
		            <?php echo $item->attributeTypeName ?>
                </td>
                
                 <td class="jtable-body-row-data">
		            <?php echo $item->group ?>
                </td>
 				
 				<td class="jtable-body-row-data">
		            <?php echo $item->is_mandatory? JText::_('LNG_YES'):JText::_('LNG_NO')?>
                </td>

 				<td class="jtable-body-row-data">
		            <?php echo $item->only_for_admin? JText::_('LNG_YES'):JText::_('LNG_NO')?>
                </td>

                <td class="jtable-body-row-data">
		            <?php echo $item->show_in_front? JText::_('LNG_YES'):JText::_('LNG_NO')?>
                </td>

                <td class="jtable-body-row-data">
		            <?php echo $item->show_icon? JText::_('LNG_YES'):JText::_('LNG_NO')?>
                </td>

                <td class="jtable-body-row-data">
		            <?php echo $item->show_name? JText::_('LNG_YES'):JText::_('LNG_NO')?>
                </td>

				<?php if($item->attribute_type == ATTRIBUTE_TYPE_OFFER && $this->appSettings->enable_offer_selling) { ?>
					<td class="jtable-body-row-data">
			            <?php echo $item->use_attribute_for_selling? JText::_('LNG_YES'):JText::_('LNG_NO')?>
	                </td>
				<?php } ?>
				
                <td class="jtable-body-row-data">
                    <?php echo  $this->escape($item->options);?>
                </td>

                <td class="jtable-body-row-data">
		            <?php echo HTMLHelper::_('jbdgrid.published', $item->status, $i, 'attributes.', true, 'cb', true, true, $item->id); ?>
                </td>

                <td class="jtable-body-row-data">
                    <span><?php echo (int) $item->id; ?></span>
                </td>
            </tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php } ?>

	<input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>
</div>