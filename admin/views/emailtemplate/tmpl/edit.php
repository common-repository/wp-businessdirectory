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
JHtml::_('formbehavior.chosen', 'select');
// Load the tooltip behavior.
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
    JBD.submitbutton = function (task) {
        jQuery("#item-form").validationEngine('detach');
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("click", true, true);
        var tab = ("tab-" + jbdUtils.getProperty("defaultLang"));
        if (!(document.getElementsByClassName(tab)[0] === undefined || document.getElementsByClassName(tab)[0] === null))
            document.getElementsByClassName(tab)[0].dispatchEvent(evt);
        if (task == 'emailtemplate.cancel' || task == 'emailtemplate.aprove' || task == 'emailtemplate.disaprove' || !jbdUtils.validateCmpForm(false, true)) {
            JBD.submitform(task, document.getElementById('item-form'));
        }
        jQuery("#item-form").validationEngine('attach');
    }
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=emailtemplate');?>" method="post" name="adminForm" id="item-form">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_EMAIL_DETAILS');?></h2>
                            <div class="form-container label-w-100">
                                <div class="form-group">
                                    <label for="email_name"><?php echo JText::_('LNG_NAME')?> </label>
                                    <input type="text"	name="email_name" id="email_name" class="form-control" value="<?php echo $this->item->email_name ?>"  maxLength="255">
                                </div>

                                <div class="form-group">
                                    <label for="state"><?php echo JText::_('LNG_TYPE')?> </label>
                                    <select id="email_type" name="email_type" class="form-control input-medium">
                                        <?php foreach ($this->types as $key=>$type){ ?>
                                            <option <?php echo $this->item->email_type==$key? "selected" : ""?> value='<?php echo $key ?>'><?php echo $type; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group" >
                                    <label for="send_to_admin"><?php echo JText::_('LNG_SEND_TO_ADMIN')?> </label>
                                    <div>
                                        <fieldset id="send_to_admin_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio" class="validate[required]" name="send_to_admin" id="send_to_admin1" value="1" <?php echo $this->item->send_to_admin==1? 'checked="checked"' :""?> />
                                            <label class="btn" for="send_to_admin1"><?php echo JText::_('LNG_YES')?></label>
                                            <input type="radio" class="validate[required]" name="send_to_admin" id="send_to_admin0" value="0" <?php echo $this->item->send_to_admin==0? 'checked="checked"' :""?> />
                                            <label class="btn" for="send_to_admin0"><?php echo JText::_('LNG_NO')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_SUBJECT')?> <?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <?php
                                    if($this->appSettings->enable_multilingual) {
                                        $jbdTabs->setOptions($options);
                                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                                        foreach( $this->languages  as $k=>$lng ) {
                                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                            $langContent = isset($this->translations[$lng."_name"])?$this->translations[$lng."_name"]:"";
                                            if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->email_subject;
                                            }
                                            $langContent=$this->escape($langContent);
                                            echo "<input type='text' name='name_$lng' id='name_$lng' class='input_txt form-control validate[required]' value=\"".stripslashes($langContent)."\"  maxLength='255'>";
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else { ?>
                                        <input type='text' name="email_subject" id="email_subject" value="<?php echo $this->item->email_subject?>" size="50" class='input_txt form-control validate[required]' maxlength="255">
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <div  class="form-detail req"></div>
                                    <label for="content"><?php echo JText::_('LNG_CONTENT')?> </label>
                                    <?php
                                    if($this->appSettings->enable_multilingual) {
                                        $jbdTabs->setOptions($options);
                                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                                        foreach( $this->languages  as $k=>$lng ) {
                                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                            $langContent = isset($this->translations[$lng])?$this->translations[$lng]:"";
                                            if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->email_content;
                                            }
                                            $editor = JBusinessUtil::getEditor();
                                            echo $editor->display('description_'.$lng, $langContent, '100%', '450', '70', '10', false);
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else {
                                        $editor = JBusinessUtil::getEditor();
                                        echo $editor->display('email_content', $this->item->email_content, '100%', '450', '60', '20', false);
                                    }
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="row">
                    <div id="dialog-container" class="col-md-12">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_PLACEHOLDERS_AVAILABLE');?></h2>
                            <div id="legend" class="form-container label-w-100 email-legend">
                                <h4> <?php echo JText::_('LNG_PLACEHOLDERS_AVAILABLE_FOR_SUBJECT');?></h4>
                                <?php if (!empty($this->placeHolders['subject'])){ ?>
                                <dl class="dl-horizontal">
                                    <?php foreach ($this->placeHolders['subject'] as $key => $placeHolder){ ?>
                                        <dt><span class="status-badge badge-info mr-2"><?php echo $key ?></span></dt>
                                        <dd><?php echo $placeHolder; ?></dd>
                                    <?php } ?>
                                </dl>
                                <?php }else{
                                    echo '<h5>' . JText::_('LNG_NONE') . '</h5>';
                                } ?>

                                <h4> <?php echo JText::_('LNG_PLACEHOLDERS_AVAILABLE_FOR_CONTENT');?></h4>
                                <?php if (!empty($this->placeHolders['content'])){ ?>
                                <dl class="dl-horizontal">
                                    <?php foreach ($this->placeHolders['content'] as $key => $placeHolder){ ?>
                                        <dt><span class="status-badge badge-info mr-2"><?php echo $key ?></span></dt>
                                        <dd><?php echo $placeHolder; ?></dd>
                                    <?php } ?>
                                </dl>
                                <?php } ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="email_id" value="<?php echo $this->item->email_id ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
