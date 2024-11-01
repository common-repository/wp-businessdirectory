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

<div class="app_tab" id="panel_8">

<div class="row panel_8_content">
	<div class="col-md-6 general-settings">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('LNG_OFFERS'); ?></legend>
            <div class="form-container">
			<div class="control-group">
				<div class="control-label"><label id="enable_offers-lbl" for="enable_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_OFFERS');?></strong><br/><?php echo JText::_('LNG_ENABLE_OFFERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_OFFERS'); ?></label></div>
				<div class="controls">
					<fieldset id="enable_offers_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="enable_offers" id="enable_offers1" value="1" <?php echo $this->item->enable_offers==true? 'checked="checked"' :""?> />
						<label class="btn" for="enable_offers1"><?php echo JText::_('LNG_YES')?></label> 
						<input type="radio"  name="enable_offers" id="enable_offers0" value="0" <?php echo $this->item->enable_offers==false? 'checked="checked"' :""?> />
						<label class="btn" for="enable_offers0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
				</div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><label id="max_offers-lbl" for="max_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MAX_OFFERS');?></strong><br/><?php echo JText::_('LNG_MAX_OFFERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MAX_OFFERS'); ?></label></div>
				<div class="controls">
					<input type="text" size="40" maxlength="20"  id="max_offers" name="max_offers" value="<?php echo $this->item->max_offers ?>">
                </div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><label id="enable_offer_coupons-lbl" for="enable_offer_coupons" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_OFFER_COUPONS');?></strong><br/><?php echo JText::_('LNG_ENABLE_OFFER_COUPONS_DESCRIPTION');?>" title="" title=""><?php echo JText::_('LNG_ENABLE_OFFER_COUPONS'); ?></label></div>
				<div class="controls">
					<fieldset id="enable_offer_coupons_fld" class="radio btn-group btn-group-yesno">
						<input type="radio"  name="enable_offer_coupons" id="enable_offer_coupons1" value="1" <?php echo $this->item->enable_offer_coupons==true? 'checked="checked"' :""?> />
						<label class="btn" for="enable_offer_coupons1"><?php echo JText::_('LNG_YES')?></label> 
						
						<input type="radio"  name="enable_offer_coupons" id="enable_offer_coupons0" value="0" <?php echo $this->item->enable_offer_coupons==false? 'checked="checked"' :""?> />
						<label class="btn" for="enable_offer_coupons0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
				</div>
			</div>
			
			<?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/offerorder.php')) { ?>
				<div class="control-group">
					<div class="control-label"><label id="enable_offer_selling-lbl" for="enable_offer_selling" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_OFFER_SELLING');?></strong><br/><?php echo JText::_('LNG_ENABLE_OFFER_SELLING_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_ENABLE_OFFER_SELLING');?></label></div>
					<div class="controls">
						<fieldset id="enable_offer_selling_fld" class="radio btn-group btn-group-yesno">
							<input type="radio"  name="enable_offer_selling" id="enable_offer_selling1" value="1" <?php echo $this->item->enable_offer_selling==true? 'checked="checked"' :""?> />
							<label class="btn" for="enable_offer_selling1"><?php echo JText::_('LNG_YES')?></label>
							<input type="radio"  name="enable_offer_selling" id="enable_offer_selling0" value="0" <?php echo $this->item->enable_offer_selling==false? 'checked="checked"' :""?> />
							<label class="btn" for="enable_offer_selling0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
					</div>
				</div>

                <div class="control-group">
					<div class="control-label"><label id="show_offer_price_list-lbl" for="show_offer_price_list" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_OFFER_PRICE_LIST');?></strong><br/><?php echo JText::_('LNG_SHOW_OFFER_PRICE_LIST_DESC');?>" title=""><?php echo JText::_('LNG_SHOW_OFFER_PRICE_LIST');?></label></div>
					<div class="controls">
						<fieldset id="show_offer_price_list_fld" class="radio btn-group btn-group-yesno">
							<input type="radio"  name="show_offer_price_list" id="show_offer_price_list1" value="1" <?php echo $this->item->show_offer_price_list==true? 'checked="checked"' :""?> />
							<label class="btn" for="show_offer_price_list1"><?php echo JText::_('LNG_YES')?></label>
							<input type="radio"  name="show_offer_price_list" id="show_offer_price_list0" value="0" <?php echo $this->item->show_offer_price_list==false? 'checked="checked"' :""?> />
							<label class="btn" for="show_offer_price_list0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
					</div>
				</div>

                <div class="control-group offer-price-list-view" style="<?php echo $this->item->show_offer_price_list == 0? "display:none" :"" ?>">
                    <div class="control-label"><label id="offer_price_list_view_style-lbl" for="offer_price_list_view_style" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_PRICE_LIST_VIEW_STYLE');?></strong><br/><?php echo JText::_('LNG_OFFER_PRICE_LIST_VIEW_STYLE_DESC');?>" title=""><?php echo JText::_('LNG_OFFER_PRICE_LIST_VIEW_STYLE'); ?></label></div>
                    <div class="controls">
                        <fieldset id="offer_price_list_view_style_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio"  name="offer_price_list_view_style" id="offer_price_list_view_style1" value="1" <?php echo $this->item->offer_price_list_view_style==true? 'checked="checked"' :""?> />
                            <label class="btn" for="offer_price_list_view_style1"><?php echo JText::_('LNG_STYLE_2')?></label>
                            <input type="radio"  name="offer_price_list_view_style" id="offer_price_list_view_style0" value="0" <?php echo $this->item->offer_price_list_view_style==false? 'checked="checked"' :""?> />
                            <label class="btn" for="offer_price_list_view_style0"><?php echo JText::_('LNG_STYLE_1')?></label>
                        </fieldset>
                    </div>
                </div>
			<?php } ?>

            <div class="control-group">
                <div class="control-label"><label id="show_offer_free-lbl" for="show_offer_free" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SHOW_OFFER_FREE_PRICE');?></strong><br/><?php echo JText::_('LNG_SHOW_OFFER_FREE_PRICE_DESCRIPTION');?>" title="" title=""><?php echo JText::_('LNG_SHOW_OFFER_FREE_PRICE'); ?></label></div>
                <div class="controls">
                    <fieldset id="show_offer_free_fld" class="radio btn-group btn-group-yesno">
                        <input type="radio"  name="show_offer_free" id="show_offer_free1" value="1" <?php echo $this->item->show_offer_free==true? 'checked="checked"' :""?> />
                        <label class="btn" for="show_offer_free1"><?php echo JText::_('LNG_YES')?></label>

                        <input type="radio"  name="show_offer_free" id="show_offer_free0" value="0" <?php echo $this->item->show_offer_free==false? 'checked="checked"' :""?> />
                        <label class="btn" for="show_offer_free0"><?php echo JText::_('LNG_NO')?></label>
                    </fieldset>
                </div>
            </div>

            <?php if(file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/models/shippingmethod.php')) { ?>
                <div class="control-group">
                    <div class="control-label"><label id="enable_shipping-lbl" for="enable_shipping" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_ENABLE_SHIPPING');?></strong><br/><?php echo JText::_('LNG_ENABLE_SHIPPING_DESCRIPTION');?>" title="" title=""><?php echo JText::_('LNG_ENABLE_SHIPPING'); ?></label></div>
                    <div class="controls">
                        <fieldset id="enable_shipping_fld" class="radio btn-group btn-group-yesno">
                            <input type="radio" name="enable_shipping" id="enable_shipping1" value="1" <?php echo $this->item->enable_shipping == true ? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_shipping1"><?php echo JText::_('LNG_YES')?></label>

                            <input type="radio" name="enable_shipping" id="enable_shipping0" value="0" <?php echo $this->item->enable_shipping == false ? 'checked="checked"' :""?> />
                            <label class="btn" for="enable_shipping0"><?php echo JText::_('LNG_NO')?></label>
                        </fieldset>
                    </div>
                </div>
            <?php } ?>
            </div>
		</fieldset>
	</div>
