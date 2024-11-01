<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

JBusinessUtil::checkPermissions("directory.access.orders", "manageuserpackageorders");
$isProfile = true;
?>
<script>
    var isProfile = true;
</script>
<style>
    #header-box, #control-panel-link {
        display: none;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=manageuserpackageorders');?>" method="post" name="adminForm" id="adminForm">
    <div id="editcell">

        <?php if(empty($this->items)) {
        echo JBusinessUtil::getNoItemMessageBlock(JText::_("LNG_ORDER"), JText::_("LNG_ORDERS"));
        ?>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="id" id="id" value="" />
        <input type="hidden" name="Itemid" id="Itemid" />
        <?php echo JHTML::_('form.token'); ?>
        </form> <?php return; } ?>

        <?php if (empty($this->items)) { ?>
        <div style="margin: 20px 0;" class="alert alert-warning">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
        <?php } else { ?>
            <table class="dir-panel-table responsive-simple">
                <thead>
                <tr>
                    <th width="6%"><?php echo JText::_('LNG_ORDER_ID'); ?></th>
                    <th width="20%"><?php echo JText::_('LNG_PACKAGE'); ?></th>
                    <th width="10%"><?php echo JText::_('LNG_USER'); ?></th>
                    <th width="10%"><?php echo JText::_('LNG_AMOUNT'); ?></th>
                    <th width="10%"><?php echo JText::_('LNG_START_DATE');  ?></th>
                    <th width="10%"><?php echo JText::_('LNG_END_DATE');  ?></th>
                    <th width="10%"><?php echo JText::_('LNG_PAYMENT_STATUS');  ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $nrcrt = 1;
                if(!empty($this->items)){
                	foreach($this->items as $item) { ?>
                        <tr class="jtable-body-row row<?php echo $nrcrt%2 ?>">
                            <td align="center">
                                <?php echo $item->id; ?>
                            </td>

                            <td>
                               <div><strong><?php echo $item->packageName; ?></strong></div>
                               <div><?php echo $item->order_id; ?></div>
                            </td>

                            <td>
                                <?php echo $item->user_name; ?>
                            </td>

                            <td>
                                <?php echo $item->amount . " " . $item->currency ?>
                            </td>                            

                            <td>
                                <?php echo JBusinessUtil::getDateGeneralShortFormat($item->start_date) ?>
                            </td>

                            <td>
                                <?php echo JBusinessUtil::getDateGeneralShortFormat($item->end_date) ?>
                            </td>

                            <td>
                                <?php
                                switch ($item->state) {
                                    case 0:
                                        echo '<div class="status-badge badge-primary">'.JText::_("LNG_NOT_PAID").'</div>';
                                        break;
                                    case 1:
                                        echo '<div class="status-badge badge-success">'.JText::_("LNG_PAID").'</div>';
                                        break;
                                    case 2:
                                        echo '<div class="status-badge badge-danger">'.JText::_("LNG_CANCELED").'</div>';
                                        break;
                                } ?>
                            </td>                            
                        </tr>
                    <?php }
                }
                ?>
                </tbody>
            </table>
        <?php } ?>

        <div class="pagination" <?php echo $this->pagination->total==0 ? 'style="display:none"':''?>>
            <?php echo $this->pagination->getListFooter(); ?>
            <div class="clear"></div>
        </div>
    </div>
    <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
    <input type="hidden" name="task" id="task" value="" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="Itemid" id="Itemid" />
    <?php echo JHTML::_('form.token'); ?>
</form>
<div class="clear"></div>