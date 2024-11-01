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
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'reviewabuses.delete' || confirm('<?php echo JText::_("COM_JBUSINESSDIRECTORY_REVIEW_ABUSE_CONFIRM_DELETE", true);?>')) {
			JBD.submitform(task);
		}
	}
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=reviewabuses');?>" method="post" name="adminForm" id="adminForm">
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
                    <th class="jtable-head-row-data" width="1%"></th>
                    <th class="jtable-head-row-data" width="1%">#</th>
                    <th class="jtable-head-row-data" width="1%">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
    				<th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_REVIEW_NAME', 'subject', $listDirn, $listOrder); ?></th>
    				<th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_EMAIL', 'ra.email', $listDirn, $listOrder); ?></th>
    				<th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_DESCRIPTION', 'ra.description', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data" style="text-align: center"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_STATE', 'ra.state', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'ra.id', $listDirn, $listOrder); ?></th>
    			</tr>
    		</thead>
    		<tfoot>
    			<tr>
    				<td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
    			</tr>
    		</tfoot>
    		<tbody class="jtable-body">
    			<?php $nrcrt = 1;$i=0;
    			foreach($this->items as $reviewabuse) { ?>
    				<tr class="jtable-body-row">
                        <td class="jtable-body-row-data">
                            <div class="d-flex align-items-center">
                                <div id="item-status-<?php echo $reviewabuse->id?>" class="jtable-body-status <?php echo $reviewabuse->state == 1?"bg-success":"bg-danger" ?> "></div>
                            </div>
                        </td>
    					<td class="jtable-body-row-data"><?php echo $nrcrt++?></td>
    					<td class="jtable-body-row-data">
                            <?php echo HTMLHelper::_('jbdgrid.id', $i, $reviewabuse->id); ?>
                        </td>
    					<td class="jtable-body-row-data">
    						<a href='<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=reviewabuse.edit&id='.$reviewabuse->id) ?>' title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
    							<b><?php echo $reviewabuse->subject ?></b>
    						</a>	
    					</td>
    					<td class="jtable-body-row-data"><?php echo $reviewabuse->email ?></td>
    					<td class="jtable-body-row-data"><?php echo JBusinessUtil::truncate($reviewabuse->description,TEXT_LENGTH_LIST_VIEW); ?></td>
                        <td class="jtable-body-row-data">
                            <?php echo HTMLHelper::_('jbdgrid.published', $reviewabuse->state, $i, 'reviewabuses.', true, 'cb', true, true, $reviewabuse->id); ?>
                        </td>
    					<td class="jtable-body-row-data"><?php echo $reviewabuse->id ?></td>
    				</tr>
    			<?php $i++;} ?>
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