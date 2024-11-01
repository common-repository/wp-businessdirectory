<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>

<div class="app_tab" id="panel_4">

    <div class="row panel_4_content">
        <div class="col-md-6 general-settings">
            <fieldset class="adminform long metadata">
                <legend><?php echo JText::_('LNG_METADATA_SETTINGS'); ?></legend>
                <div class="form-container">
                <ul class="adminformlist">
                    <li>
                        <label title="" class="hasTip hasTooltip" for="meta_description" id="meta_description-lbl" aria-invalid="false" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_META_DESCRIPTION');?></strong><br/><?php echo JText::_('LNG_META_DESCRIPTION_META');?>"><?php echo JText::_('LNG_META_DESCRIPTION'); ?></label>
                        <textarea rows="3" cols="60" id="meta_description" name="meta_description" class="h-auto" aria-invalid="false"><?php echo $this->item->meta_description ?></textarea>
                    </li>
                    <li>
                        <label title="" class="hasTip hasTooltip" for="meta_keywords" id="meta_keywords_lbl" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_META_KEYWORDS');?></strong><br/><?php echo JText::_('LNG_META_KEYWORDS_META');?>"><?php echo JText::_('LNG_META_KEYWORDS'); ?></label>
                        <textarea rows="3" cols="60" id="meta_keywords" class="h-auto" name="meta_keywords"><?php echo $this->item->meta_keywords ?></textarea>
                    </li>
                    <li>
                        <label title="" class="hasTip hasTooltip" for="meta_description_facebook" id="meta_description_facebook-lbl" aria-invalid="false"  data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_META_DESCRIPTION_FACEBOOK');?></strong><br/><?php echo JText::_('LNG_META_DESCRIPTION_FACEBOOK_META');?>"><?php echo JText::_('LNG_META_DESCRIPTION_FACEBOOK'); ?></label>
                        <textarea rows="3" cols="60" id="meta_description_facebook" name="meta_description_facebook" class="h-auto" aria-invalid="false"><?php echo $this->item->meta_description_facebook ?></textarea>
                    </li>
                </ul>
                </div>
            </fieldset>
        </div>
    </div>

</div>