<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class FormService {

    public static function renderAddressFields($attributeConfig, $item){
        $appSettings = JBusinessUtil::getApplicationSettings();
        $db = JFactory::getDBO();

        ?>
            <div class="row">
                <?php if ($attributeConfig["address"] != ATTRIBUTE_NOT_SHOW) { ?>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="address"><?php echo JText::_('LNG_ADDRESS_LINE_1') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["address"]) ?></label>
                            <input type="text" name="address" id="route" class="form-control map-autocomplete <?php echo $attributeConfig["address"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" value="<?php echo $db->escape($item->address) ?>" maxlength="100">
                        </div>
                    </div>
                <?php } ?>

                <?php if ($attributeConfig["street_number"] != ATTRIBUTE_NOT_SHOW) { ?>
                    <div class="col-12">
                        <div class="form-group">
                            <?php if ($attributeConfig["street_number"] == ATTRIBUTE_MANDATORY) { ?>
                                <div class="form-detail req"></div>
                            <?php } ?>
                            <label for="street_number"><?php echo JText::_('LNG_ADDRESS_LINE_2') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["street_number"]) ?></label>
                            <input type="text" name="street_number" id="street_number" class="form-control text-input <?php echo $attributeConfig["street_number"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" value="<?php echo $db->escape($item->street_number) ?>" maxlength="100" placeholder="<?php echo JText::_('LNG_ADDRESS2_PLACEHOLDER') ?>">
                        </div>
                    </div>
                <?php } ?>

                <?php if ($attributeConfig["area"] != ATTRIBUTE_NOT_SHOW && isset($item->area)) { ?>
                    <div class="col-12">
                        <div class="form-group" id="districtContainer">
                            <?php if ($attributeConfig["area"] == ATTRIBUTE_MANDATORY) { ?>
                                <div class="form-detail req"></div>
                            <?php } ?>
                            <label for="area_id"><?php echo JText::_('LNG_AREA') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["area"]) ?></label>
                            <input class="form-control <?php echo $attributeConfig["area"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" type="text" name="area" id="area_id" value="<?php echo $db->escape($item->area) ?>" maxlength="60" />
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php if ($appSettings->limit_cities_regions != 1) { ?>
                <div class="row">
                    <?php if ($attributeConfig["city"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group">
                                <?php if ($attributeConfig["city"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>

                                <label for="city_id"><?php echo JText::_('LNG_CITY') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["city"]) ?></label>
                                <input name="city" id="locality" class="form-control <?php echo $attributeConfig["city"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" type="text" value="<?php echo $db->escape($item->city) ?>" maxlength="60">
                            </div>
                        </div>
                    <?php } ?>
                   
                    <?php if ($attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group" id="districtContainer">
                                <?php if ($attributeConfig["postal_code"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <label for="district_id"><?php echo JText::_('LNG_POSTAL_CODE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["postal_code"]) ?></label>
                                <input class="form-control <?php echo $attributeConfig["postal_code"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="postalCode" id="postal_code" value="<?php echo $db->escape($item->postalCode) ?>" maxlength="55" />
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <?php if ($attributeConfig["region"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group" id="districtContainer">
                                <?php if ($attributeConfig["region"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <label for="county"><?php echo JText::_('LNG_COUNTY') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["region"]) ?></label>
                                <input name="county" id="administrative_area_level_1" class="form-control <?php echo $attributeConfig["region"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" type="text" value="<?php echo $db->escape($item->county) ?>" maxlength="60" />
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($attributeConfig["province"] != ATTRIBUTE_NOT_SHOW && isset($item->province)) { ?>
                        <div class="col-md">
                            <div class="form-group" id="districtContainer">
                                <label for="province_id"><?php echo JText::_('LNG_PROVINCE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["province"]) ?></label>
                                <input class="form-control <?php echo $attributeConfig["province"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" type="text" name="province" id="administrative_area_level_2" value="<?php echo $db->escape($item->province) ?>" maxlength="60" />
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($attributeConfig["country"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group">
                                <?php if ($attributeConfig["country"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <label for="country"><?php echo JText::_('LNG_COUNTRY') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["country"]) ?></label>
                                <select data-placeholder="<?php echo JText::_("LNG_SELECT_COUNTRY") ?>" class="form-control <?php echo $attributeConfig["country"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> select" name="countryId" id="country">
                                    <option value=''></option>
                                    <?php foreach ($item->countries as $country) { ?>
                                        <option <?php echo $item->countryId == $country->id ? "selected" : "" ?> value='<?php echo $country->id ?>'><?php echo $country->country_name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            <?php }else{ ?>
                <div class="row">
                    <?php if ($attributeConfig["country"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group">
                                <?php if ($attributeConfig["country"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <label for="country"><?php echo JText::_('LNG_COUNTRY') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["country"]) ?></label>
                                <select data-placeholder="<?php echo JText::_("LNG_SELECT_COUNTRY") ?>" class="form-control <?php echo $attributeConfig["country"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> select" name="countryId" id="country" onclick="<?php echo isset($item->activityRegions) ?"updateRegions('#country','#activity_regions','#activity_cities')":"updateRegions()"?>">
                                    <option value=''></option>
                                    <?php foreach ($item->countries as $country) { ?>
                                        <option <?php echo $item->countryId == $country->id ? "selected" : "" ?> value='<?php echo $country->id ?>'><?php echo $country->country_name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($attributeConfig["province"] != ATTRIBUTE_NOT_SHOW && isset($item->province)) { ?>
                        <div class="col-md">
                            <div class="form-group" id="districtContainer">
                                <label for="province_id"><?php echo JText::_('LNG_PROVINCE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["province"]) ?></label>
                                <input class="form-control <?php echo $attributeConfig["province"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?> text-input" type="text" name="province" id="administrative_area_level_2" value="<?php echo $db->escape($item->province) ?>" maxlength="60" />
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <?php if ($attributeConfig["region"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group" id="districtContainer">
                                <?php if ($attributeConfig["region"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <?php if(isset($item->activityRegions)){?>
                                    <label for="activity_regions"><?php echo JText::_('LNG_SELECT_REGION') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["region"]) ?></label>
                                    <select id="activity_regions" class="form-control input-medium chosen-select <?php echo $attributeConfig["region"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" name="activity_regions[]" onchange="updateCities('#activity_regions','#activity_cities')">
                                        <?php
                                        foreach ($item->regions as $region) {
                                            $selected = false;
                                            foreach ($item->activityRegions as $aregion) {
                                                if ($aregion->region_id == $region->id)
                                                    $selected = true;
                                            } ?>
                                            <option <?php echo $selected ? "selected" : "" ?> value='<?php echo $region->id ?>'>
                                                <?php echo $region->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <label for="county"><?php echo JText::_('LNG_REGION') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["region"])?></label>
                                    <select name="county" id="administrative_area_level_1" class="input_txt <?php echo ($attributeConfig["region"] == ATTRIBUTE_MANDATORY) ? "validate[required]" : "" ?>" onchange="updateCities()" >
                                        <option value=""><?php echo JText::_('LNG_SELECT_REGION') ?></option>
                                        <?php foreach ($item->regions as $region) { ?>
                                            <option value="<?php echo $region->name ?>" <?php echo $item->county == $region->name ? "selected" : "" ?>><?php echo $region->name ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($attributeConfig["city"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group">
                                <?php if ($attributeConfig["city"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <?php if(isset($item->activityRegions)){?>
                                    <label for="activity_cities"><?php echo JText::_('LNG_SELECT_CITY') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["city"]) ?></label>
                                    <select id="activity_cities" class="form-control input-medium chosen-select  <?php echo $attributeConfig["city"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" name="activity_cities[]">
                                        <?php
                                        foreach ($item->cities as $city) {
                                            $selected = false;
                                            foreach ($item->activityCities as $acity) {
                                                if ($acity->city_id == $city->id)
                                                    $selected = true;
                                            } ?>
                                            <option <?php echo $selected ? "selected" : "" ?> value='<?php echo $city->id ?>'>
                                                <?php echo $city->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <label for="city"><?php echo JText::_('LNG_CITY') ?><?php echo JBusinessUtil::showMandatory($attributeConfig["city"])?></label>
                                    <select name="city" id="locality" class="input_txt <?php echo ($attributeConfig["city"] == ATTRIBUTE_MANDATORY) ? "validate[required]" : "" ?>" >
                                        <option value=""><?php echo JText::_('LNG_SELECT_CITY') ?></option>
                                        <?php foreach ($item->cities as $city) { ?>
                                            <option value="<?php echo $city->name ?>" <?php echo $item->city == $city->name ? "selected" : "" ?> ><?php echo $city->name ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($attributeConfig["postal_code"] != ATTRIBUTE_NOT_SHOW) { ?>
                        <div class="col-md">
                            <div class="form-group" id="districtContainer">
                                <?php if ($attributeConfig["postal_code"] == ATTRIBUTE_MANDATORY) { ?>
                                    <div class="form-detail req"></div>
                                <?php } ?>
                                <label for="district_id"><?php echo JText::_('LNG_POSTAL_CODE') ?> <?php echo JBusinessUtil::showMandatory($attributeConfig["postal_code"]) ?></label>
                                <input class="form-control <?php echo $attributeConfig["postal_code"] == ATTRIBUTE_MANDATORY ? "validate[required]" : "" ?>" type="text" name="postalCode" id="postal_code" value="<?php echo $db->escape($item->postalCode) ?>" maxlength="55" />
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>          
        <?php
    }

}