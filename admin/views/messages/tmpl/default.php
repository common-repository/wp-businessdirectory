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

JHtml::_('behavior.multiselect');
 JBusinessUtil::initializeChosen();

use MVC\Factory;
use MVC\HTML\HTMLHelper;
use MVC\Language\Multilanguage;
use MVC\Language\Text;
use MVC\Layout\LayoutHelper;
use MVC\Router\Route;
use MVC\Session\Session;

$filterType = $this->state->get('filter.type');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = true;
$saveOrder = $listOrder == 'cm.id';
?>

<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=messages'); ?>" method="post"
          name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
            <?php
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' => JBusinessUtil::setFilterVisibility($this->state))));
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
                    <td class="jtable-head-row-data"></td>
                    <th class="jtable-head-row-data">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
                    <th class="jtable-head-row-data">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="jtable-head-row-data-title"> # </span>
                        </div>
                    </th>
                    <th class="jtable-head-row-data">
                        <?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'cm.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="jtable-head-row-data">
                        <?php echo HTMLHelper::_('searchtools.sort', 'LNG_EMAIL', 'cm.email', $listDirn, $listOrder); ?>
                    </th>
                    <th class="jtable-head-row-data">
                        <?php echo JText::_('LNG_ITEM_NAME'); ?>
                    </th>
                    <th class="jtable-head-row-data">
                        <?php echo JText::_('LNG_DATE'); ?>
                    </th>
                    <th class="jtable-head-row-data">
                        <?php echo HTMLHelper::_('searchtools.sort', 'LNG_TYPE', 'cm.type', $listDirn, $listOrder); ?>
                    </th>
                    <?php if ($filterType == MESSAGE_TYPE_BUSINESS || empty($filterType)) { ?>
                        <th class="jtable-head-row-data">
                            <?php echo HTMLHelper::_('searchtools.sort', 'LNG_CONTACT_NAME', 'cc.contact_name', $listDirn, $listOrder); ?>
                        </th>
                    <?php } ?>
                    <th class="jtable-head-row-data">
                        <?php echo JText::_('LNG_IP_ADDRESS'); ?>
                    </th>
                    <th class="jtable-head-row-data">
                        <?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'cm.id', $listDirn, $listOrder); ?>
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
                <?php if (!empty($this->items)) : ?>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <tr class="jtable-body-row <?php echo ($item->read) ? 'read-message' : 'unread-message' ?>" id="message-<?php echo $item->id ?>">
                            <td class="jtable-body-row-data">
                                <div class="d-flex align-items-center">
                                </div>
                            </td>
                            <td class="jtable-body-row-data py-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <?php echo HTMLHelper::_('jbdgrid.id', $i, $item->id); ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data">
                                <div class="d-flex align-items-center">
                                    <?php echo $this->pagination->getRowOffset($i); ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')">
                                <div class="d-flex align-items-center">
                                    <?php echo $item->name . " " . $item->surname; ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')" class="hidden-phone">
                                <div class="d-flex align-items-center">
                                    <?php echo $item->email; ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')" class="hidden-phone">
                                <div class="d-flex align-items-center">
                                    <?php
                                    $itemType = '';
                                    switch ($item->type) {
                                        case MESSAGE_TYPE_BUSINESS:
                                            echo $item->companyName;
                                            $itemType = JText::_('LNG_COMPANY');
                                            break;
                                        case MESSAGE_TYPE_OFFER:
                                            echo $item->offerName;
                                            $itemType = JText::_('LNG_OFFER');
                                            break;
                                        case MESSAGE_TYPE_EVENT:
                                            echo $item->eventName;
                                            $itemType = JText::_('LNG_EVENT');
                                            break;
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')" class="hidden-phone">
                                <div class="d-flex align-items-center">
                                    <?php echo JBusinessUtil::getDateGeneralFormatWithTime($item->date); ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')" class="hidden-phone">
                                <div class="d-flex align-items-center">
                                    <?php echo $itemType; ?>
                                </div>
                            </td>
                            <?php if ($filterType == MESSAGE_TYPE_BUSINESS || empty($filterType)) { ?>
                                <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')" class="hidden-phone">
                                    <div class="d-flex align-items-center">
                                        <?php echo $item->contactName ?>
                                        <?php echo !empty($item->contactEmail) ? ' (' . $item->contactEmail . ')' : ''; ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <td class="jtable-body-row-data">
                                <div class="d-flex align-items-center">
                                    <?php echo $item->ip_address; ?>
                                </div>
                            </td>
                            <td class="jtable-body-row-data" onclick="readMessage('<?php echo $item->id ?>')" class="center hidden-phone">
                                <div class="d-flex align-items-center">
                                    <span><?php echo (int)$item->id; ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>

        <?php } ?>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>

<div class="jbd-container" id="message-modal" style="display: none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_MESSAGE') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <h3><?php echo JText::_('LNG_FROM'); ?>:</h3>
            <h4 id="message-name"></h4>
            <p id="message-email"></p>
            <br/>
            <p id="message-message" class="message-text"></p>
        </div>
    </div>
</div>

<script>
    function readMessage(id) {
        jQuery('#message-modal #message-name').html();
        jQuery('#message-modal #message-email').html();
        jQuery('#message-modal #message-message').html();
        <?php foreach ($this->items as $item) { ?>
        var val = '<?php echo $item->id ?>';
        if (id == val) {
            var name = '<?php echo addslashes($item->name) . " " . addslashes($item->surname) ?>';
            jQuery('#message-modal #message-name').html(name);
            var email = '<?php echo $item->email ?>';
            jQuery('#message-modal #message-email').html(email);
            var message = "<?php echo str_replace(array("\n", "\r"), array("<br/>", "\\r"), addslashes($item->message)); ?>";
            jQuery('#message-modal #message-message').html(message);
        }
        <?php } ?>
        jQuery('#message-modal').jbdModal();
    }
</script>