<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');



use MVC\Factory;
use MVC\HTML\HTMLHelper;
use MVC\Language\Multilanguage;
use MVC\Language\Text;
use MVC\Layout\LayoutHelper;
use MVC\Router\Route;
use MVC\Session\Session;

$user       = JBusinessUtil::getUser();
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$canOrder   = true;
$saveOrder  = $listOrder == 'cm.ordering';
?>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=eventmessages');?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
            <?php
            // Search tools bar
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>
        </div>
        <div class="clr clearfix"></div>
    
        <?php if (empty($this->items)) { ?>
            <div class="alert alert-warning">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php } else { ?>
    
        <table class="table table-striped adminlist"  id="itemList">
            <thead>
            <tr>
                <th width="1%">#</th>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="JBD.checkAll(this)" />
                </th>
                <th width="5%"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'cm.name', $listDirn, $listOrder); ?></th>
                <th width="5%" class="nowrap hidden-phone"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_EMAIL', 'cm.email', $listDirn, $listOrder); ?></th>
                <th width="10%" class="nowrap hidden-phone"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_MESSAGE_EVENT_NAME', 'bc.name', $listDirn, $listOrder); ?></th>
                <th width="25%" class="nowrap hidden-phone"><?php echo JText::_('LNG_MESSAGE') ?></th>
                <th nowrap="nowrap" class="hidden-phone" width="1%"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'cm.id', $listDirn, $listOrder); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
            </tfoot>
            <tbody>
            <?php if(!empty($this->items)) : ?>
                <?php foreach($this->items as $i=>$item) : ?>
                    <tr>
                        <td>
                            <?php echo $this->pagination->getRowOffset($i); ?>
                        </td>
                        <td>
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td>
                            <?php echo $item->name." ".$item->surname; ?>
                        </td>
                        <td class="hidden-phone">
                            <?php echo $item->email; ?>
                        </td>
                        <td class="hidden-phone">
                            <?php echo $item->eventName; ?>
                        </td>
                        <td class="hidden-phone break-word">
                            <?php echo $item->message; ?>
                        </td>
                        <td class="center hidden-phone">
                            <span><?php echo (int) $item->id; ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    
        <?php }?>
    
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
