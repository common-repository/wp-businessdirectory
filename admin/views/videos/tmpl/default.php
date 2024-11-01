<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

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
$saveOrder	= $listOrder == 'v.ordering';
JBusinessUtil::addSorting($saveOrder, $listDirn);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function(task) {
        if (task != 'videos.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>')) {
            JBD.submitform(task);
        }
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=videos');?>" method="post" name="adminForm" id="adminForm">
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
                        <td class="jtable-head-row-data" width="3%"></td>
                        <th class="jtable-head-row-data" width="3%">
                            <?php echo JHtml::_('searchtools.sort', '', 'v.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
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
                                <span class="jtable-head-row-data-title"> # </span>
                            </div>
                        </th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'v.name', $listDirn, $listOrder); ?></th>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_CATEGORY'); ?></th>
                        <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_URL'); ?></th>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'v.id', $listDirn, $listOrder); ?></th>
                    </tr>
                </thead>
            <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody <?php if ($saveOrder) {?> class="js-draggable jtable-body" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php }else{echo "class='jtable-body'"; }; ?>>
            <?php $nrcrt = 1; $i=0;
            $count = count($this->items);
            foreach($this->items as $item) {
            	$ordering  = ($listOrder == 'v.ordering');
            	$canCreate  = true;
            	$canEdit    = true;
            	$canChange  = true;
                ?>
                <TR class="jtable-body-row">
                    <td class="jtable-body-row-data">
                        <div class="d-flex align-items-center">
                            <div id="item-status-<?php echo $item->id?>" class="jtable-body-status bg-success"></div>
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
                                        <i class="icon-menu"></i>
                                    </span>
                        <?php if ($canChange && $saveOrder) : ?>
                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" />
                        <?php endif; ?>
                    </td>

                    <td class="jtable-body-row-data px-3">
                        <?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
                    </td>

                    <td class="jtable-body-row-data" style="text-align: center"><?php echo $nrcrt++?></td>
    
                    <TD class="jtable-body-row-data">
                        <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=video.edit&id='. $item->id )?>'
                                title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>"> <B><?php echo $item->name?>
                            </B>
                        </a>
                    </TD>
    
                    <td class="jtable-body-row-data">
                        <?php echo $item->category_name ?>
                    </td>
    
                    <TD class="jtable-body-row-data">
                        <a href="<?php echo $item->url ?>" target="_blank"><?php echo $item->url ?></a>
                    </TD>
                    <td class="jtable-body-row-data">
                        <?php echo $item->id?>
                    </td>
                </TR>
                <?php
                $i++;
            } ?>
            </tbody>
        </table>
        <?php } ?>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>