<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JBusinessUtil::initializeChosen();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    Joomla.submitbutton = function(task) {
        if (task != 'schedulednotification.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>')) {
            Joomla.submitform(task);
        }
    }
});
</script>

<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=schedulednotification');?>" method="post" name="adminForm" id="adminForm">
        <div class="clr clearfix"></div>
        <table class="jtable" id="itemList">
            <thead class="jtable-head">
                <tr class="jtable-head-row">
                    <th class="jtable-head-row-data" width="1%">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="Joomla.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_NAME'); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_TYPE'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_TITLE'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_BODY'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_FREQUENCY'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_STATUS'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_ID'); ?></th>
                </tr>
            </thead>               

            <tbody class="jtable-body">
                <?php $nrcrt = 1; $i=0;
                foreach ($this->items as $item) { ?>
                    <tr class="jtable-body-row">
                        <td class="jtable-body-row-data px-3">
                            <?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
                        </td>
                    
                        <td class="jtable-body-row-data">
                            <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=schedulednotification.edit&id='. $item->id )?>'
                                title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> <B><?php echo $item->name?>
                            </a>
                        </td>

                        <td class="jtable-body-row-data"><?php echo $item->type?></td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->title?>
                        </td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->body?>
                        </td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->frequency?>
                        </td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->status ;?>
                        </td>
                        
                        <td class="jtable-body-row-data"><?php echo $item->id?></td>
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