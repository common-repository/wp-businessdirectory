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
$saveOrder	= $listOrder == 'c.ordering';
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task)
	{
		if (task != 'countries.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_COUNTRIES_CONFIRM_DELETE', true);?>'))
		{
			JBD.submitform(task);
		}
	}
});
</script>
<div id="jbd-container" class="jbd-container">
<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=countries');?>" method="post" name="adminForm" id="adminForm">
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
					<th class="jtable-head-row-data">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
					</th>
					<th class="jtable-head-row-data">#</th>
					
					<th class="jtable-head-row-data">
						<?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'c.country_name', $listDirn, $listOrder); ?>
					</th>
					<th class="jtable-head-row-data">
						<?php echo JText::_('LNG_CODE'); ?>
					</th>
					<th class="jtable-head-row-data">
						<?php echo JText::_('LNG_LOGO'); ?>
					</th>
					<th  class="jtable-head-row-data">
						<?php echo JText::_('LNG_DESCRIPTION'); ?>
					</th>
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
			<tbody class="jtable-body">
			<?php $nrcrt = 1; $count = count($this->items); ?>
			<?php foreach ($this->items as $i => $item) :
				$ordering  = ($listOrder == 'c.ordering');
				$canCreate = true;
				$canEdit   = true;
				$canChange = true;
				?>
				<tr class="jtable-body-row <?php echo $i % 2; ?>">
					<td class="jtable-body-row-data">
						<?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
					</td>
					<td class="jtable-body-row-data"><?php echo $nrcrt++?></td>
					<td class="jtable-body-row-data">
						<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=country.edit&id='.$item->id);?>">
							<?php echo $this->escape($item->country_name); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->country_name); ?>
						<?php endif; ?>
					</td>
					<td class="jtable-body-row-data">
						<?php echo $item->country_code; ?>
					</td>
					<td class="jtable-body-row-data">
						<?php echo !empty($item->logo)?"<img style='height:50px' src='".BD_PICTURES_PATH.$item->logo."'/>":""; ?>
					</td>
					<td class="jtable-body-row-data">
						<?php echo JBusinessUtil::truncate((string)$item->description,TEXT_LENGTH_LIST_VIEW); ?>
					</td>
					<td class="jtable-body-row-data">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>
	 <?php } ?>
	 <input type="hidden" name="task" value="" /> 
	 <input type="hidden" name="id" value="" />
	 <input type="hidden" name="boxchecked" value="0" />
	 <?php echo JHTML::_( 'form.token' ); ?> 
</form>
</div>