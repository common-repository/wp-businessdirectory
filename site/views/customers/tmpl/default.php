<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$user = JBusinessUtil::getUser();
if($user->ID == 0){
    $app = JFactory::getApplication();
    $return = 'index.php?option=com_jbusinessdirectory&view=customers';
    $app->redirect(JBusinessUtil::getLoginUrl($return, false));
}

if(!$this->actions->get('directory.access.customers') && $this->appSettings->front_end_acl){
    $app = JFactory::getApplication();
    $app->enqueueMessage(JText::_("LNG_ACCESS_RESTRICTED"),'warning');
    $app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', false));
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=customers');?>" method="post" name="adminForm" id="adminForm">
    <table class="dir-panel-table" id="itemList">        
        <tbody>
            <tr>
                <td class="jtable-body-row-data" align="left">
                    <div class="row">
                        <div class="col-lg-4">
                            <select name="user_id" id="user_id" class="inputbox input-large">
                                <option value=""><?php echo JText::_('LNG_SELECT_CUSTOMER');?></option>
                                <?php echo JHtml::_('select.options',$this->users, 'value', 'name', '');?>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <button type="button" class="btn btn-dark btn-sm" onClick="if(checkIfUserSelected()){this.form.submit()}">
                                <span class="ui-button-text"><i class="la la-user"></i> <?php echo JText::_("LNG_SWITCH_CUSTOMER")?></span>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
    <input type="hidden" name="task" id="task" value="customers.switchUser" />
    <?php echo JHTML::_('form.token'); ?>
</form>

<script>
    function checkIfUserSelected(){
        var option = jQuery('#user_id').val();
        if (option == ''){
            alert("<?php echo JText::_("LNG_PLEASE_SELECT_AN_OPTION_FIRST")?>");
            return false;
        }
        return true;
    }

</script>