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
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'companies.delete' || confirm('<?php echo JText::_("COM_JBUSINESS_DIRECTORY_OFFERS_CONFIRM_DELETE", true);?>')) {
			JBD.submitform(task);
		}
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=reports');?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
            <?php
            // Search tools bar
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' =>JBusinessUtil::setFilterVisibility($this->state))));
            ?>
        </div>
        <div class="clr clearfix"></div>
        <?php if (empty($this->reports)) { ?>
            <div class="alert alert-warning">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php } else { ?>
        <table class="jtable" id="itemList">
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
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'r.name', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data" ><?php echo JText::_('LNG_DESCRIPTION'); ?></th>
                    <th class="jtable-head-row-data"></th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'r.id', $listDirn, $listOrder); ?></th>
                </tr>
            </thead>
            <tbody class="jtable-body">
                <?php $nrcrt = 1; $i=0;
                foreach($this->reports as $item) { ?>
                    <TR class="jtable-body-row <?php echo $i % 2; ?>">
                        <TD class="jtable-body-row-data"><?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?></TD>
                        <TD class="jtable-body-row-data"><?php echo $nrcrt++?></TD>
                        <TD class="jtable-body-row-data">
                            <a href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=report.edit&id='. $item->id )?>' title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
                                <B><?php echo $item->name?></B>
                            </a>
                        </TD>
                        <TD class="jtable-body-row-data"><?php echo JBusinessUtil::truncate($item->description,TEXT_LENGTH_LIST_VIEW); ?></TD>
                        <td class="jtable-body-row-data" style="text-align:right">
                            <a nowrap="nowrap" class="btn" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=reports&task=reports.generateReport&reportId='. $item->id )?>">
                                <span class="ui-button-text"><?php echo JText::_("LNG_GENERATE_REPORT")?></span>
                            </a>
                        </td>
                        <TD class="jtable-body-row-data"><?php echo $item->id; ?></TD>
                    </TR>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php } ?>

        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="id" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php if(isset($this->report)) { ?> <input type="hidden" name="reportId" value="<?php echo $this->report->report->id; ?>" /> <?php } ?>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
<br/><br/><br/>
<?php if(isset($this->report)) { ?>
	<table class="jtable">
		<?php
		if($this->report->report->type == 1) { ?>
			 <thead class="jtable-head">
				<tr>
					<?php foreach ($this->report->headers as $header) { ?>
						<th class="jtable-head-row-data"><?php echo JText::_($this->conferenceParams[$header]) ?></th>
					<?php } ?>
				</tr>
			 </thead>
			 <tbody class="jtable-body">
				<?php foreach ($this->report->data as $data) { ?>
					<tr>
						<?php foreach ($this->report->headers as $header) { ?>
							<td class="jtable-body-row-data"> <?php echo $data->$header; ?> </td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
        <?php } else if($this->report->report->type == 2) { ?>
			 <thead class="jtable-head">
				<tr>
					<?php foreach ($this->report->headers as $header) { ?>
						<th class="jtable-head-row-data"><?php echo JText::_($this->offerParams[$header]) ?></th>
					<?php } ?>

                    <?php
                    if (!empty($this->report->customHeaders)) {
                        foreach ($this->report->customHeaders as $header) { ?>
                            <th class="jtable-head-row-data"><?php echo $header ?></th>
                        <?php }
                    }?>
				</tr>
			 </thead>
			 <tbody class="jtable-body">
				<?php foreach ($this->report->data as $data) { ?>
					<tr>
						<?php foreach ($this->report->headers as $header) { ?>
							<td class="jtable-body-row-data"> <?php echo $data->$header; ?> </td>
						<?php } ?>
                        
                        <?php
                        if (!empty($this->report->customHeaders)) {
                            foreach ($this->report->customHeaders as $header) { ?>
                                <td class="jtable-body-row-data"><?php echo !empty($data->customAttributes[$header]) ? $data->customAttributes[$header]->value : ""; ?></td>
                            <?php }
                        }?>
					</tr>
				<?php } ?>
			</tbody>
		<?php } else { ?>
			 <thead class="jtable-head">
				<tr>
					<?php foreach ($this->report->headers as $header){ ?>
						<th class="jtable-head-row-data"><?php echo JText::_($this->params[$header]) ?></th>
					<?php } ?>

					<?php
                    if (!empty($this->report->customHeaders)) {
                        foreach ($this->report->customHeaders as $header) { ?>
                            <th class="jtable-head-row-data"><?php echo $header ?></th>
                        <?php }
                    }?>
				</tr>
			</thead>
			<tbody class="jtable-body">
				<?php foreach ($this->report->data as $data) { ?>
					<tr>
						<?php foreach ($this->report->headers as $header) { ?>
							<td class="jtable-body-row-data">
								<?php 
									$param = str_replace("cp.", "", $header);
									echo $data->$param;
								?>
							</td>
						<?php } ?>


						<?php
                        if (!empty($this->report->customHeaders)) {
                            foreach ($this->report->customHeaders as $header) { ?>
                                <td class="jtable-body-row-data"><?php echo !empty($data->customAttributes[$header]) ? $data->customAttributes[$header]->value : ""; ?></td>
                            <?php }
                        }?>
					</tr>
				<?php } ?>
			</tbody>
		<?php } ?>
	</table>
<?php } ?>

<?php echo $this->loadTemplate('export'); ?>