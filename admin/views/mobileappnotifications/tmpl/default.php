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
?>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=mobileappconfig');?>" method="post" name="adminForm" id="adminForm">
        <div class="clr clearfix"></div>

        <fieldset class="boxed">
    		 <div class="row">
    		 	<div class="col">
                    <fieldset class="boxed">
                        <h3><?php echo JText::_('LNG_PUSH_NOTIFICATIONS',true); ?></h3>
                        <?php echo !empty($this->mobileAppConfig->firebase_server_key) ? '<p class="">'.JText::_('LNG_PUSH_NOTIFICATIONS_DESC')."</p>"  : '<b><p class="text-danger">'.JText::_('LNG_PUSH_NOTIFICATIONS_NOTICE')."</p></b>"?>

                        <div id="mobile-notification" class="jbd-container" style=" <?php echo empty($this->mobileAppConfig->firebase_server_key) ? 'display: none' : ''?>">    
                            <div id="mobileNotificationForm" name="mobileNotificationForm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">
                                <fieldset>
                                    <div class="form-item">
                                        <label class="font-weight-bold"><?php echo JText::_('LNG_TITLE') ?></label>
                                        <div class="outer_input">
                                            <input type="text" name="notification_title" id="notification_title" class="input_txt  ">
                                            <span class="error_msg" id="frmFirstNameC_error_msg"
                                                    style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                        </div>
                                    </div>

                                    <div class="form-item">
                                        <label class="font-weight-bold"><?php echo JText::_('LNG_CONTACT_TEXT') ?>:</label>
                                        <div class="outer_input">
                                        <textarea rows="5" name="notification_body" id="notification_body"
                                        class="input_txt "></textarea>
                                            <span class="error_msg" id="frmDescriptionC_error_msg"
                                                    style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD') ?></span>
                                        </div>
                                    </div>

                                    <div class="form-item">
                                        <label class="font-weight-bold"><?php echo JText::_('LNG_SEND_TO') ?>:</label>
                                        <div class="outer_input">
                                            <div class="d-flex justify-content-start align-items-center">
                                                <input type="radio" name="notification_type" value="<?php echo NOTIFICATION_TYPE_USERGROUP ?>" checked/>
                                                <label><?php echo JTEXT::_("LNG_REGISTERED_USERS")?></label>
                                            </div>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <input type="radio" name="notification_type" value="<?php echo NOTIFICATION_TYPE_TOPIC ?>" />
                                                <label><?php echo JText::_('LNG_ALL_USERS')?></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="clearfix clear-left">
                                        <div class="button-row ">
                                            <button type="submit" class="btn">
                                                <span class="ui-button-text"><?php echo JText::_("LNG_SEND") ?></span>
                                            </button>
                                        </div>
                                    </div>
                                </fieldset>
                                <input type='hidden' id="notification_topic" name="notification_topic" value='<?php echo NOTIFICATION_TOPIC_GENERAL ?>'/>
                            </div>  
                        </div>
                    
                    </fieldset>
    		 	</div>
    		 	
    		 </div>
    	</fieldset>
        
      

        <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="mobileappnotifications.sendNotifications" />
        <?php echo JHTML::_('form.token'); ?>
    </form>

    <table class="jtable" id="itemList">
            <thead class="jtable-head">
                <tr class="jtable-head-row">
                    <th class="jtable-head-row-data"><?php echo JText::_('LNG_TITLE'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_BODY'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_CONTACTED_USERS'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_NOTIFICATION_TYPE'); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo JText::_('LNG_SENT_AT'); ?></th>
                </tr>
            </thead>               

            <tbody class="jtable-body">
                <?php $nrcrt = 1; $i=0;
                foreach ($this->items as $item) { ?>
                    <tr class="jtable-body-row">
                        <td class="jtable-body-row-data"><?php echo $item->title?></td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->body?>
                        </td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->nr_contacts > 0 ? $item->nr_contacts : 'N/A'?>
                        </td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->type == NOTIFICATION_TYPE_TOPIC ? JText::_('LNG_ALL_USERS') : JText::_('LNG_REGISTERED_USERS') ?>
                        </td>

                        <td class="jtable-body-row-data text-center">
                            <?php echo $item->created ;?>
                        </td>
                    </tr>
                <?php
                $i++;
                } ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
</div>