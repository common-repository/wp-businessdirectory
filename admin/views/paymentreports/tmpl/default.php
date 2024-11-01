<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior.
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$filterTypeId = $this->state->get('filter.item_type');

?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function (task) {
        JBD.submitform(task);
        jQuery("#adminForm #task").val("");
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=paymentreports'); ?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
            <?php
            // Search tools bar
            echo
            JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' =>0)));
            ?>
        </div>
        <div class="clr clearfix"></div>

        <div class="payment-info">
            <strong>
                <?php echo JText::_('LNG_TOTAL_PAID') ?>: <?php echo !empty($this->items)?$this->items[0]->totalAmount:"0" ?>
                <?php echo "<br/>" ?>
                <?php echo JText::_('LNG_TOTAL_COMMISSION_PAID') ?>: <?php echo !empty($this->items)?$this->items[0]->totalCommission:"0" ?>
            </strong>
            <div><?php echo JText::_('LNG_DISPLAY_TOTAL_NOTIFICATION') ?></div>
        </div>

        <table class="jtable" id="itemList">
            <thead class="jtable-head">
                <tr class="jtable-head-row">
                	<td class="jtable-head-row-data"></td>
                    <th class="jtable-head-row-data" width="2%">
                      #
                    </th>
                    <?php foreach($this->headers as $header)  { ?>
                        <th class="jtable-head-row-data">
                            <?php echo $header->sort ? JHtml::_('grid.sort', $header->text , $header->field, $listDirn, $listOrder) : $header->text ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody class="jtable-body">
            <?php $count = count($this->items); ?>
            <?php $nrcrt = 1 + $this->pagination->limitstart; ?>
            <?php foreach ($this->items as $i => $item) { ?>
            	<tr class="jtable-body-row" >
                	<td class="jtable-body-row-data">
                    	<div class="d-flex align-items-center">
                        	<div class="jtable-body-status <?php echo $item->payment_status == PAYMENT_STATUS_PAID ?"bg-success":"" ?> <?php echo $item->payment_status == PAYMENT_STATUS_FAILURE || $item->payment_status == PAYMENT_STATUS_CANCELED ?"bg-danger":"" ?> <?php echo $item->payment_status == PAYMENT_STATUS_WAITING || $item->payment_status == PAYMENT_STATUS_PENDING ?"bg-warning":"" ?> "></div>
                        </div>
                    </td>
                
                    <td class="jtable-body-row-data py-3" align='center'><?php echo $nrcrt++ ?></td>
                    <?php foreach($this->headers as $header) { ?>
                        <td class="jtable-body-row-data">
                            <?php echo isset($item->{$header->field})?$item->{$header->field}:""; ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
        </table>

        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>"/>
        <input type="hidden" name="task" id="task" value=""/>
        <input type="hidden" name="id" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>