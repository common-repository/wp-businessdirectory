<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.

$options = array(
	'onActive'     => 'function(title, description){
                                    description.setStyle("display", "block");
                                    title.addClass("open").removeClass("closed");
                                }',
	'onBackground' => 'function(title, description){
                                    description.setStyle("display", "none");
                                    title.addClass("closed").removeClass("open");
                                }',
	'startOffset'  => 0,  // 0 starts on the first tab, 1 starts the second, etc...
	'useCookie'    => true, // this must not be a string. Don't use quotes.
);

$jbdTabs = new JBDTabs();
$jbdTabs->setOptions($options);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function (task) {
        var defaultLang = "<?php echo JBusinessUtil::getLanguageTag() ?>";

        jQuery("#item-form").validationEngine('detach');
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("click", true, true);
        var tab = ("tab-" + defaultLang);
        if (!(document.getElementsByClassName(tab)[0] === undefined || document.getElementsByClassName(tab)[0] === null))
            document.getElementsByClassName(tab)[0].dispatchEvent(evt);
        if (task == 'companytype.cancel' || !jbdUtils.validateCmpForm(false, false)) {
            JBD.submitform(task, document.getElementById('item-form'));
        }
        jQuery("#item-form").validationEngine('attach');
    }
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
        <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id=' . (int) $this->item->id); ?>"
              method="post" name="adminForm" id="item-form" class="form-horizontal">
            <div class="row">
                <div class="<?php echo isset($isProfile)?"col-12":"col-md-7"?>">
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="boxed">
                                <h2> <?php echo JText::_('LNG_COMPANY_TYPE'); ?></h2>
                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="subject"><?php echo JText::_('LNG_NAME') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY) ?></label>
                                        <?php
                                        if ($this->appSettings->enable_multilingual) {
                                            echo $jbdTabs->startTabSet('tab_group_name');
                                            foreach ($this->languages as $k => $lng) {
                                                echo $jbdTabs->addTab('tab_group_name', 'tab-' . $lng, $k);
                                                $langContent = isset($this->translations[$lng . "_name"]) ? $this->translations[$lng . "_name"] : "";
                                                if ($lng == JBusinessUtil::getLanguageTag() && empty($langContent)) {
                                                    $langContent = $this->item->name;
                                                }
                                                $langContent = $this->escape($langContent);
                                                echo "<input type='text' name='name_$lng' id='name_$lng' class='form-control validate[required]' value=\"" . stripslashes($langContent) . "\"  maxLength='100'>";
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        }
                                        else { ?>
                                            <input type="text" name="name" id="name" class="form-control validate[required]"
                                                   value="<?php echo $this->escape($this->item->name) ?>" maxLength="100">
                                        <?php } ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="only_for_admin"><?php echo JText::_('LNG_SHOW_ONLY_FOR_ADMIN') ?> </label>
                                        <fieldset id="only_for_admin_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio" class="validate[required]" name="only_for_admin" id="only_for_admin1"
                                                   value="1" <?php echo $this->item->only_for_admin == true ? 'checked="checked"' : "" ?> />
                                            <label class="btn" for="only_for_admin1"><?php echo JText::_('LNG_YES') ?></label>
                                            <input type="radio" class="validate[required]" name="only_for_admin" id="only_for_admin0"
                                                   value="0" <?php echo $this->item->only_for_admin == false ? 'checked="checked"' : "" ?> />
                                            <label class="btn" for="only_for_admin0"><?php echo JText::_('LNG_NO') ?></label>
                                        </fieldset>
                                    </div>

                                    <div class="form-group">
                                        <label for="company_view"><?php echo JText::_('LNG_COMPANY_VIEW'); ?></label>
                                        <select name="company_view" id="company_view_fld" class="form-control input-medium chosen-select">
                                            <option value="0"><?php echo JText::_("LNG_DEFAULT") ?></option>
                                            <?php  foreach( $this->item->companyViews as $key=>$companyView){?>
                                                <option value="<?php echo $key ?>"<?php echo $key == $this->item->company_view ? "selected":"" ; ?>><?php echo JText::_($companyView)  ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="name"><?php echo JText::_('LNG_ID') ?> </label>
                                        <?php echo $this->item->id ?>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName() ?>"/>
            <input type="hidden" name="task" id="task" value=""/>
            <input type="hidden" name="id" value="<?php echo $this->item->id ?>"/>
            <input type="hidden" name="view" id="view" value="companytype"/>
            <?php echo JHTML::_('form.token'); ?>
        </form>
</div>
<script>
    window.addEventListener("load", function () {
        jQuery(".chosen-select").chosen({width: "95%",disable_search_threshold:-1,allow_single_deselect: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
    });
</script>