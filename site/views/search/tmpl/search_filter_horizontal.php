<?php
/**
 * @copyright    Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
    
    $showClearFilter = false;

?>

<div id="search-filter-horizontal" class="search-filter-horizontal">

 	<?php if(!empty($this->searchkeyword)) { ?>
 		 <div class="search-options-item">
 		 	<div class="jbd-input-box"> 
 		 		<i class="la la-pencil"></i>
				<a onclick="jbdUtils.removeSearchRule('keyword')"><?php echo $this->searchkeyword; ?>&nbsp;&nbsp;x</a>
			</div>        	
		</div>
		<?php $showClearFilter = true; ?>
	<?php } ?>

    <?php if (!empty($this->searchFilter["categories"])) { ?>
    
        <div class="search-options-item">
        	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="categories" class="chosen" onchange="jbdUtils.chooseCategory(this.value)">
                    <option value=""><?php echo JText::_("LNG_CATEGORY") ?></option>
    				<?php
    				foreach ($this->searchFilter["categories"] as $filterCriteria) {
                        if ($filterCriteria[1] > 0) {
                            $selected = "";
                            if (isset($this->category) && $filterCriteria[0][0]->id == $this->category->id) {
                                $selected = "selected";
                            } ?>
                            <option value="<?php echo $filterCriteria[0][0]->id ?>" <?php echo $selected?> ><?php echo $filterCriteria[0][0]->name; ?></option>
                        <?php }
                    } ?>
                </select>
            </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["starRating"])) { ?>
        <div class="search-options-item">
        	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="starRating" class="chosen" onchange="jbdListings.addFilterRule('starRating', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_RATING") ?></option>
                    <?php
    				foreach ($this->searchFilter["starRating"] as $filterCriteria) { ?>
    					<?php if (empty($filterCriteria->reviewScore)) continue; ?>
    					<?php $selected = isset($this->selectedParams["starRating"]) && in_array($filterCriteria->reviewScore, $this->selectedParams["starRating"]); ?>
                        <option value="<?php echo $filterCriteria->reviewScore ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->reviewScore ?></option>
    				<?php } ?>
                </select>
            </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["types"])) { ?>
        <div class="search-options-item">
        	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="types" class="chosen" onchange="jbdListings.addFilterRule('type', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_TYPE") ?></option>
                    <?php
    				foreach ($this->searchFilter["types"] as $filterCriteria) { ?>
                        <?php if (empty($filterCriteria->typeName)) {
                            continue;
                        } ?>
                        <?php $selected = isset($this->selectedParams["type"]) && in_array($filterCriteria->typeId, $this->selectedParams["type"]); ?>
                        <option value="<?php echo $filterCriteria->typeId ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->typeName; ?></option>
                        <?php
                    }
    				?>
                </select>
            </div>
         </div>
         <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["countries"])) { ?>
        <div class="search-options-item">
           	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="countries" class="chosen" onchange="jbdListings.addFilterRule('country', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_COUNTRY") ?></option>
                    <?php
    				foreach ($this->searchFilter["countries"] as $filterCriteria) { ?>
                        <?php if (empty($filterCriteria->countryName)) {
                            continue;
                        } ?>
                        <?php $selected = isset($this->selectedParams["country"]) && in_array($filterCriteria->countryId, $this->selectedParams["country"]); ?>
                        <option value="<?php echo $filterCriteria->countryId ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->countryName; ?></option>
                        <?php
                    }
    				?>
                </select>
            </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["regions"])) { ?>
        <div class="search-options-item">
        	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="regions" class="chosen" onchange="jbdListings.addFilterRule('region', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_REGION") ?></option>
                    <?php
    				foreach ($this->searchFilter["regions"] as $filterCriteria) { ?>
                        <?php if (empty($filterCriteria->regionName)) {
                            continue;
                        } ?>
                        <?php $selected = isset($this->selectedParams["region"]) && in_array($filterCriteria->region, $this->selectedParams["region"]); ?>
                        <option value="<?php echo $filterCriteria->region ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->regionName; ?></option>
                        <?php
                    }
    				?>
                </select>
            </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["cities"])) { ?>
        <div class="search-options-item">
           	<div class="jbd-select-box"> 
            	<i class="la la-list"></i>
                <select name="cities" class="chosen" onchange="jbdListings.addFilterRule('city', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_CITY") ?></option>
                    <?php
    				foreach ($this->searchFilter["cities"] as $filterCriteria) { ?>
                        <?php if (empty($filterCriteria->cityName)) {
                            continue;
                        } ?>
                        <?php $selected = isset($this->selectedParams["city"]) && in_array($filterCriteria->city, $this->selectedParams["city"]); ?>
                        <option value="<?php echo $filterCriteria->city ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->cityName; ?></option>
                        <?php
                    }
    				?>
                </select>
             </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["areas"])) { ?>
        <div class="search-options-item">
        	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="areas" class="chosen" onchange="jbdListings.addFilterRule('area', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_AREA") ?></option>
                    <?php
    				foreach ($this->searchFilter["areas"] as $filterCriteria) { ?>
                        <?php if (empty($filterCriteria->areaName)) {
                            continue;
                        } ?>
                        <?php $selected = isset($this->selectedParams["area"]) && in_array($filterCriteria->areaName, $this->selectedParams["area"]); ?>
                        <option value="<?php echo $filterCriteria->areaName ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->areaName; ?></option>
                        <?php
                    }
    				?>
                </select>
            </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>

	<?php if (!empty($this->searchFilter["provinces"])) { ?>
        <div class="search-options-item">
          	<div class="jbd-select-box"> 
        		<i class="la la-list"></i>
                <select name="provinces" class="chosen" onchange="jbdListings.addFilterRule('province', this.value)">
                    <option value=""><?php echo JText::_("LNG_SELECT_PROVINCE") ?></option>
                    <?php
    				foreach ($this->searchFilter["provinces"] as $filterCriteria) { ?>
                        <?php if (empty($filterCriteria->provinceName)) {
                            continue;
                        } ?>
                        <?php $selected = isset($this->selectedParams["province"]) && in_array($filterCriteria->provinceName, $this->selectedParams["province"]); ?>
                        <option value="<?php echo $filterCriteria->provinceName ?>" <?php echo ($selected) ? "selected" : ""; ?>><?php echo $filterCriteria->provinceName; ?></option>
                        <?php
                    }
    				?>
                </select>
             </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>
	
	<?php if(!empty($this->customAtrributesValues)) {
        foreach ($this->customAtrributesValues as $attribute) { ?>
        	 <?php if(!empty($attribute)){ ?>
                <div class="search-options-item">
     		 		<div class="jbd-input-box"> 
                        <a class="filter-type-elem"
                           onclick="jbdUtils.removeAttrCond(<?php echo $attribute->attribute_id ?>)"><?php echo $attribute->name; ?>&nbsp;&nbsp;x</a>
                   </div>        	
    			</div>
			<?php } ?>
		  <?php } ?>
          <?php $showClearFilter = true; ?>
	<?php } ?>

    <?php if(!empty($this->zipCode)) { ?>
        <div class="search-options-item">
 		 	<div class="jbd-input-box"> 
 		 		<i class="la la-map-marker"></i>
				 <a class="filter-type-elem" onclick="jbdUtils.removeSearchRule('zipcode')"><?php echo $this->zipCode; ?>&nbsp;&nbsp;x</a>
			</div>        	
		</div>
       <?php $showClearFilter = true; ?>
	<?php } ?>
	
	<?php if (!empty($this->location["latitude"])) { ?>
        <div class="search-options-item">
            <div class="jbd-select-box"> 
            	<i class="la la-list"></i>
                <select name="distance" class="chosen" onchange="jbdListings.setRadius(this.value)">
                    <option value="0"><?php echo JText::_("LNG_RADIUS") ?></option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
        <?php $showClearFilter = true; ?>
	<?php } ?>
	
	<?php if($showClearFilter){ ?>
    	<div class="search-options-item">
    		<a class="clear-search" href="javascript:jbdUtils.resetFilters(true, true)" style="text-decoration: none;"><i class="la la-close"></i></a>
    	</div>
	<?php } ?>

</div>
