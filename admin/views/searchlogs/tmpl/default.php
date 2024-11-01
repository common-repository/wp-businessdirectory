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
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
        JBD.submitform(task);
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=searchlogs');?>" method="post" name="adminForm" id="adminForm">
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
                        <th class="jtable-head-row-data">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="jtable-head-row-data-title"> # </span>
                            </div>
                        </th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_SEARCHED_ON', 'sla.object_type', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_SEARCH_TYPE', 'sla.item_type', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_DATE', 'sla.date', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_SEARCHED_FOR'); ?></th>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_NUMBER_OF_SEARCHES'); ?></th>
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
                    <?php $nrcrt = 1;$i=0;
                    foreach($this->items as $item) { ?>
                        <TR class="jtable-body-row">
                            <td class="jtable-body-row-data py-3" align="center"><?php echo $nrcrt++?></td>
                            <td class="jtable-body-row-data">
                                
                                <?php echo $item->object_type?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $item->item_type?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo JBusinessUtil::getDateGeneralFormat($item->date) ?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $item->itemName?>
                            </td>
                            <td class="jtable-body-row-data">
                                <?php echo $item->item_count?>
                            </td>
                        </TR>
                    <?php $i++;
                    } ?>
                </tbody>
            </table>
        <?php } ?>

        <input type="hidden" name="option"value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
<?php echo $this->loadTemplate('export'); ?>
