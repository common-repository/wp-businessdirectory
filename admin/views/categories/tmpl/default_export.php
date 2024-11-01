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

?>

<div class="jbd-container jbd-edit-container" id="export-model" style="display: none">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=categories');?>" method="post" name="exportForm" id="exportForm" enctype="multipart/form-data">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_CATEGORIES_EXPORT_CSV') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>
            <div class="jmodal-body modal-batch">
                <p><?php echo JText::_('LNG_CATEGORIES_EXPORT_CSV_TEXT'); ?></p>
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline ">
                            <label for="delimiter"><?php echo JText::_('LNG_DELIMITER')?> </label>
                            <select name="delimiter" class="form-control">
                                <option value=";"><?php echo JText::_('LNG_SEMICOLON')?></option>
                                <option value=","><?php echo JText::_('LNG_COMMA')?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
                <input type="hidden" name="task" id="task" value="categories.exportCategoriesCsv" />
                <?php echo JHTML::_( 'form.token' ); ?>
            </div>
            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="submit" class="jmodal-btn"><?php echo JText::_("LNG_EXPORT")?></button>
                </div>
            </div>
        </div>
    </form>
</div>