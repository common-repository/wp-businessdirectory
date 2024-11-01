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
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.

?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task)
	{	
		if (task == 'country.cancel' || !jbdUtils.validateCmpForm(false, false)) {
			JBD.submitform(task, document.getElementById('item-form'));
		}
    }
});
</script>

<?php 
$appSetings = JBusinessUtil::getApplicationSettings();
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

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">
                            <h2> <?php echo JText::_('LNG_COUNTRY_DETAILS');?></h2>
                            <div class="form-container label-w-100">
                                <div class="form-group">
                                    <label for="subject"><?php echo JText::_('LNG_NAME')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <?php
                                    if($this->appSettings->enable_multilingual){
                                        $jbdTabs->setOptions($options);
                                        echo $jbdTabs->startTabSet('tab_groupsd_id');
                                        foreach( $this->languages as $k=>$lng ){
                                            echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                            $langContent = isset($this->translations[$lng."_name"])?$this->translations[$lng."_name"]:"";
                                            if($lng == JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                $langContent = $this->item->country_name;
                                            }
                                            $langContent = $this->escape($langContent);
                                            echo "<input type='text' name='name_$lng' id='name_$lng' class='input_txt form-control validate[required]' value=\"".stripslashes($langContent)."\"  maxLength='100'>";
                                            echo $jbdTabs->endTab();
                                        }
                                        echo $jbdTabs->endTabSet();
                                    } else { ?>
                                        <input type="text" name="country_name" id="country_name" class="validate[required] form-control input_txt" value="<?php echo $this->escape($this->item->country_name) ?>" maxlength="255">
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <label for="subject"><?php echo JText::_('LNG_CODE')?> </label>
                                    <input type="text"
                                        name="country_code" id="country_code" class="input_txt form-control" value="<?php echo $this->item->country_code ?>" maxlength="4">
                                </div>

                                <div class="form-group">
                                    <label for="description_id"><?php echo JText::_('LNG_DESCRIPTION')?></label>
                                    <?php
                                        if($this->appSettings->enable_multilingual){
                                            $jbdTabs->setOptions($options);
                                            echo $jbdTabs->startTabSet('tab_groupsd_id');
                                            foreach( $this->languages  as $k=>$lng ){
                                                echo $jbdTabs->addTab('tab_groupsd_id', 'tab-'.$lng, $k);
                                                $langContent = isset($this->translations[$lng])?$this->translations[$lng]:"";
                                                if($lng==JBusinessUtil::getLanguageTag() && empty($langContent)){
                                                    $langContent = $this->item->description;
                                                }

                                                echo "<textarea id='description_$lng' name='description_$lng' class='input_txt form-control h-auto' cols='75' rows='10' maxLength='245'>$langContent</textarea>";
                                                echo $jbdTabs->endTab();
                                            }
                                            echo $jbdTabs->endTabSet();
                                        }else {
                                        ?>
                                            <textarea name="description" id="description" class="input_txt form-control h-auto"  cols="75" rows="5"  maxLength="245"
                                                 onkeyup="calculateLenght();"><?php echo $this->item->description ?></textarea>
                                        <?php
                                        }
                                        ?>

                                </div>

                                <div class="form-group" style="display:none">
                                    <label for="price"><?php echo JText::_('LNG_CURRENCY')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <input type="text"
                                        name="price" id="price" class="input_txt form-control"
                                        value="<?php echo $this->item->country_currency ?>" maxlength="255">
                                </div>

                                <div class="form-group" style="display:none">
                                    <label for="price"><?php echo JText::_('LNG_CURRENCY_SHORT')?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY)?> </label>
                                    <input type="text"
                                        name="price" id="price" class="input_txt form-control"
                                        value="<?php echo $this->item->country_currency_short ?>" maxlength="50">
                                </div>

                            </div>
                        </fieldset>

                        <fieldset class="boxed col-12">
                            <div class="form-container">
                                <h2> <?php echo JText::_('LNG_ADD_LOGO');?></h2>
                                <div>
                                    <?php echo JText::_('LNG_ADD_LOGO_TEXT');?>
                                </div>
                                <div class="jupload logo-jupload">
                                    <div class="jupload-header">
                                        <div class="jupload-header-title">
                                            <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                        </div>
                                    </div>
                                    <div class="jupload-body">
                                        <div class="jupload-files">
                                            <div class="jupload-files-img image-fit-contain" id="picture-preview">
                                                <?php
                                                if (!empty($this->item->logo)) {
                                                    echo "<img src='".BD_PICTURES_PATH.$this->item->logo."'/>";
                                                }else{
                                                    echo "<i class='la la-image'></i>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="jupload-options">
                                        <div class="jupload-options-btn jupload-actions">
                                            <label for="imageUploader" class="btn btn-outline-success"><?php echo JText::_("LNG_UPLOAD")?></label>
                                            <a name="" id="" class="" href="javascript:uploadInstance.removeImage()" role="button"><i class="la la-trash"></i></a>
                                        </div>
                                        <div class="">
                                            <?php echo JText::_("LNG_SELECT_IMAGE_TYPE") ?>
                                        </div>
                                    </div>
                                    <div class="jupload-footer">
                                        <fieldset>
                                            <input  type="file" id="imageUploader" name="uploadLogo" size="50" >
                                            <input type="hidden" name="logo" id="imageLocation" value="<?php echo $this->item->logo?>">
                                        </fieldset>
                                    </div>
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
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>

<?php JBusinessUtil::loadUploadScript(); ?>

<script  type="text/javascript">

    var companyFolder = '<?php echo COUNTRIES_PICTURES_PATH ?>';
    var companyFolderPath = '<?php echo JBusinessUtil::getUploadUrl() ?>&t=<?php echo strtotime("now")?>&picture_type=<?php echo PICTURE_TYPE_LOGO?>&_path_type=1&_target=<?php echo urlencode(COUNTRIES_PICTURES_PATH)?>&croppable=1';

    var uploadInstance;

    window.addEventListener('load', function() {
        uploadInstance = JBDUploadHelper.getUploadInstance();
        uploadInstance.imageUploader(companyFolder, companyFolderPath);
    });

</script>