</div>

    <div class="row">
        <div class="col-md-12 general-settings">
    		<fieldset class="form-horizontal">
    			<legend><?php echo JText::_('LNG_SEARCH'); ?></legend>
    			<div class="row">
                    <div class="col-md-6 general-settings">
                        <div class="form-container">
                            <div class="control-group">
                                <div class="control-label"><label id="offer_submit_method-lbl" for="offer_submit_method" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SUBMIT_METHOD');?></strong><br/><?php echo JText::_('LNG_SUBMIT_METHOD_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SUBMIT_METHOD'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="offer_submit_method_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="offer_submit_method" id="offer_submit_method1" value="post" <?php echo $this->item->offer_submit_method=="post"? 'checked="checked"' :""?> />
                                        <label class="btn" for="offer_submit_method1"><?php echo JText::_('LNG_POST')?></label>
                                        <input type="radio"  name="offer_submit_method" id="offer_submit_method2" value="get" <?php echo $this->item->offer_submit_method=="get"? 'checked="checked"' :""?> />
                                        <label class="btn" for="offer_submit_method2"><?php echo JText::_('LNG_GET')?></label>
                                    </fieldset>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label"><label id="order_search_offers-lbl" for="order_search_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_order_search_offers');?></strong><br/><?php echo JText::_('LNG_ORDER_SEARCH_OFFERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_order_search_offers'); ?></label></div>
                                <div class="controls">
                                    <select name="order_search_offers" id="order_search_offers_fld" class="chosen-select">
                                        <?php foreach( $this->item->orderSearchOffers as $key=>$orderSearchOffer){?>
                                            <option value="<?php echo $key ?>" <?php echo $key == $this->item->order_search_offers ? "selected":"" ; ?>><?php echo JText::_($orderSearchOffer)  ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="control-group">
			                    <div class="control-label"><label id="mix_results_offers" for="mix_results_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_MIX_RESULTS');?></strong><br/><?php echo JText::_('LNG_MIX_RESULTS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_MIX_RESULTS'); ?></label></div>
	                            <div class="controls">
	                                <fieldset id="mix_results_offers_fld" class="radio btn-group btn-group-yesno">
	                                    <input type="radio"  name="mix_results_offers" id="mix_results_offers1" value="1" <?php echo $this->item->mix_results_offers==true? 'checked="checked"' :""?> />
	                                    <label class="btn" for="mix_results_offers1"><?php echo JText::_('LNG_YES')?></label>
	                                    <input type="radio"  name="mix_results_offers" id="mix_results_offers0" value="0" <?php echo $this->item->mix_results_offers==false? 'checked="checked"' :""?> />
	                                    <label class="btn" for="mix_results_offers0"><?php echo JText::_('LNG_NO')?></label>
	                                </fieldset>
	                            </div>
	                        </div>
                            
                            <div class="control-group">
                                <div class="control-label"><label id="offer_search_results_grid_view-lbl" for="offer_search_results_grid_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_SEARCH_RESULT_GRID_VIEW');?></strong><br/><?php echo JText::_('LNG_OFFER_SEARCH_RESULT_GRID_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_OFFER_SEARCH_RESULT_GRID_VIEW'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="offer_search_results_grid_view_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="offer_search_results_grid_view" id="offer_search_results_grid_view1" value="1" <?php echo $this->item->offer_search_results_grid_view==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="offer_search_results_grid_view1"><?php echo JText::_('LNG_STYLE_2')?></label>
                                        <input type="radio"  name="offer_search_results_grid_view" id="offer_search_results_grid_view0" value="0" <?php echo $this->item->offer_search_results_grid_view==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="offer_search_results_grid_view0"><?php echo JText::_('LNG_STYLE_1')?></label>
                                    </fieldset>
                                </div>
                            </div>
            
                            <div class="control-group">
                                <div class="control-label"><label id="offer_search_results_list_view-lbl" for="offer_search_results_list_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_SEARCH_RESULT_LIST_VIEW');?></strong><br/><?php echo JText::_('LNG_OFFER_SEARCH_RESULT_LIST_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_OFFER_SEARCH_RESULT_LIST_VIEW'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="offer_search_results_list_view_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="offer_search_results_list_view" id="offer_search_results_list_view1" value="1" <?php echo $this->item->offer_search_results_list_view==1? 'checked="checked"' :""?> />
                                        <label class="btn" for="offer_search_results_list_view1"><?php echo JText::_('LNG_STYLE_1')?></label>
                                        <input type="radio"  name="offer_search_results_list_view" id="offer_search_results_list_view2" value="2" <?php echo $this->item->offer_search_results_list_view==2? 'checked="checked"' :""?> />
                                        <label class="btn" for="offer_search_results_list_view2"><?php echo JText::_('LNG_STYLE_2')?></label>
                                    </fieldset>
                                </div>
                            </div>
            
                            <div class="control-group">
                                <div class="control-label"><label id="offers_view_mode-lbl" for="offers_view_mode" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_DEFAULT_OFFERS_VIEW');?></strong><br/><?php echo JText::_('LNG_DEFAULT_OFFERS_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_DEFAULT_OFFERS_VIEW'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="offers_view_mode_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="offers_view_mode" id="offers_view_mode1" value="1" <?php echo $this->item->offers_view_mode==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="offers_view_mode1"><?php echo JText::_('LNG_GRID_MODE')?></label>
                                        <input type="radio"  name="offers_view_mode" id="offers_view_mode0" value="0" <?php echo $this->item->offers_view_mode==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="offers_view_mode0"><?php echo JText::_('LNG_LIST_MODE')?></label>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 general-settings">
                    	<div class="form-container">
                		   <div class="control-group">
                                <div class="control-label"><label id="enable_search_filter_offers-lbl" for="enable_search_filter_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_enable_search_filter_offers');?></strong><br/><?php echo JText::_('LNG_ENABLE_SEARCH_FILTER_OFFERS_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_enable_search_filter_offers'); ?></label></div>
                                <div class="controls">
                                    <fieldset id="enable_search_filter_offers_fld" class="radio btn-group btn-group-yesno">
                                        <input type="radio"  name="enable_search_filter_offers" id="enable_search_filter_offers1" value="1" <?php echo $this->item->enable_search_filter_offers==true? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_search_filter_offers1"><?php echo JText::_('LNG_YES')?></label>
                                        <input type="radio"  name="enable_search_filter_offers" id="enable_search_filter_offers0" value="0" <?php echo $this->item->enable_search_filter_offers==false? 'checked="checked"' :""?> />
                                        <label class="btn" for="enable_search_filter_offers0"><?php echo JText::_('LNG_NO')?></label>
                                    </fieldset>
                                </div>
                            </div>

                            <div id="offer-search-filter-settings" style="<?php echo $this->item->enable_search_filter_offers == 0? "display:none" :"" ?>">
                                <div class="control-group">
                                    <div class="control-label"><label id="offers_search_filter_type-lbl" for="offers_search_filter_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFERS_SEARCH_FILTER_TYPE');?></strong><br/><?php echo JText::_('LNG_OFFERS_SEARCH_FILTER_TYPE_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_OFFERS_SEARCH_FILTER_TYPE"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="offers_search_filter_type_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="offers_search_filter_type" id="offers_search_filter_type1" value="1" <?php echo $this->item->offers_search_filter_type==1? 'checked="checked"' :""?> />
                                            <label class="btn" for="offers_search_filter_type1"><?php echo JText::_('LNG_HORIZONTAL')?></label>
                                            <input type="radio"  name="offers_search_filter_type" id="offers_search_filter_type2" value="2" <?php echo $this->item->offers_search_filter_type==2? 'checked="checked"' :""?> />
                                            <label class="btn" for="offers_search_filter_type2"><?php echo JText::_('LNG_VERTICAL')?></label>
                                        </fieldset>
                                    </div>
                                </div>

                                
                                <div class="control-group">
                                    <div class="control-label"><label id="search_filter_view_offers-lbl" for="search_filter_view_offers" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER_VIEW_OFFERS');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_VIEW_OFFERS_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER_VIEW_OFFERS"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="search_filter_view_offers_fld" class="radio btn-group btn-group-yesno">
                                            <input type="radio"  name="search_filter_view_offers" id="search_filter_view_offers1" value="1" <?php echo $this->item->search_filter_view_offers==1? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_filter_view_offers1"><?php echo JText::_('LNG_STYLE_1')?></label>
                                            <input type="radio"  name="search_filter_view_offers" id="search_filter_view_offers2" value="2" <?php echo $this->item->search_filter_view_offers==2? 'checked="checked"' :""?> />
                                            <label class="btn" for="search_filter_view_offers2"><?php echo JText::_('LNG_STYLE_2')?></label>
                                        </fieldset>
                                    </div>
                                </div>
                                
                                <div class="control-group">
                                    <div class="control-label"><label id="offer_search_type-lbl" for="offer_search_type" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_("LNG_SEARCH_FILTER"); ?></label></div>
                                    <div class="controls">
                                        <fieldset id="offer_search_type_fld" class="radio btn-group btn-group-yesno">
                                            <!--input type="radio"  name="offer_search_type" id="offer_search_type1" value="1" <?php echo $this->item->offer_search_type==true? 'checked="checked"' :""?> />
                                            <label class="btn" for="offer_search_type1"><?php echo JText::_('LNG_FACETED')?></label-->
                                            <input type="radio"  name="offer_search_type" id="offer_search_type0" value="0" <?php echo $this->item->offer_search_type==false || true? 'checked="checked"' :""?> />
                                            <label class="btn" for="offer_search_type0"><?php echo JText::_('LNG_FILTER_REGULAR')?></label>
                                        </fieldset>
                                    </div>
                                </div>   
                                
                                <div class="control-group">
                                    <div class="control-label"><label id="offer_search_filter_items-lbl" for="offer_search_filter_items" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SEARCH_FILTER_ITEM');?></strong><br/><?php echo JText::_('LNG_SEARCH_FILTER_ITEM_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SEARCH_FILTER_ITEM'); ?></label></div>
                                    <div class="controls">
                                        <input type="text" size=40 maxlength=20  id="offer_search_filter_items" name="offer_search_filter_items" value="<?php echo $this->item->offer_search_filter_items?>">
                                    </div>
                                </div>
                                
                                <div class="control-group">
                                    <div class="control-label"><label id="offer_search_filter_fields-lbl" for="offer_search_filter_fields[]" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_SELECT_FILTER_FIELDS');?></strong><br/><?php echo JText::_('LNG_SELECT_SEARCH_FILTER_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_SELECT_FILTER_FIELDS'); ?></label></div>
                                    <div class="controls">
                                        <select	id="offer_search_filter_fields[]" name="offer_search_filter_fields[]" data-placeholder="<?php echo JText::_("LNG_SELECT_FIELDS") ?>" class="chzn-color" multiple>
                                            <?php
                                            foreach($this->offerSearchFilterFields as $field) {
                                                $selected = "";
                                                if (!empty($this->item->offer_search_filter_fields)) {
                                                    if (in_array($field->value, $this->item->offer_search_filter_fields))
                                                        $selected = "selected";
                                                } ?>
                                                <option value='<?php echo $field->value ?>' <?php echo $selected ?>> <?php echo $field->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                		</div>
	            	</div>
	        	</div>
	        </fieldset>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-md-6 general-settings">
    		<fieldset class="form-horizontal">
    			<legend><?php echo JText::_('LNG_OFFER_DETAILS'); ?></legend>
                <div class="form-container">
    			<div class="control-group">
    				<div class="control-label"><label id="offer_view-lbl" for="offer_view" class="hasTooltip" data-toggle="tooltip" data-original-title="<strong><?php echo JText::_('LNG_OFFER_VIEW');?></strong><br/><?php echo JText::_('LNG_OFFER_VIEW_DESCRIPTION');?>" title=""><?php echo JText::_('LNG_OFFER_VIEW'); ?></label></div>
    				<div class="controls">
    					<fieldset id="offer_view_fld" class="radio btn-group btn-group-yesno">
    						<input type="radio"  name="offer_view" id="offer_view1" value="1" <?php echo $this->item->offer_view==1? 'checked="checked"' :""?> />
    						<label class="btn" for="offer_view1"><?php echo JText::_('LNG_STYLE_1')?></label>
                            <input type="radio"  name="offer_view" id="offer_view0" value="0" <?php echo $this->item->offer_view==0? 'checked="checked"' :""?> />
    						<label class="btn" for="offer_view0"><?php echo JText::_('LNG_STYLE_2')?></label>
                        </fieldset>
    				</div>
    			</div>
                </div>
    		</fieldset>
    	</div>
    </div>

</div>

<script>
    window.addEventListener('load', function() {
        // Hide settings not taken into consideration
        jQuery("#show_offer_price_list1").click(function(){
            jQuery(".offer-price-list-view").show(300);
        });
        jQuery("#show_offer_price_list0").click(function(){
            jQuery(".offer-price-list-view").hide(300);
        });
        
        jQuery("#enable_search_filter_offers1").click(function(){
            jQuery("#offer-search-filter-settings").show(300);
        });
        jQuery("#enable_search_filter_offers0").click(function(){
            jQuery("#offer-search-filter-settings").hide(300);
        });
    });

</script>