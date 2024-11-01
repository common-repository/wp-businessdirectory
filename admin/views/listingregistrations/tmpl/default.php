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
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'listingregistrations.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_ITEMS_CONFIRM_DELETE', true);?>'))
		{
			JBD.submitform(task);
		}
	}
});
</script>
<div id="jbd-container" class="jbd-container">
<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=listingregistrations');?>" method="post" name="adminForm" id="adminForm">
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

					<th class="jtable-head-row-data">#</th>

                    <th class="jtable-head-row-data" width="3%">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
					
					<th class="jtable-head-row-data">
						<?php echo HTMLHelper::_('searchtools.sort', 'LNG_MAIN_COMPANY', 'cj.company_id', $listDirn, $listOrder); ?>
					</th>

					<th class="jtable-head-row-data">
						<?php echo HTMLHelper::_('searchtools.sort', 'LNG_JOINING_COMPANY', 'cj.joined_company_id', $listDirn, $listOrder); ?>
					</th>

					<th class="jtable-head-row-data" style="text-align: center">
						<?php echo HTMLHelper::_('searchtools.sort', 'LNG_STATUS', 'cj.approved', $listDirn, $listOrder); ?>
					</th>

					<th class="jtable-head-row-data">
						<?php echo JText::_('LNG_JOINING_OWNER'); ?>
					</th>

                    <th class="jtable-head-row-data">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'cj.id', $listDirn, $listOrder); ?>
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
			<tbody class="jtable-body">
			<?php $nrcrt = 1; $count = count($this->items); ?>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="jtable-body-row <?php echo $i % 2; ?>">

					<td class="jtable-body-row-data">
						<div class="d-flex align-items-center">
							<div id="item-status-<?php echo $item->id ?>" class="jtable-body-status <?php echo $item->approved == LISTING_JOIN_STATUS_APPROVED?"bg-success":"bg-danger" ?> "></div>
						</div>
					</td>

					<td class="jtable-body-row-data"><?php echo $nrcrt++?></td>

                    <td class="jtable-body-row-data px-3">
						<?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
                    </td>

					<td class="jtable-body-row-data">
						<?php echo $this->escape($item->mainCompanyName); ?>
					</td>

					<td class="jtable-body-row-data">
						<?php echo $this->escape($item->joinedCompanyName); ?>
					</td>

					<td class="jtable-body-row-data">
						<?php
						$action = "";
						switch ($item->approved) {
							case LISTING_JOIN_STATUS_DISAPPROVED:
								$action = "aprove";
								break;
							case LISTING_JOIN_STATUS_APPROVED:
								$action = "disaprove";
								break;
						}
						echo HTMLHelper::_('jbdgrid.approve', $action, $item->approved == LISTING_JOIN_STATUS_APPROVED ? 2 : 1, $i, 'listingregistrations.', true, 'cb', true, true, $item->id); ?>
                    </td>

                    <td class="jtable-body-row-data">
						<?php echo $item->userName; ?>
                    </td>

					<td class="jtable-body-row-data">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php } ?>

			</tbody>
		</table>
	 <?php } ?>
	 <input type="hidden" name="task" value="" /> 
	 <input type="hidden" name="id" value="" />
	 <input type="hidden" name="boxchecked" value="0" />
	 <?php echo JHTML::_( 'form.token' ); ?> 
</form>
</div>