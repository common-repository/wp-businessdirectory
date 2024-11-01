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

<div class="app_tab" id="panel_11">

    <div class="row panel_11_content">
        <div class="col-md-6 general-settings">
        <fieldset class="form-horizontal">
            <legend><?php echo JText::_('LNG_CONFERENCES'); ?></legend>
            <div class="form-container">
            <div class="control-group">
                <div class="control-label"><label id="conference_view_mode-lbl" for="conference_view_mode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_CONFERNCE_VIEW_MODE');?></strong><br/><?php echo JText::_('LNG_CONFERNCE_VIEW_MODE');?>" title=""><?php echo JText::_('LNG_CONFERNCE_VIEW_MODE'); ?></label></div>
                <div class="controls">
                    <fieldset id="conference_view_mode_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="conference_view_mode" id="conference_view_mode1" value="1" <?php echo $this->item->conference_view_mode==1? 'checked="checked"' :""?> />
                        <label class="btn" for="conference_view_mode1"><?php echo JText::_('LNG_LIST_MODE')?></label>
                        <input type="radio"  name="conference_view_mode" id="conference_view_mode0" value="0" <?php echo $this->item->conference_view_mode==0? 'checked="checked"' :""?> />
                        <label class="btn" for="conference_view_mode0"><?php echo JText::_('LNG_GRID_MODE')?></label>
                    </fieldset>
                </div>
            </div>
            </div>
        </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_SESSIONS'); ?></legend>
                <div class="form-container">
                <div class="control-group">
                    <div class="control-label"><label id="sessions_view-lbl" for="sessions_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SESSIONS_VIEW');?></strong><br/><?php echo JText::_('LNG_SESSIONS_VIEW');?>" title=""><?php echo JText::_('LNG_SESSIONS_VIEW'); ?></label></div>
                    <div class="controls">
                        <fieldset id="sessions_view_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="sessions_view" id="sessions_view0" value="1" <?php echo $this->item->sessions_view==1? 'checked="checked"' :""?> />
                            <label class="btn" for="sessions_view0"><?php echo JText::_('LNG_STYLE_1')?></label>
                            <input type="radio"  name="sessions_view" id="sessions_view1" value="2" <?php echo $this->item->sessions_view==2? 'checked="checked"' :""?> />
                            <label class="btn" for="sessions_view1"><?php echo JText::_('LNG_STYLE_2')?></label>
                            <input type="radio"  name="sessions_view" id="sessions_view2" value="3" <?php echo $this->item->sessions_view==3? 'checked="checked"' :""?> />
                            <label class="btn" for="sessions_view2"><?php echo JText::_('LNG_STYLE_3')?></label>
                        </fieldset>
                    </div>
                </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_SESSION_DETAILS'); ?></legend>
                <div class="form-container">
                    <div class="control-group">
                        <div class="control-label"><label id="session-view-lbl" for="session-view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SESSION');?></strong><br/><?php echo JText::_('LNG_SESSION');?>" title=""><?php echo JText::_('LNG_SESSION'); ?></label></div>
                        <div class="controls">
                            <fieldset id="session_view_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="session_view" id="session_view0" value="1" <?php echo $this->item->session_view==1? 'checked="checked"' :""?> />
                                <label class="btn" for="session_view0"><?php echo JText::_('LNG_STYLE_1')?></label>
                                <input type="radio"  name="session_view" id="session_view1" value="2" <?php echo $this->item->session_view==2? 'checked="checked"' :""?> />
                                <label class="btn" for="session_view1"><?php echo JText::_('LNG_STYLE_2')?></label>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 general-settings">
            <fieldset class="form-horizontal">
                <legend><?php echo JText::_('LNG_SPEAKERS'); ?></legend>
                <div class="form-container">

                    <div class="control-group">
                        <div class="control-label"><label id="speakers_view-lbl" for="speakers_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SPEAKERS_VIEW');?></strong><br/><?php echo JText::_('LNG_SPEAKERS_VIEW');?>" title=""><?php echo JText::_('LNG_SPEAKERS_VIEW'); ?></label></div>
                        <div class="controls">
                            <fieldset id="speakers_view_fld" class="radio btn-group btn-group-yesno">
                                <input type="radio"  name="speakers_view" id="speakers_view0" value="1" <?php echo $this->item->speakers_view==1? 'checked="checked"' :""?> />
                                <label class="btn" for="speakers_view0"><?php echo JText::_('LNG_STYLE_1')?></label>
                                <input type="radio"  name="speakers_view" id="speakers_view1" value="2" <?php echo $this->item->speakers_view==2? 'checked="checked"' :""?> />
                                <label class="btn" for="speakers_view1"><?php echo JText::_('LNG_STYLE_2')?></label>
                            </fieldset>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><label id="speaker_img_width-lbl" for="speaker_img_width" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SPEAKER_IMG_WIDTH');?></strong><br/><?php echo JText::_('LNG_SPEAKER_IMG_WIDTH_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SPEAKER_IMG_WIDTH'); ?></label></div>
                        <div class="controls">
                            <input type="text" size=40 maxlength=20  id="speaker_img_width" name="speaker_img_width" value="<?php echo $this->item->speaker_img_width?>">
                        </div>
                    </div>
	
                    <div class="control-group">
                        <div class="control-label"><label id="speaker_img_height-lbl" for="speaker_img_height" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SPEAKER_IMG_HEIGHT');?></strong><br/><?php echo JText::_('LNG_SPEAKER_IMG_HEIGHT_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SPEAKER_IMG_HEIGHT'); ?></label></div>
                        <div class="controls">
                            <input type="text" size=40 maxlength=20  id="speaker_img_height" name="speaker_img_height" value="<?php echo $this->item->speaker_img_height?>">
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

</div>