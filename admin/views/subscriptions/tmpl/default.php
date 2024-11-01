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

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	Joomla.submitbutton = function(task) {
		if (task != 'subscriptions.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_SUBSCRIPTIONS_CONFIRM_DELETE', true);?>')) {
			Joomla.submitform(task);
		}
    }
});
</script>

<div id="jbd-container" class="jbd-container contaner-fluid">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=subscriptions');?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
		    <?php
		    // Search tools bar
		    echo
            JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' =>JBusinessUtil::setFilterVisibility($this->state))));
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
                            <span class="jtable-head-row-data-title"> # </span>
                        </div>
                    </th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_SUBSCRIPTION_ID', 'sb.subscription_id', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_COMPANY', 'c.name', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_AMOUNT'); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_CREATED'); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_START_DATE'); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_END_DATE'); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_PROCESSOR_TYPE'); ?></th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_STATE', 'sb.status', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_ACTION'); ?></th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'sb.id', $listDirn, $listOrder); ?></th>
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
                <?php $nrcrt = 1; $i=0;
                foreach ($this->items as $item) { ?>
                    <TR class="jtable-body-row">

                        <td class="jtable-body-row-data">
                            <div class="d-flex align-items-center">
                                <div id="item-status-<?php echo $item->id?>" class="jtable-body-status <?php echo $item->status == 1?"bg-success":"bg-danger" ?> "></div>
                            </div>
                        </td>

                        <td class="jtable-body-row-data"><?php echo $nrcrt++?></td>

                        <td class="jtable-body-row-data">
                            <?php echo $item->subscription_id?>
                        </td>

                        <td class="jtable-body-row-data">
                            <?php if(!empty($item->company_name) && !empty($item->company_id)) { ?>
                                <a target="_blank" href="<?php echo JBusinessUtil::getCompanyDefaultLink($item->company_id) ?>">
                                    <?php echo $item->company_name; ?>
                                </a>
                            <?php } ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php echo $item->amount ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php echo JBusinessUtil::getDateGeneralShortFormat($item->created) ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php echo JBusinessUtil::getDateGeneralShortFormat($item->start_date) ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php echo JBusinessUtil::getDateGeneralShortFormat($item->end_date) ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php echo $item->processor_type ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php
                            switch ($item->status) {
                                case SUBSCRIPTION_STATUS_ACTIVE:
                                    echo '<div class="status-badge badge-success">'.JText::_("LNG_ACTIVE").'</div>';
                                    break;
                                case SUBSCRIPTION_STATUS_CANCELED:
                                    echo '<div class="status-badge badge-warning">'.JText::_("LNG_CANCELED").'</div>';
                                    break;
                                case SUBSCRIPTION_STATUS_INACTIVE:
                                    echo '<div class="status-badge badge-danger">'.JText::_("LNG_INACTIVE").'</div>';
                                    break;
                            } ?>
                        </td>
                        <td class="jtable-body-row-data">
                            <?php echo SubscriptionService::getSubscriptionButton($item, 'subscriptions', true); ?>
                        </td>
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

        <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_( 'form.token' ); ?>

    </form>
</div>

<script>

</script>