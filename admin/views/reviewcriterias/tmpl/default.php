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

// Load the tooltip behavior.
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
$saveOrder	= $listOrder == 'ct.ordering';

$saveOrderingUrl = JBusinessUtil::addSorting($saveOrder, $listDirn);

?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'reviewcriterias.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>')) {
			JBD.submitform(task);
		}
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=reviewcriterias');?>" method="post" name="adminForm" id="adminForm">
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
            <table class="jtable"  id="itemList">
                <thead class="jtable-head">
                    <tr class="jtable-head-row">
                        <th class="jtable-head-row-data" width="3%"></th>
                        <th class="jtable-head-row-data" width="3%">#</th>
                        <th class="jtable-head-row-data" width="3%">
                            <?php echo JHtml::_('searchtools.sort', '', 'ct.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th class="jtable-head-row-data" width="3%">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="jradio">
                                    <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                    <label for="jradio-2"></label>
                                </div>
                            </div>
                        </th>
                        <th class="jtable-head-row-data">
                            <?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'ct.name', $listDirn, $listOrder); ?>
                        </th>
                        <th class="jtable-head-row-data" style="text-align: center"><?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'ct.published', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data">
                            <?php echo JText::_('JGRID_HEADING_ID'); ?>
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
                <tbody <?php if ($saveOrder) :?> class="jtable-body js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
                    <?php $nrcrt = 1; $count = count($this->items); $i=0; ?>
                    <?php foreach ($this->items as $index => $item) :
                        $ordering  = ($listOrder == 'ct.ordering');
                        $canCreate = true;
                        $canEdit   = true;
                        $canChange = true;
                        ?>
                        <tr class="jtable-body-row" data-draggable-group="3">
                            <td class="jtable-body-row-data">
                                <div class="d-flex align-items-center">
                                    <div id="item-status-<?php echo $item->id?>" class="jtable-body-status <?php echo $item->published == 1?"bg-success":"bg-danger" ?> "></div>
                                </div>
                            </td>
                            <td class="jtable-body-row-data"><?php echo $nrcrt++?></td>
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
                            <td class="jtable-body-row-data">
                                <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=reviewcriteria.edit&id='.$item->id);?>">
                                    <?php echo $this->escape($item->name); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo HTMLHelper::_('jbdgrid.published', $item->published, $i, 'reviewcriterias.', true, 'cb', true, true, $item->id); ?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo (int) $item->id; ?>
                            </td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
        <?php } ?>
    	<input type="hidden" name="task" value="" /> 
    	<input type="hidden" name="id" value="" />
    	<input type="hidden" name="boxchecked" value="0" />
    	<?php echo JHTML::_('form.token'); ?> 
    </form>
</div>