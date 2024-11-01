<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('formbehavior.chosen', 'select');
$options = array(
        'onActive' => 'function(title, description){
                                        description.setStyle("display", "block");
                                        title.addClass("open").removeClass("closed");
                                    }',
        'onBackground' => 'function(title, description){
                                        description.setStyle("display", "none");
                                        title.addClass("closed").removeClass("open");
                                    }',
        'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
        'useCookie' => true, // this must not be a string. Don't use quotes.
);
$jbdTabs = new JBDTabs();
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function(task) {

        var defaultLang="<?php echo JBusinessUtil::getLanguageTag() ?>";

        jQuery("#item-form").validationEngine('detach');
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("click", true, true);
        var tab = ("tab-"+defaultLang);
        if(!(document.getElementsByClassName(tab)[0] === undefined || document.getElementsByClassName(tab)[0] === null))
            document.getElementsByClassName(tab)[0].dispatchEvent(evt);
        if (task == 'reviewquestion.cancel' || !jbdUtils.validateCmpForm(false, false)) {
            JBD.submitform(task, document.getElementById('item-form'));
        }
        jQuery("#item-form").validationEngine('attach');
    }
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_REVIEW_QUESTION');?></h2>
                            <div class="form-container label-w-100">
                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_NAME')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <?php
                                    if($this->appSettings->enable_multilingual){
                                        $jbdTabs->setOptions($options);
                                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                                        foreach( $this->languages as $k=>$lng ){
                                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                            $langContent = isset($this->translations[$lng."_name"])?$this->translations[$lng."_name"]:"";
                                            if($lng == JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->name;
                                            }
                                            $langContent = $this->escape($langContent);
                                            echo "<input type='text' name='name_$lng' id='name_$lng' class='input_txt form-control validate[required]' value=\"".stripslashes($langContent)."\"  maxLength='255'>";
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else { ?>
                                        <input type="text" name="name" id="name" class="validate[required] form-control input_txt" value="<?php echo $this->escape($this->item->name) ?>" maxlength="255">
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <label for="type"><?php echo JText::_('LNG_TYPE')?> </label>
                                    <select data-placeholder="<?php echo JText::_("LNG_JOPTION_SELECT_TYPE") ?>" class="form-control validate[required] chosen-select" name="type" id="type">
                                        <option value=""><?php echo JText::_("LNG_JOPTION_SELECT_TYPE") ?></option>
                                        <?php echo JHtml::_('select.options', $this->types, 'value', 'text', $this->item->type);?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="category_published"><?php echo JText::_('LNG_STATUS')?> </label>
                                    <fieldset id="show_time_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio" class="validate[required]" name="published" id="published1" value="1" <?php echo $this->item->published==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="published1"><?php echo JText::_('LNG_PUBLISHED')?></label>
                                        <input type="radio" class="validate[required]" name="published" id="published0" value="0" <?php echo $this->item->published==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="published0"><?php echo JText::_('LNG_UNPUBLISHED')?></label>
                                    </fieldset>
                                </div>

                                <div class="form-group">
                                    <label for="category_published"><?php echo JText::_('LNG_MANDATORY')?> </label>
                                    <fieldset id="show_time_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio" class="validate[required]" name="is_mandatory" id="is_mandatory1" value="1" <?php echo $this->item->is_mandatory==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="is_mandatory1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio" class="validate[required]" name="is_mandatory" id="is_mandatory0" value="0" <?php echo $this->item->is_mandatory==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="is_mandatory0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
        <input type="hidden" name="view" id="view" value="reviewquestion" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>