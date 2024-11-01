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
	JBD.submitbutton = function(task)
	{
		if (task != 'emailtemplates.delete' || confirm('<?php echo JText::_('ARE_YOU_SURE_YOU_WANT_TO_DELETE', true);?>'))
		{
			JBD.submitform(task);
		}
    }
});
</script>
<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=emailtemplates');?>" method="post" name="adminForm" id="adminForm">
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
                    <td class="jtable-head-row-data" width="1%"></td>
                    <th class="jtable-head-row-data" width="1%">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
                    <th class="jtable-head-row-data" width="1%">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="jtable-head-row-data-title"> # </span>
                        </div>
                    </th>

                    <th class="jtable-head-row-data" width='20%' align='center'><?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'e.email_name', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data" width='20%' align='center'><?php echo HTMLHelper::_('searchtools.sort', 'LNG_TYPE', 'e.email_type', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data" width='20%' align='center'><?php echo HTMLHelper::_('searchtools.sort', 'LNG_SUBJECT', 'e.email_subject', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data" width='30%' align='center'><?php echo JText::_('LNG_CONTENT'); ?></th>
                    <th class="jtable-head-row-data" width='1%' style="text-align:center !important;" align='center'><?php echo JText::_('LNG_STATUS'); ?></th>
                    <th class="jtable-head-row-data" width='1%' align='center'><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'e.email_id', $listDirn, $listOrder); ?></th>
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
    			<?php
    			$nrcrt = 1;
    			$i=0;
    			//if(0)4
    			foreach ($this->items as $email) { ?>
                    <tr class="jtable-body-row">

                        <td class="jtable-body-row-data">
                            <div class="d-flex align-items-center">
                                <div id="item-status-<?php echo $email->email_id ?>" class="jtable-body-status <?php echo $email->status == 1 ? "bg-success" : "bg-danger" ?> "></div>
                            </div>
                        </td>

                        <td class="jtable-body-row-data px-3">
						    <?php echo HTMLHelper::_('jbdgrid.id', $i, $email->email_id); ?>
                        </td>

                        <td class="jtable-body-row-data"><?php echo $nrcrt++; ?></td>

                        <td class="jtable-body-row-data jtable-body-name">
                            <div class="d-flex align-items-center">
                            <span class="ml-3 d-flex flex-column justify-content-center">
                                <span class="jtable-body-row-data-title">
                                    <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=emailtemplate.edit&email_id='. $email->email_id ) ?>"
                                       title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
                                        <b><?php echo $email->email_name ?></b>
                                    </a><br/>
                                </span>
                            </span>
                            </div>
                        </td>

                        <td class="jtable-body-row-data">
	                        <?php echo $email->email_type ?>
                        </td>

                        <td class="jtable-body-row-data">
		                    <?php echo $email->email_subject ?>
                        </td>

                        <td class="jtable-body-row-data">
		                    <?php echo $email->email_content ?>
                        </td>

                        <td class="jtable-body-row-data">
		                    <?php echo HTMLHelper::_('jbdgrid.published', $email->status, $i, 'emailtemplates.', true, 'cb', true, true, $email->email_id); ?>
                        </td>

                        <td class="jtable-body-row-data">
                            <span><?php echo (int) $email->email_id; ?></span>
                        </td>

                    </tr>
    			<?php
    				$i++;
    			}
    			?>
    			</tbody>
    		</table>
    	 <?php } ?>
    	 <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
    	 <input type="hidden" name="task" value="" /> 
    	 <input type="hidden" name="id" value="" />
    	 <input type="hidden" name="boxchecked" value="0" />
    	 <?php echo JHTML::_( 'form.token' ); ?> 

        <?php // Load the batch processing form. ?>
	    <?php echo $this->loadTemplate('batch'); ?>
    </form>
</div>