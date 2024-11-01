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

<div class="app_tab" id="panel_7">

<div class="row panel_7_content">
    <div class="col-md-4">
        <legend><?php echo JText::_('LNG_BUSINESS_LISTING_ATTRIBUTES'); ?></legend>
        <fieldset style="background-color: #fbfbfb;padding: 14px;" class="form-horizontal">
            <div class="form-container">
            <?php foreach($this->item->defaultAtrributes as $attribute){
                if($attribute->name == 'zip_codes') {
                    continue;
                } ?>
                <div class="control-group">
                    <div class="control-label"><label id="order_search_listings-lbl" for="order_search_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo $attribute->name?></strong><br/><?php echo $attribute->name?>" title=""><?php echo $attribute->name?></label></div>
                    <div class="controls">
                        <select name="attribute-listing-<?php echo $attribute->id ?>" id="attribute-listing-<?php echo $attribute->id ?>" class="chosen-select">
                            <?php $attrConfiguraiton = $this->attributeConfiguration;
                                if(in_array($attribute->name, array('keywords', 'pictures', 'attachments', 'cover_image', 'opening_hours', 'metadata_information', 'publish_dates', 'zip_codes', 'business_team', 'address_autocomplete', 'publish_only_city', 'custom_gallery', 'areas_served')))
                                {
                                    unset($attrConfiguraiton[0]);
                                } 
                                if(in_array($attribute->name, array('zip_codes'))){
                                    unset($attrConfiguraiton);
                                }
                            ?>
                            <?php echo JHtml::_('select.options', $attrConfiguraiton, 'value', 'text', $attribute->config);?>
                        </select>
                    </div>
                </div>
            <?php }?>
            </div>
        </fieldset>
    </div>
    
    <div class="col-md-4">
        <legend ><?php echo JText::_('LNG_OFFERS_ATTRIBUTES'); ?></legend>
        <fieldset style="background-color: #fbfbfb;padding: 14px;" class="form-horizontal">
            <div class="form-container">
            <?php foreach($this->item->offerAtrributes as $attribute){
                if($attribute->id == 37){
                    continue;
                } ?>
                <div class="control-group">
                    <div class="control-label"><label id="order_search_listings-lbl" for="order_search_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo $attribute->name?></strong><br/><?php echo $attribute->name?>" title=""><?php echo $attribute->name?></label></div>
                    <div class="controls">
                        <select name="attribute-offer-<?php echo $attribute->id ?>" id="attribute-offer-<?php echo $attribute->id ?>" class="chosen-select">
                            <?php $attrConfiguraiton = $this->attributeConfiguration;
                            if($attribute->name=='attachments' || $attribute->name == 'metadata_information') {
                                unset($attrConfiguraiton[0]);
                            } ?>
                            <?php echo JHtml::_('select.options', $attrConfiguraiton, 'value', 'text', $attribute->config);?>
                        </select>
                    </div>
                </div>
            <?php }?>
            </div>
        </fieldset>
    </div>
     <div class="col-md-4">
        <legend><?php echo JText::_('LNG_EVENTS_ATTRIBUTES'); ?></legend>
        <fieldset style="background-color: #fbfbfb;padding: 14px;" class="form-horizontal">
            <div class="form-container">
            <?php foreach($this->item->eventAtrributes as $attribute){
                if($attribute->id == 37) {
                    continue;
                } ?>
                <div class="control-group">
                    <div class="control-label"><label id="order_search_listings-lbl" for="order_search_listings" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo $attribute->name?></strong><br/><?php echo $attribute->name?>" title=""><?php echo $attribute->name?></label></div>
                    <div class="controls">
                        <select name="attribute-event-<?php echo $attribute->id ?>" id="attribute-event-<?php echo $attribute->id ?>" class="chosen-select">
                            <?php $attrConfiguraiton = $this->attributeConfiguration;
                            if($attribute->name=='attachments' || $attribute->name == 'metadata_information' || $attribute->name == 'associated_listings') {
                                unset($attrConfiguraiton[0]);
                            } ?>
                            <?php echo JHtml::_('select.options', $attrConfiguraiton, 'value', 'text', $attribute->config);?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            </div>
        </fieldset>
    </div>
</div>

</div>
