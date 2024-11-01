<?php
$view = JFactory::getApplication()->input->get('view');
if($view != 'billingdetails') {
    $this->item = $this->guestDetails;
}
?>

<div class="form-box">                         
    <div class="row">
        <div class="col-md">
            <div class="form-group">
                <label for="first_name"><?php echo JText::_('LNG_FIRST_NAME')?> </label>
                <input type="text" name="first_name" id="first_name" class="form-control input_txt  validate[required]" value="<?php echo $this->item->first_name ?>" maxlength="45">
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                <div  class="form-detail req"></div>
                <label for="last_name"><?php echo JText::_('LNG_LAST_NAME')?> </label>
                <input type="text" name="last_name" id="last_name" class="form-control input_txt  validate[required]" value="<?php echo $this->item->last_name ?>" maxlength="45">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                <label for="email"><?php echo JText::_('LNG_EMAIL')?> </label>
                <input type="text" name="email" id="email" class="form-control input_txt  validate[required,custom[email]]" value="<?php echo $this->item->email ?>" maxlength="45">
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                <label for="phone"><?php echo JText::_('LNG_PHONE')?> </label>
                <input type="text" name="phone" id="phone" class="form-control input_txt" value="<?php echo $this->item->phone ?>" maxlength="45">
            </div>
        </div>
    </div>
    <?php if ($view == 'billingdetails') { ?>
        <div class="row company-name-billing">
            <div class="col">
                <div class="form-group">
                    <label for="company_name"><?php echo JText::_('LNG_COMPANY_NAME')?> </label>
                    <input type="text" name="company_name" id="company_name" class="form-control input_txt" value="<?php echo $this->item->company_name ?>" maxlength="55">
                </div>
            </div>
        </div>

        <div class="row vat-details">
            <div class="col">
                <div class="form-group">
                    <label for="vat-details"><?php echo JText::_('LNG_VAT_DETAILS')?> <i class="la la-info-circle" title="<?php echo JText::_('LNG_VAT_DETAILS_INFO') ?> "></i></label>
                    <textarea name="vat_details" id="vat-details" class="form-control input_txt" maxlength="455"><?php echo !empty($this->item->vat_details)?$this->item->vat_details:"" ?></textarea>
                </div>
            </div>
        </div>            
    <?php } ?>
    
    <div class="form-divider"></div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="address"><?php echo JText::_('LNG_ADDRESS_LINE_1')?> </label>
                <input type="text" name="address" id="route" class="form-control input_txt" value="<?php echo $this->item->address ?>" maxlength="55">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="street_number"><?php echo JText::_('LNG_ADDRESS_LINE_2') ?></label>
                <input type="text" name="street_number" id="street_number" class="form-control text-input" value="<?php echo $this->item->street_number ?>" maxlength="100" placeholder="<?php echo JText::_('LNG_ADDRESS2_PLACEHOLDER') ?>">
            </div>
        </div>
    </div>

    <?php if ($this->appSettings->limit_cities_regions != 1) { ?>
        <div class="row">
            <div class="col-md">
                <div class="form-group">
                    <label for="city"><?php echo JText::_('LNG_CITY')?> </label>
                    <input type="text" name="city" id="locality" class="form-control input_txt" value="<?php echo $this->item->city ?>" maxlength="45">
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label for="postal_code"><?php echo JText::_('LNG_POSTAL_CODE')?> </label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control input_txt" value="<?php echo $this->item->postal_code ?>" maxlength="45">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <div class="form-group">
                    <label for="region"><?php echo JText::_('LNG_REGION')?> </label>
                    <input type="text" name="region" id="administrative_area_level_1" class="form-control input_txt" value="<?php echo $this->item->region ?>" maxlength="45">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="country"><?php echo JText::_('LNG_COUNTRY')?></label>
                    <select class="form-control" name="country" id="country" <?php echo $this->appSettings->limit_cities_regions ? 'onclick="jbdUtils.updateRegionsByCountry()"' : ''; ?>>
                        <option value=''><?php echo JText::_('LNG_SELECT_COUNTRY') ?></option>
                        <?php foreach( $this->item->countries as $country ) { ?>
                            <option <?php echo isset($this->item->country->id) && $this->item->country->id==$country->id? "selected" : ""?> value='<?php echo $country->id?>'><?php echo $country->country_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    <?php }else{ ?>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="country"><?php echo JText::_('LNG_COUNTRY')?></label>
                    <select class="form-control" name="country" id="country" <?php echo $this->appSettings->limit_cities_regions ? 'onclick="jbdUtils.updateRegionsByCountry()"' : ''; ?>>
                        <option value=''><?php echo JText::_('LNG_SELECT_COUNTRY') ?></option>
                        <?php foreach( $this->item->countries as $country ) { ?>
                            <option <?php echo isset($this->item->country->id) && $this->item->country->id==$country->id? "selected" : ""?> value='<?php echo $country->id?>'><?php echo $country->country_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label for="region"><?php echo JText::_('LNG_REGION')?> </label>
                    <?php if ($this->appSettings->limit_cities_regions != 1) { ?>
                        <input type="text" name="region" id="administrative_area_level_1" class="form-control input_txt" value="<?php echo $this->item->region ?>" maxlength="45">
                    <?php } else { ?>
                        <select name="region" id="administrative_area_level_1" class="form-control" onchange="jbdUtils.updateCitiesByRegion()" >
                            <option value=""><?php echo JText::_('LNG_SELECT_REGION') ?></option>
                            <?php foreach ($this->item->regions as $region) { ?>
                                <option value="<?php echo $region->name ?>" <?php echo  $this->item->region == $region->name ? "selected" : "" ?>><?php echo $region->name ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <div class="form-group">
                    <label for="city"><?php echo JText::_('LNG_CITY')?> </label>
                    <?php if ($this->appSettings->limit_cities_regions != 1) { ?>
                        <input type="text" name="city" id="locality" class="form-control input_txt" value="<?php echo $this->item->city ?>" maxlength="45">
                    <?php } else { ?>
                        <select name="city" id="locality" class="form-control" >
                            <option value=""><?php echo JText::_('LNG_SELECT_CITY') ?></option>
                            <?php foreach ($this->item->cities as $city) { ?>
                                <option value="<?php echo $city->name ?>" <?php echo $this->item->city == $city->name ? "selected" : "" ?> ><?php echo $city->name ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label for="postal_code"><?php echo JText::_('LNG_POSTAL_CODE')?> </label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control input_txt" value="<?php echo $this->item->postal_code ?>" maxlength="45">
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<input type="hidden" name="id" id="details_id" value="<?php echo !empty($this->item->id)?$this->item->id:""; ?>" />
<script>
    window.addEventListener('load', function() {
        <?php if ($this->appSettings->limit_cities_regions == 1) { ?>
            jbdUtils.updateRegionsByCountry();
            jbdUtils.updateCitiesByRegion();
        <?php } ?>
    })
</script>