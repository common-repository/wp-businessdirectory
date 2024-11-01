<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$jbdTabs = new JBDTabs();
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

// Load the tooltip behavior.

JBusinessUtil::includeColorPicker();
//JBusinessUtil::loadJQueryChosen(); 
?>


<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task)
	{
		var defaultLang="<?php echo JBusinessUtil::getLanguageTag() ?>";

		jQuery("#item-form").validationEngine('detach');
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent("click", true, true);
		var tab = ("tab-"+defaultLang);
		if(!(document.getElementsByClassName(tab)[0] === undefined || document.getElementsByClassName(tab)[0] === null))
			document.getElementsByClassName(tab)[0].dispatchEvent(evt);
		if (task == 'attribute.cancel' || !jbdUtils.validateCmpForm()){
			JBD.submitform(task, document.getElementById('item-form'));
		}
		jQuery("#item-form").validationEngine('attach');
    }
})
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=attribute');?>" method="post" name="adminForm" id="item-form">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">

                            <h2> <?php echo JText::_('LNG_ATTRIBUTE_DETAILS'); ?>
                                <?php
                                echo ' ( ';
                                if($this->type == ATTRIBUTE_TYPE_BUSINESS)
                                    echo JTEXT::_('LNG_COMPANY');
                                if($this->type == ATTRIBUTE_TYPE_OFFER)
                                    echo JTEXT::_('LNG_OFFER');
                                if($this->type == ATTRIBUTE_TYPE_EVENT)
                                    echo JTEXT::_('LNG_EVENT');
                                if($this->type == ATTRIBUTE_TYPE_VIDEO)
                                    echo JTEXT::_('LNG_VIDEO');
                                echo ' )';
                                ?></h2>
                            <div class="form-container">

                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_NAME')?> <?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <?php
                                    if($this->appSettings->enable_multilingual) {
                                        $jbdTabs->setOptions($options);
                                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                                        foreach( $this->languages  as $k=>$lng ) {
                                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                            $langContent = isset($this->translations[$lng."_name"])?$this->translations[$lng."_name"]:"";
                                            if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->name;
                                            }
                                            $langContent=$this->escape($langContent);
                                            echo "<input type='text' name='name_$lng' id='name_$lng' class='input_txt form-control validate[required]' value=\"".stripslashes($langContent)."\"  maxLength='50'>";
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else { ?>
                                        <input name="name" id="name" value="<?php echo $this->escape($this->item->name)?>" class='input_txt form-control validate[required]' size="50" type="text" maxlength="255">
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <label for="code"><?php echo JText::_('LNG_CODE')?> </label>
                                    <input type="text"
                                           name="code" id="code-lbl" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->item->code ?>" maxlength="100">
                                </div>

								<div class="form-group">
                                    <label for="group"><?php echo JText::_('LNG_GROUP')?> </label>
                                    <input type="text" name="group" id="group-lbl" class="control-label form-control hasTooltip" data-toggle="tooltip" value="<?php echo $this->item->group ?>" maxlength="100">
                                </div>
                                
                                <div class="form-group" style="display: none;">
                                    <label for="show_in_filter"><?php echo JText::_('LNG_SHOW_IN_FILTER')?> </label>
                                    <fieldset id="show_in_filter_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio" class="validate[required]" name="show_in_filter" id="show_in_filter1" value="1" <?php echo $this->item->show_in_filter==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_in_filter1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio" class="validate[required]" name="show_in_filter" id="show_in_filter0" value="0" <?php echo $this->item->show_in_filter==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="show_in_filter0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="is_mandatory"><?php echo JText::_('LNG_MANDATORY')?> </label>
		                                    <fieldset id="is_mandatory_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="is_mandatory" id="is_mandatory1" value="1" <?php echo $this->item->is_mandatory==true? 'checked="checked"' :""?> />
		                                        <label class="btn" for="is_mandatory1"><?php echo JText::_('LNG_YES')?></label>
		                                        <input type="radio" class="validate[required]" name="is_mandatory" id="is_mandatory0" onclick="removeSellOption();" value="0" <?php echo $this->item->is_mandatory==false? 'checked="checked"' :""?> />
		                                        <label class="btn" for="is_mandatory0"><?php echo JText::_('LNG_NO')?></label>
		                                    </fieldset>
		                                </div>
									</div>
									<div class="col-md-6">
										<?php if($this->type == ATTRIBUTE_TYPE_OFFER && $this->appSettings->enable_offer_selling) { ?>
		                                    <div class="form-group">
		                                        <label for="use_attribute_for_selling"><?php echo JText::_('LNG_USE_FOR_SELLING')?> </label>
		                                        <fieldset id="use_attribute_for_selling_fld" class="radio btn-group btn-group-yesno">
		                                            <input type="radio" class="validate[required]" name="use_attribute_for_selling" id="use_attribute_for_selling1" value="1" <?php echo $this->item->use_attribute_for_selling==true? 'checked="checked"' :""?> />
		                                            <label class="btn" for="use_attribute_for_selling1" onclick="setSellingAttribute();"><?php echo JText::_('LNG_YES')?></label>
		                                            <input type="radio" class="validate[required]" name="use_attribute_for_selling" id="use_attribute_for_selling0" value="0" <?php echo $this->item->use_attribute_for_selling==false? 'checked="checked"' :""?> />
		                                            <label class="btn" for="use_attribute_for_selling0"><?php echo JText::_('LNG_NO')?></label>
		                                        </fieldset>
		                                    </div>
			                            <?php } ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="only_for_admin"><?php echo JText::_('LNG_ONLY_FOR_ADMIN')?> </label>
		                                    <fieldset id="only_for_admin_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="only_for_admin" id="only_for_admin1" onclick="removeSellOption();" value="1" <?php echo $this->item->only_for_admin==true? 'checked="checked"' :""?> />
		                                        <label class="btn" for="only_for_admin1"><?php echo JText::_('LNG_YES')?></label>
		                                        <input type="radio" class="validate[required]" name="only_for_admin" id="only_for_admin0" value="0" <?php echo $this->item->only_for_admin==false? 'checked="checked"' :""?> />
		                                        <label class="btn" for="only_for_admin0"><?php echo JText::_('LNG_NO')?></label>
		                                    </fieldset>
		                                </div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="show_name"><?php echo JText::_('LNG_SHOW_NAME')?> </label>
		                                    <fieldset id="show_name_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="show_name" id="show_name1" value="1" onclick="removeSellOption();" <?php echo $this->item->show_name==true? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_name1"><?php echo JText::_('LNG_YES')?></label>
		                                        <input type="radio" class="validate[required]" name="show_name" id="show_name0" value="0" <?php echo $this->item->show_name==false? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_name0"><?php echo JText::_('LNG_NO')?></label>
		                                    </fieldset>
		                                </div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="show_in_front"><?php echo JText::_('LNG_SHOW_IN_FRONT')?> </label>
		                                    <fieldset id="show_in_front_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="show_in_front" id="show_in_front1" value="1" <?php echo $this->item->show_in_front==true? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_in_front1"><?php echo JText::_('LNG_YES')?></label>
		                                        <input type="radio" class="validate[required]" name="show_in_front" id="show_in_front0" value="0" onclick="removeSellOption();" <?php echo $this->item->show_in_front==false? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_in_front0"><?php echo JText::_('LNG_NO')?></label>
		                                    </fieldset>
		                                </div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="show_icon"><?php echo JText::_('LNG_SHOW_ICONS')?> </label>
		                                    <fieldset id="show_icon_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="show_icon" id="show_icon1" onclick="showColorPanel();removeSellOption();" value="1" <?php echo $this->item->show_icon==true? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_icon1"><?php echo JText::_('LNG_YES')?></label>
		                                        <input type="radio" class="validate[required]" name="show_icon" id="show_icon0" onclick="hideColorPanel()" value="0" <?php echo $this->item->show_icon==false? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_icon0"><?php echo JText::_('LNG_NO')?></label>
		                                    </fieldset>
		                                </div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="state-attr"><?php echo JText::_('LNG_STATE')?> </label>
		                                    <fieldset id="status_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="status" id="status-1" value="1" <?php echo $this->item->status==1? 'checked="checked"' :""?> />
		                                        <label class="btn" for="status-1"><?php echo JText::_('LNG_ACTIVE')?></label>
		                                        <input type="radio" class="validate[required]" name="status" id="status-0" value="0" <?php echo $this->item->status==0? 'checked="checked"' :""?> />
		                                        <label class="btn" for="status-0"><?php echo JText::_('LNG_INACTIVE')?></label>
		                                    </fieldset>
		                                </div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
		                                    <label for="show_in_list_view"><?php echo JText::_('LNG_SHOW_IN_LIST_VIEW')?> </label>
		                                    <fieldset id="show_in_list_view_fld" class="radio btn-group btn-group-yesno">
		                                        <input type="radio" class="validate[required]" name="show_in_list_view" id="show_in_list_view1" value="1" <?php echo $this->item->show_in_list_view==true? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_in_list_view1" onclick="removeSellOption();"><?php echo JText::_('LNG_YES')?></label>
		                                        <input type="radio" class="validate[required]" name="show_in_list_view" id="show_in_list_view0" value="0" <?php echo $this->item->show_in_list_view==false? 'checked="checked"' :""?> />
		                                        <label class="btn" for="show_in_list_view0"><?php echo JText::_('LNG_NO')?></label>
		                                    </fieldset>
		                                </div>
									</div>
								</div>


                                <div class="form-group" id="colorpanel" style="<?php echo $this->item->show_icon?'':'display:none;' ?>">
                                    <label for="color"> <?php echo JText::_('LNG_COLOR')?> </label>
                                    <input type="text" name="color" class="minicolors w-auto" id="colorpicker" value="<?php echo $this->item->color ?>" />
                                    <a href="javascript:clearColor()"><?php echo JText::_("LNG_CLEAR")?></a>
                                </div>

                                <?php if($this->appSettings->enable_attribute_category || $this->type == ATTRIBUTE_TYPE_OFFER) { ?>
                                    <div class="form-group">
                                        <label for="categories"><?php echo JText::_('LNG_CATEGORY')?> </label>
                                        <select name="categories[]" id="categories" data-placeholder="<?php echo JText::_("LNG_SELECT_CAT") ?>" multiple class="chosen-select-categories">
                                            <?php echo JHtml::_('select.options', $this->categoryOptions, 'value', 'text', $this->item->selectedCategories); ?>
                                        </select>
                                        <a href="javascript:jbdUtils.uncheckAllCategories('categories')"><?php echo JText::_("LNG_UNCHECK_ALL")?></a>
                                    </div>
                                <?php } ?>

                                <div class="form-group">
                                    <label for="type_attribute"><?php echo JText::_('LNG_TYPE')?> </label>
                                    <fieldset id="type_attr_fld" class="radio btn-group btn-group-yesno">
                                        <?php foreach($this->attributeTypes as $key=>$value){?>
                                            <input onclick ="doAction(this.value);" type="radio" class="validate[required]"  name="type" id="type_<?php echo $key ?>" value="<?php echo $value->id; ?>" <?php echo (isset($this->item->type) && $this->item->type==$value->id)? 'checked="checked"' :""?> <?php echo (!isset($this->item->type) && $key==0)? 'checked="checked"' :""?> />
                                            <label class="btn" for="type_<?php echo $key ?>"><?php echo $value->name; ?></label>
                                        <?php }?>
                                    </fieldset>
                                </div>

                                <div class="form-group">
                                    <label for="option"><?php echo JText::_('LNG_OPTION_NAME')?> </label>
                                    <div class="controls">
                                        <div 
                                            id="add-attribute"
                                            class="btn btn-success mb-2"
                                            onclick = "addAttributeOption('<?php echo BD_ASSETS_FOLDER_PATH."images/deleteIcon.png" ?>');">
                                            <div class="la la-plus"></div>
                                        </div>
                                        <div class="controls">
                                            <fieldset id="option_fld" class="btn-group w-100">
                                                <input type='hidden' name='crt_pos' id='crt_pos' value=''>
                                                <ul class="m-0 list-unstyled" id="list_feature_options">
                                                    <?php
                                                    if(!empty($this->attributeOptions)) {
                                                        foreach ($this->attributeOptions as $key => $value) { ?>
                                                            <li>
                                                                <div>
                                                                    <div class="input-group py-1">
                                                                        <div class="input-group-prepend">
                                                                            <div class="btn btn-outline-light btn-sm d-flex">
                                                                                <div class="la la-arrows-v text-dark"></div>
                                                                            </div>
                                                                        </div>

                                                                        <input type="text" name="option_name[]" id="option_name"
                                                                           value="<?php echo $this->escape($value->name) ?>" size="32"
                                                                           class='form-control radius-0 m-0 input_txt attribute_option validate[required]'
                                                                           maxlength="128"/>

                                                                        <div class="input-group iconpicker-container">
                                                                            <input name="icon[]" data-placement="bottomRight" class="form-control icp icp-auto iconpicker-element iconpicker-input"
                                                                                value="<?php echo !empty($value->icon) ? $value->icon : 'la la-500px'; ?>" type="hidden">
                                                                            <span class="input-group-addon btn btn-dark border-radius-0 attribute-icon-holder">
                                                                                <i class="<?php echo !empty($value->icon) ? $value->icon : 'la la-500px'; ?>"></i>
                                                                            </span>
                                                                        </div>

                                                                        <div class="input-group-append">
                                                                           <div class="btn btn-info btn-sm d-flex"
                                                                                onclick="clearIcon(this)">
                                                                                <i class="la la-close"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group-append">
                                                                            <div class="btn btn-danger btn-sm d-flex"
                                                                                onclick="var row = jQuery(this).parents('li:first');
                                                                                    var row_idx = row.prevAll().length;
                                                                                    jQuery('#crt_pos').val(row_idx);
                                                                                    deleteAttributeOption(jQuery('#crt_pos').val())">
                                                                                <i class="la la-trash-o"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="option_id[]" id="option_id[]" value="<?php echo $value->id?>" >
                                                                </div>
                                                            </li>
                                                        <?php }
                                                    }else {
                                                        ?>
                                                        <li>
                                                            <div class="input-group py-1">

                                                                <div class="input-group-prepend">
                                                                    <div class="btn btn-outline-light btn-sm d-flex">
                                                                        <i class="la la-arrows-v text-dark"></i>
                                                                    </div>
                                                                </div>

                                                                <input  type="text"
                                                                        name="option_name[]"
                                                                        id="option_name"
                                                                        class='input_txt form-control radius-0 m-0 validate[required] attribute_option'
                                                                        value="" size="32"
                                                                        maxlength="128"/>

                                                                <div class="input-group iconpicker-container">
                                                                    <input name="icon[]" data-placement="bottomRight" class="form-control icp icp-auto iconpicker-element iconpicker-input"
                                                                        value="<?php echo !empty($value->icon) ? $value->icon : 'la la-500px'; ?>" type="hidden">
                                                                    <span class="input-group-addon btn btn-dark border-radius-0 attribute-icon-holder">
                                                                        <i class="<?php echo !empty($value->icon) ? $value->icon : 'la la-500px'; ?>"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="hidden" name="option_id[]" id="option_id" value="" >

                                                                <div class="input-group-append">
                                                                   <div class="btn btn-info btn-sm d-flex"
                                                                        onclick="clearIcon(this)">
                                                                        <i class="la la-close"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="input-group-append">
                                                                    <div class="btn btn-danger btn-sm d-flex"
                                                                        onclick="var row = jQuery(this).parents('li:first');
                                                                            var row_idx = row.prevAll().length;
                                                                            jQuery('#crt_pos').val(row_idx);
                                                                            deleteAttributeOption(jQuery('#crt_pos').val())">
                                                                        <i class="la la-trash-o"></i>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </li>
                                                        <?php
                                                    } ?>
                                                </ul>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="attribute_type" value="<?php echo $this->type;?>"/>
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>

<script>
    var visibility = <?php echo !empty($this->item->show_icon)?$this->item->show_icon:0; ?>;
    window.addEventListener('load', function(){
        var itemId = '<?php echo $this->item->id ?>';
        if (itemId === ''){
            jQuery( "#type_0" ).click();
        }

        jQuery('.icp-auto').iconpicker({
            placement: 'topRightCorner'
        });

        jQuery( "#list_feature_options" ).sortable();
        jQuery( "#list_feature_options" ).disableSelection();

        jQuery("#item-form").validationEngine('attach');

        checkedValue = jQuery('input[name=type]:radio:checked').val();

        if(checkedValue == 1 || checkedValue==6 || checkedValue==7 || checkedValue==5){
            jQuery("#option_name").prop('disabled', true);
            jQuery(".attr-img").hide();
        }

        showIconFields();

        jQuery(".chosen-select-categories").chosen({width:"95%", disable_search_threshold: 5});
    });

    function showColorPanel() {
        jQuery('#colorpanel').show(500);
        visibility = 1;
        showIconFields();
    }

    function hideColorPanel() {
        jQuery('#colorpanel').hide(500);
        visibility = 0;
        showIconFields();
    }

	function clearColor(){
		jQuery("#colorpicker").val("");
		jQuery(".minicolors-swatch").html("");
	}
    
    function showIconFields() {
        var disabled = typeof jQuery('.attribute_option').attr('disabled') !== 'undefined';
        if(visibility === 1 && !disabled)
            jQuery('.attribute_icon_container').show(500);
        else
            jQuery('.attribute_icon_container').hide(500);

    }

    function doAction(value) {
        var disabled = false;
        if (value == 1 || value == 6 || value == 7 || value == 5) {
            disabled = true;
            jQuery("#add-attribute").removeClass("btn-success");
            jQuery("#add-attribute").addClass("btn-secondary");
            jQuery("#add-attribute").attr("disabled", true);
        } if (value == 2 || value == 3 || value == 4 || value == 8){
            jQuery("#add-attribute").removeClass("btn-secondary");
            jQuery("#add-attribute").addClass("btn-success");
            jQuery('#add-attribute').attr("disabled", false);  
        }
        if (value != 2){
            jQuery('#use_attribute_for_selling0').click();
        }
        var optionsTemp = document.getElementsByName("option_name[]");
        for (i = 0; i < optionsTemp.length; ++i) {
            optionsTemp[i].disabled = disabled;
        }

        if (disabled) {
            jQuery(".attr-img").hide();
            jQuery('.attribute_icon_container').hide(500);
        }
        else {
            jQuery(".attr-img").show();
            showIconFields();
        }
    }

    function deleteAttributeOption(pos) {
        var lis=document.querySelectorAll('#list_feature_options li');

        if(lis==null) {
            alert('Undefined List, contact administrator !');
        }

        var count = jQuery('input[name="option_name[]"]').length;
        if(count == 1)
            return false;

        if(pos >= lis.length)
            pos = lis.length-1;
        lis[pos].parentNode.removeChild(lis[pos]);

        //return jQuery('#'+id).remove();// (elem = document.getElementById(id)).remove();
    }

    function clearIcon(elem){
		jQuery(elem).parents('li:first').find('.input-group input:hidden').val("");
		jQuery(elem).parents('li:first').find('span.attribute-icon-holder').html("<i class=\"la la-500px\"></i>");
    }

    function addAttributeOption() {
        var attrType = document.getElementsByName("type");
        if (attrType[0].checked || attrType[4].checked || attrType[5].checked || attrType[6].checked) {            
            // alert(JBD.JText._("LNG_INPUT_TYPE_NO_OPTIONS"));   
            return false;
        } 

        var tb = document.getElementById('list_feature_options');
        if (tb == null) {
            alert('Undefined list, contact administrator !');
        }

        var li_new	= document.createElement('li');

        var div_new = document.createElement('div');
        div_new.setAttribute('class','input-group py-1');

        var sort_btn_wrap = document.createElement('div');
        sort_btn_wrap.setAttribute('class','input-group-prepend');
        var sort_btn = document.createElement('div');
        sort_btn.setAttribute('class','btn btn-outline-light btn-sm d-flex');
        var sort_btn_icon = document.createElement('i');
        sort_btn_icon.setAttribute('class','la la-arrows-v text-dark');

        sort_btn.appendChild(sort_btn_icon);
        sort_btn_wrap.appendChild(sort_btn);
        div_new.appendChild(sort_btn_wrap);

        var input_o_new = document.createElement('input');
        input_o_new.setAttribute('type', 'text');
        input_o_new.setAttribute('name', 'option_name[]');
        input_o_new.setAttribute('class','input_txt form-control radius-0 m-0 validate[required] attribute_option');
        input_o_new.setAttribute('id', 'option_name[]');
        input_o_new.setAttribute('size', '32');
        input_o_new.setAttribute('maxlength', '128');
        var d = new Date();
        var id = d.getTime();

        var id_hidden_input = document.createElement('input');
        id_hidden_input.setAttribute('type', 'hidden');
        id_hidden_input.setAttribute('name', 'option_id[]');
        id_hidden_input.setAttribute('id', 'option_id[]');

        var icons_div = document.createElement('div');
        icons_div.setAttribute('class', 'input-group iconpicker-container');
        var iconsHtml = '';
        iconsHtml += '<input name="icon[]" data-placement="bottomRight" class="form-control radius-0 icp icp-auto iconpicker-element iconpicker-input attribute_icon" value="la la-500px" type="hidden">';
        iconsHtml += '<span class="input-group-addon btn btn-dark border-radius-0 attribute-icon-holder"><i class="la la-500px"></i></span>';
        icons_div.innerHTML = iconsHtml;

        var clear_btn_wrap = document.createElement('div');
        clear_btn_wrap.setAttribute('class','input-group-append');
        var delete_btn = document.createElement('div');
        delete_btn.setAttribute('class','btn btn-info btn-sm d-flex');
        delete_btn.onclick = function() {
        	clearIcon(jQuery(this));
        };
        var delete_btn_icon = document.createElement('i');
        delete_btn_icon.setAttribute('class','la la-close');
        delete_btn.appendChild(delete_btn_icon);
        clear_btn_wrap.appendChild(delete_btn);

        
        var delete_btn_wrap = document.createElement('div');
        delete_btn_wrap.setAttribute('class','input-group-append');
        var delete_btn = document.createElement('div');
        delete_btn.setAttribute('class','btn btn-danger btn-sm d-flex');
        delete_btn.onclick = function() {
            var row = jQuery(this).parents('li:first');
            var row_idx = row.prevAll().length;
            jQuery('#crt_pos').val(row_idx);
            jQuery('#btn_removefile').click();
            deleteAttributeOption(jQuery('#crt_pos').val())
        };
        var delete_btn_icon = document.createElement('i');
        delete_btn_icon.setAttribute('class','la la-trash-o');
        delete_btn.appendChild(delete_btn_icon);
        delete_btn_wrap.appendChild(delete_btn);

        div_new.appendChild(input_o_new);
        div_new.appendChild(id_hidden_input);
        div_new.appendChild(icons_div);
        div_new.appendChild(clear_btn_wrap);
        div_new.appendChild(delete_btn_wrap);
        li_new.appendChild(div_new);
        tb.appendChild(li_new);

        jQuery('.icp-auto').iconpicker({
            placement: 'topRightCorner'
        });
        showIconFields();
    }

    function setSellingAttribute() {
        jQuery("label[for='type_1']").click();
        jQuery("label[for='is_mandatory1']").click();
        jQuery("label[for='only_for_admin0']").click();
        jQuery("label[for='show_name0']").click();
        jQuery("label[for='show_in_front1']").click();
        jQuery("label[for='show_icon0']").click();
        jQuery("label[for='show_in_list_view0']").click();
    }

    function removeSellOption(){
        jQuery("label[for='use_attribute_for_selling0']").click();
    }

</script>
