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

JBusinessUtil::loadJQueryChosen();
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
		if (task == 'reviewcriteria.cancel' || !jbdUtils.validateCmpForm(false, false)) {
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
                            <h2> <?php echo JText::_('LNG_REVIEW_CRITERIA');?></h2>
                            <div class="form-container label-w-100">
                                <div class="form-group">
                                    <div  class="form-detail req"></div>
                                    <label for="subject"><?php echo JText::_('LNG_NAME')?> <?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <?php
                                    if($this->appSettings->enable_multilingual) {
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
                                        $jbdTabs->setOptions($options);
                                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                                        foreach( $this->languages  as $k=>$lng ) {
                                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                            $langContent = isset($this->translations[$lng."_name"])?$this->translations[$lng."_name"]:"";
                                            if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->name;
                                            }
                                            $langContent = $this->escape($langContent);
                                            echo "<input type='text' name='name_$lng' id='name_$lng' class='input_txt form-control validate[required]' value=\"".stripslashes($langContent)."\"  maxLength='77'>";
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else { ?>
                                        <input type="text" name="name" id="name" class="input_txt form-control validate[required]" value="<?php echo $this->escape($this->item->name) ?>"  maxLength="77">
                                    <?php } ?>
                                </div>

                                <?php if($this->appSettings->enable_criteria_category) { ?>
                                    <div class="form-group">
                                        <label for="categories"><?php echo JText::_('LNG_CATEGORY')?> </label>
                                        <select name="categories[]" id="categories" data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" multiple class="chosen-select-categories">
                                            <?php echo JHtml::_('select.options', $this->categoryOptions, 'value', 'text', $this->item->selectedCategories); ?>
                                        </select>
                                        <a href="javascript:jbdUtils.uncheckAllCategories('categories')"><?php echo JText::_("LNG_UNCHECK_ALL")?></a>
                                    </div>
                                <?php } ?>

                                <div class="form-group">
                                    <label for="article_id"><?php echo JText::_('LNG_STATUS')?> </label>
                                    <fieldset id="show_time_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio" class="validate[required]" name="published" id="published1" value="1" <?php echo $this->item->published==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="published1"><?php echo JText::_('LNG_PUBLISHED')?></label>
                                        <input type="radio" class="validate[required]" name="published" id="published0" value="0" <?php echo $this->item->published==0? 'checked="checked"' :""?> />
                                        <label class="btn" for="published0"><?php echo JText::_('LNG_UNPUBLISHED')?></label>
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
		<input type="hidden" name="view" id="view" value="reviewcriteria" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>

<script>
    window.addEventListener('load', function(){
        jQuery(".chosen-select-categories").chosen({width:"95%", disable_search_threshold: 5});
    });
</script>