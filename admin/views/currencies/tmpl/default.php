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

use MVC\HTML\HTMLHelper;
use MVC\Layout\LayoutHelper;

?>
<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task)
	{
		if (task != 'currencies.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_CURRENCIES_CONFIRM_DELETE', true);?>'))
		{
			JBD.submitform(task);
		}
	}
});
</script>

<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=currencies');?>" method="post" name="adminForm" id="adminForm">
        <div class="clr clearfix"></div>
        <table class="jtable" id="itemList">
            <thead class="jtable-head">
                <tr class="jtable-head-row">
                    <th class="jtable-head-row-data" width="1%">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
                    <th class="jtable-head-row-data" width="1%">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="jtable-head-row-data-title"> <?php echo JText::_('LNG_ID'); ?></span>
                        </div>
                    </th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_CURRENCY_NAME'); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_CURRENCY_DESCRIPTION'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_SYMBOL'); ?></th>
                </tr>
            </thead>               

            <tbody class="jtable-body">
                <?php $nrcrt = 1; $i=0;
                foreach ($this->items as $item) { ?>
                    <tr class="jtable-body-row">
                        <td class="jtable-body-row-data px-3">
                            <?php echo HTMLHelper::_('jbdgrid.id', $i, $item->currency_id); ?>
                        </td>
                        <td class="jtable-body-row-data"><?php echo $item->currency_id?></td>
                    
                        <td class="jtable-body-row-data">
                            <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=currency.edit&currency_id='. $item->currency_id )?>'
                                title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> <B><?php echo $item->currency_name?>
                            </a>
                        </td>

                        <td class="jtable-body-row-data"><?php echo $item->currency_description?></td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->currency_symbol?>
                        </td>
                    </tr>
                <?php
                $i++;
                } ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="id" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>
