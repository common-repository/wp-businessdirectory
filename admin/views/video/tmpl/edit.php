<?php
/**
 * @package    WPBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');


require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';
// Load the tooltip behavior.
JHtml::_('formbehavior.chosen');

$attributeConfig = $this->item->defaultAtrributes;
$video=$this->item;

$options = array(
        'onActive' => 'function(title, description) {
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
        'onBackground' => 'function(title, description) {
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
        'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
        'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<script type="text/javascript">
    window.addEventListener('load', function() {
        JBD.submitbutton = function (task) {

            jQuery("#item-form").validationEngine('detach');
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("click", true, true);
           
            if (task == 'video.cancel' || !jbdUtils.validateCmpForm(true, false)) {
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
                            <h2> <?php echo JText::_('LNG_VIDEO'); ?></h2>
                            <p> <?php echo JText::_('LNG_VIDEOS_INFORMATION_TEXT'); ?></p>
                            <div id="video-details">
                                <div class="form-container label-w-100" id="video-form-box">
                                    <div class="form-group">
                                        <label for="name"><?php echo JText::_('LNG_NAME') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?></label>                                        
                                            <input type="text" name="name" id="name" class="input_txt form-control validate[required]" value="<?php echo $this->escape($video->name) ?>"  maxLength="100">                                        
                                    </div>
                                    <div class="form-group">
                                        <label for="url"><?php echo JText::_('LNG_URL') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?></label>
                                        <input type="text" name="url" id="url"
                                               class="input_txt form-control validate[required]"
                                               value="<?php echo $video->url ?>" maxlength="100">
                                    </div>
                                    <div class="form-group">
                                        <label for="description"><?php echo JText::_('LNG_DESCRIPTION') ?></label>                                        
                                            <textarea name="description" id="description" class="input_txt form-control h-auto"  cols="75" rows="5"  maxLength="250"><?php echo $video->description ?></textarea>                                        
                                    </div>

                                    <?php if ($attributeConfig["category"] != ATTRIBUTE_NOT_SHOW) { ?>
                                        <fieldset class="boxed">
                                            <h2> <?php echo JText::_('LNG_CATEGORIES');?></h2>
                                            <p><?php echo JText::_('LNG_SELECT_CATEGORY');?></p>
                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label for="category"><?php echo JText::_('LNG_CATEGORY');?> <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                                        <select name="selectedSubcategories[]" id="selectedSubcategories" data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="form-control input-medium chosen-select-categories" multiple>
                                                            <?php echo JHtml::_('select.options', $this->categoryOptions, 'value', 'text', $this->item->selCats);?>
                                                        </select>
                                                    <a href="javascript:jbdUtils.uncheckAllCategories('mainSubcategory', 'selectedSubcategories')"><?php echo JText::_("LNG_UNCHECK_ALL")?></a>
                                                </div>
                                                <div class="form-group">
                                                    <?php if($attributeConfig["category"] == ATTRIBUTE_MANDATORY){?>
                                                        <div  class="form-detail req"></div>
                                                    <?php }?>
                                                    <label for="subcat_main_id"><?php echo JText::_('LNG_MAIN_SUBCATEGORY');?>  <?php echo JBusinessUtil::showMandatory($attributeConfig["category"]) ?></label>
                                                    <select data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" class="form-control select <?php echo $attributeConfig["category"] == ATTRIBUTE_MANDATORY?"validate[required]":""?>" name="main_subcategory" id="mainSubcategory" onchange="jbdListings.updateAttributes(this.value, <?php echo $this->item->id ?>)" ?>>
                                                        <?php foreach( $this->item->selectedCategories as $selectedCategory){?>
                                                            <option value="<?php echo $selectedCategory->id ?>" <?php echo $selectedCategory->id == $this->item->main_subcategory ? "selected":"" ; ?>><?php echo $selectedCategory->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="error_msg" id="frmMainSubcategory_error_msg" style="display: none;"><?php echo JText::_('LNG_REQUIRED_FIELD')?></span>
                                                </div>
                                            </div>                                        
                                        </fieldset>
                                    <?php }?>

                                    <input type="hidden" name="id" id="id" value="<?php echo $video->id ?>"/>
                                    <hr/>
                                </div>
                            </span>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="id" value="<?php echo $video->id ?>" />
        <input type="hidden" name="view" id="view" value="video" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>

<script>

let maxCategories = <?php echo isset($this->item->package)?$this->item->package->max_categories :$this->appSettings->max_categories ?>;

window.addEventListener('load', function() {
    jQuery("#item-form").validationEngine('attach');

    jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5,search_contains: true, placeholder_text_single: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>" , placeholder_text_multiple: "<?php echo JText::_('LNG_SELECT_OPTION')  ?>"});
    jQuery(".chosen-select-categories").chosen({width:"95%", max_selected_options: maxCategories, search_contains: true});

    jQuery('select#selectedSubcategories').on('change', function() {
        var selected = jQuery('#mainSubcategory option:selected').val();
        console.debug(selected);
        jQuery('select#mainSubcategory').find('option').remove();
        jQuery('select#selectedSubcategories option:selected').each(function () {
            if (jQuery(this).length) {
                var selCategoryOption = jQuery(this).clone();
                selCategoryOption.removeAttr('selected');
                jQuery('select#mainSubcategory').append(selCategoryOption);
                if(selCategoryOption.val() === selected) {
                    jQuery('select#mainSubcategory').find('option').attr('selected', 'selected');
                }
                jbdUtils.updateChosenSelect('select#mainSubcategory');
            }
        });

        var catId = jQuery('#mainSubcategory option:selected').val();
        if(catId === 0 || typeof catId === "undefined" ) {
            catId = -1;
        }
        
        <?php if($this->appSettings->enable_attribute_category) { ?>
            jbdListings.updateAttributes(catId, '<?php echo $this->item->id ?>');
        <?php } ?>
    });
})
</script>