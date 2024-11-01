<div id="filter-switch" class="filter-switch">
	<?php echo JText::_("LNG_SHOW_FILTER")?>
</div>
<div id="search-filter" class="search-filter">
	<div class="filter-fav clear" style="display:none"> 
		<a href="javascript:filterByFavorites(<?php echo $user->ID==0?'false':'true' ?>)" style="float:right;padding:5px;"><?php echo JText::_('LNG_FILTER_BY_FAVORITES'); ?></a>
	</div>
	<div class="search-category-box">
		 <?php if(!empty($this->location["latitude"])) { ?>
		 	<div class="filter-criteria">
				<div class="filter-header"><?php echo JText::_("LNG_DISTANCE"); ?></div>
				<ul>
					<li>
						<?php if($this->radius != 50) { ?>
							<a href="javascript:setRadius(50)">50 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
						<?php } else { ?>
							<strong>50 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
						<?php } ?>
					</li>
					<li>
						<?php if($this->radius != 25) { ?>
							<a href="javascript:setRadius(25)">25 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
						<?php } else { ?>
							<strong>25 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
						<?php } ?>
					</li>
					<li>
						<?php if($this->radius != 10) { ?>
							<a href="javascript:setRadius(10)">10 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></a>
						<?php } else { ?>
							<strong>10 <?php echo $this->appSettings->metric==1?JText::_("LNG_MILES"):JText::_("LNG_KM") ?></strong>
						<?php } ?>
					</li>
					<li>
						<?php if($this->radius != 0) { ?>
							<a href="javascript:setRadius(0)"><?php echo JText::_("LNG_ALL")?></a>
						<?php } else { ?>
							<strong><?php echo JText::_("LNG_ALL")?></strong>
						<?php } ?>
					</li>
				</ul>
			</div>
		<?php } ?>

		<div id="filterCategoryItems"  class="">
		<?php if(!empty($this->searchFilter["categories"])) { ?>
			<div class="filter-criteria">
		        <div class="filter-header"><?php echo JText::_("LNG_CATEGORIES") ?></div>
		        <?php if ($this->appSettings->search_type == 0) {
		            $counterCategories = 0; ?>
		            <ul>
		                <?php foreach ($this->searchFilter["categories"] as $filterCriteria) {
		                    if ($counterCategories < $this->appSettings->search_filter_items) {
		                        if ($filterCriteria[1] > 0) { ?>
		                            <li>
		                                <?php if (isset($this->category) && $filterCriteria[0][0]->id == $this->category->id) { ?>
		                                    <strong><?php echo $filterCriteria[0][0]->name; ?>
		                                        &nbsp;</strong>
		                                <?php } else { ?>
		                                    <a href="javascript:chooseCategory(<?php echo $filterCriteria[0][0]->id ?>)"><?php echo $filterCriteria[0][0]->name; ?></a>
		                                <?php } ?>
		                            </li>
		                        <?php }
		                        $counterCategories++;
		                    } else { ?>
		                        <a id="showMoreCategories" class="filterExpand" href="javascript:void(0)"
		                           onclick="showMoreParams('extra_categories_params','showMoreCategories')"><?php echo JText::_("LNG_MORE") . " (+)" ?></a>
		                        <?php break;
		                    }
		                } ?>

		                <div style="display: none" id="extra_categories_params">
		                    <?php
		                    foreach ($this->searchFilter["categories"] as $filterCriteria) {
		                        $counterCategories--; ?>
		                        <?php if ($counterCategories >= 0) {
		                            continue;
		                        } else {
		                            if ($filterCriteria[1] > 0) { ?>
		                                <li>
		                                    <?php if (isset($this->category) && $filterCriteria[0][0]->id == $this->category->id) { ?>
		                                        <strong><?php echo $filterCriteria[0][0]->name; ?>
		                                            &nbsp;</strong>
		                                    <?php } else { ?>
		                                        <a href="javascript:chooseCategory(<?php echo $filterCriteria[0][0]->id ?>)"><?php echo $filterCriteria[0][0]->name; ?></a>
		                                        <?php //echo '('.$filterCriteria[1].')' ?>
		                                    <?php } ?>
		                                </li>
		                            <?php }
		                        }
		                    } ?>
		                    <a id="showLessCategories" class="filterExpand" href="javascript:void(0)"
		                       onclick="showLessParams('extra_categories_params','showMoreCategories')"><?php echo JText::_("LNG_LESS") . " (-)" ?></a>
		                </div>
		            </ul>
		        <?php } else { ?>
		            <ul class="filter-categories">
		                <?php $counterCategories = 0;
		                foreach ($this->searchFilter["categories"] as $filterCriteria) {
		                    if ($counterCategories < $this->appSettings->search_filter_items) {
		                        if ($filterCriteria[1] > 0) { ?>
		                            <li <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
		                                <div <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
		                                    <a href="javascript:void(0)" class="filter-main-cat"
		                                       onclick="<?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $filterCriteria[0][0]->id . ")" : "addFilterRuleCategory(" . $filterCriteria[0][0]->id . ")"; ?>"> <?php echo $filterCriteria[0][0]->name ?><?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                    <?php //echo '('.$filterCriteria[1].')' ?>
		                                </div>
		                                <?php if (isset($filterCriteria[0]["subCategories"])) { ?>
		                                    <ul>
		                                        <?php foreach ($filterCriteria[0]["subCategories"] as $subcategory) { ?>
		                                            <li <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
		                                                <div <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
		                                                    <a href="javascript:void(0)"
		                                                       onclick="<?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $subcategory[0]->id . ")" : "addFilterRuleCategory(" . $subcategory[0]->id . ")"; ?>"> <?php echo $subcategory[0]->name ?><?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                                </div>
		                                            </li>
		                                        <?php } ?>
		                                    </ul>
		                                <?php } ?>
		                            </li>
	                                <?php  $counterCategories++; ?>
   		                        <?php }?>
		                    <?php } else { ?>
		                        <a id="showMoreCategories1" class="filterExpand" href="javascript:void(0)"
		                           onclick="showMoreParams('extra_categories_params1','showMoreCategories1')"><?php echo JText::_("LNG_MORE") . " (+)" ?></a>
		                        <?php break;
		                    }
		                } ?>
		                <div style="display: none" id="extra_categories_params1">
		                    <?php
		                    foreach ($this->searchFilter["categories"] as $filterCriteria) {
		                        $counterCategories--; ?>
		                        <?php if ($counterCategories >= 0) {
		                            continue;
		                        } else {
		                            if ($filterCriteria[1] > 0) { ?>
		                                <li <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
		                                    <div <?php if (in_array($filterCriteria[0][0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
		                                        <a href="javascript:void(0)" class="filter-main-cat"
		                                           onclick="<?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $filterCriteria[0][0]->id . ")" : "addFilterRuleCategory(" . $filterCriteria[0][0]->id . ")"; ?>"> <?php echo $filterCriteria[0][0]->name ?><?php echo in_array($filterCriteria[0][0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                        <?php //echo '('.$filterCriteria[1].')' ?>
		                                    </div>
		                                    <?php if (isset($filterCriteria[0]["subCategories"])) {
		                                        $counterCategories = 0; ?>
		                                        <ul>
		                                            <?php foreach ($filterCriteria[0]["subCategories"] as $subcategory) { ?>
		                                                <li <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selectedlink"'; ?>>
		                                                    <div <?php if (in_array($subcategory[0]->id, $this->selectedCategories)) echo 'class="selected"'; ?>>
		                                                        <a href="javascript:void(0)"
		                                                           onclick="<?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? "removeFilterRuleCategory(" . $subcategory[0]->id . ")" : "addFilterRuleCategory(" . $subcategory[0]->id . ")"; ?>"> <?php echo $subcategory[0]->name ?><?php echo in_array($subcategory[0]->id, $this->selectedCategories) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                                    </div>
		                                                </li>
		                                            <?php } ?>
		                                        </ul>
		                                    <?php } ?>
		                                </li>
		                            <?php }
		                        }
		                    } ?>
		                    <a id="showLessCategories1" class="filterExpand" href="javascript:void(0)"
		                       onclick="showLessParams('extra_categories_params1','showMoreCategories1')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
		                </div>
		            </ul>
		        
	        		<?php } ?>
	        		<div class="clear"></div>
	        	</div>
	    	<?php } ?>
		
			<?php $searchType = 1;?>

            <?php if(!empty($this->searchFilter["starRating"])) { ?>
                <div class="filter-criteria">
                    <div class="filter-header"><?php echo JText::_("LNG_STAR_RATING") ?></div>
                    <ul>
                        <?php
                        foreach($this->searchFilter["starRating"] as $filterCriteria) { ?>
                            <?php if(empty($filterCriteria->reviewScore)) continue; ?>
                            <?php $selected = isset($this->selectedParams["starRating"]) && in_array($filterCriteria->reviewScore, $this->selectedParams["starRating"]); ?>
                            <li <?php if($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
                                <div <?php if($selected) echo 'class="selected"'; ?>>
                                    <a href="javascript:void(0)" onclick="<?php echo $selected?"removeFilterRule('starRating', ".$filterCriteria->reviewScore.")":"addFilterRule('starRating', ".$filterCriteria->reviewScore.")";?>"><?php echo $filterCriteria->reviewScore." ".JText::_("LNG_STARS"); ?><?php echo ($selected)?'<span class="cross">(remove)</span>':"";  ?></a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

			<?php if(!empty($this->searchFilter["types"])) { ?>
				<div class="filter-criteria">
		            <div class="filter-header"><?php echo JText::_("LNG_TYPES") ?></div>
		            <ul>
		                <?php $counterTypes = 0;
		                foreach ($this->searchFilter["types"] as $filterCriteria) { ?>
		                    <?php if (empty($filterCriteria->typeName)){continue;} ?>
		                    <?php if ($counterTypes < $this->appSettings->search_filter_items) { ?>
		                        <?php $selected = isset($this->selectedParams["type"]) && in_array($filterCriteria->typeId, $this->selectedParams["type"]); ?>
		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                <a href="javascript:void(0)"
		                                   onclick="<?php echo ($selected) ? "removeFilterRule('type', " . $filterCriteria->typeId . ")" : "addFilterRule('type', " . $filterCriteria->typeId . ")"; ?>"><?php echo $filterCriteria->typeName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                            </div>
		                        </li>
		                        <?php $counterTypes++;
		                    } else { ?>
		                        <a id="showMoreTypes" class="filterExpand" href="javascript:void(0)"
		                           onclick="showMoreParams('extra_types_params','showMoreTypes')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
		                        <?php break;
		                    }
		                }
		                ?>
		                <div style="display: none" id="extra_types_params">
		                    <?php
		                    foreach ($this->searchFilter["types"] as $filterCriteria) {
		                      	if (empty($filterCriteria->typeName)) {
		                            continue;
		                      	}else if ($counterTypes > 0) {
		                           $counterTypes--; 
		                           continue;
		                        } else {
		                            $selected = isset($this->selectedParams["type"]) && in_array($filterCriteria->typeId, $this->selectedParams["type"]); ?>
		                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                                <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                    <a href="javascript:void(0)"
		                                       onclick="<?php echo ($selected) ? "removeFilterRule('type', " . $filterCriteria->typeId . ")" : "addFilterRule('type', " . $filterCriteria->typeId . ")"; ?>"><?php echo $filterCriteria->typeName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                </div>
		                            </li>
		                            <?php
		                        }
		                    } ?>
		                    <a id="showLessTypes" class="filterExpand" href="javascript:void(0)"
		                       onclick="showLessParams('extra_types_params','showMoreTypes')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
		                </div>
		            </ul>
		            <div class="clear"></div>
		          </div>
	        <?php } ?>
	     
			<?php if(!empty($this->searchFilter["countries"])) { ?>
				<div class="filter-criteria">
	            	<div class="filter-header"><?php echo JText::_("LNG_COUNTRIES") ?></div>
		            <ul>
		                <?php $counterCountries = 0;
		                foreach ($this->searchFilter["countries"] as $filterCriteria) { ?>
		                    <?php if (empty($filterCriteria->countryName)){continue;}?>
		                    <?php $selected = isset($this->selectedParams["country"]) && in_array($filterCriteria->countryId, $this->selectedParams["country"]); ?>
		                    <?php if ($counterCountries < $this->appSettings->search_filter_items) { ?>
		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                <a href="javascript:void(0)"
		                                   onclick="<?php echo $selected ? "removeFilterRule('country', " . $filterCriteria->countryId . ")" : "addFilterRule('country', " . $filterCriteria->countryId . ")"; ?>"><?php echo $filterCriteria->countryName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                            </div>
		                        </li>
		                        <?php $counterCountries++;
		                    } else { ?>
		                        <a id="showMoreCountries" class="filterExpand" href="javascript:void(0)"
		                           onclick="showMoreParams('extra_countries_params','showMoreCountries')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
		                        <?php break;
		                    } ?>
		                <?php } ?>
		                <div style="display: none" id="extra_countries_params">
		                    <?php
		                    foreach ($this->searchFilter["countries"] as $filterCriteria) {
		                        if(empty($filterCriteria->countryName)) {
		                            continue;
		                        }else if ($counterCountries > 0) {
		                        	$counterCountries--;
		                           	continue;
		                        } else
		                            $selected = isset($this->selectedParams["country"]) && in_array($filterCriteria->countryId, $this->selectedParams["country"]); ?>
		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                <a href="javascript:void(0)"
		                                   onclick="<?php echo $selected ? "removeFilterRule('country', " . $filterCriteria->countryId . ")" : "addFilterRule('country', " . $filterCriteria->countryId . ")"; ?>"><?php echo $filterCriteria->countryName; ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                            </div>
		                        </li>
		                        <?php
		                    } ?>
		                    <a id="showLessCountries" class="filterExpand" href="javascript:void(0)"
		                       onclick="showLessParams('extra_countries_params','showMoreCountries')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
		                </div>
		            </ul>
		            <div class="clear"></div>
	            </div>
	        <?php } ?>
			
			<?php if(!empty($this->searchFilter["regions"])) { ?>
				<div class="filter-criteria">
		            <div class="filter-header"><?php echo JText::_("LNG_REGIONS") ?></div>
		            <ul>
		                <?php $counterRegions = 0;
		                foreach ($this->searchFilter["regions"] as $filterCriteria) { ?>
		                    <?php if (empty($filterCriteria->regionName)){continue;} ?>
		                    <?php if ($counterRegions < $this->appSettings->search_filter_items) { ?>
		                        <?php $selected = isset($this->selectedParams["region"]) && in_array($filterCriteria->region, $this->selectedParams["region"]); ?>
		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                            <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                <a href="javascript:void(0)"
		                                   onclick="<?php echo $selected ? "removeFilterRule('region', &quot;" . $this->escape($filterCriteria->region) . "&quot;)" : "addFilterRule('region', &quot;".$this->escape($filterCriteria->region)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->regionName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                            </div>
		                        </li>
		                        <?php $counterRegions++;
		                    } else { ?>
		                        <a id="showMoreRegions" class="filterExpand" href="javascript:void(0)"
		                           onclick="showMoreParams('extra_regions_params','showMoreRegions')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
		                        <?php break;
		                    } ?>
		                <?php } ?>
		                <div style="display: none" id="extra_regions_params">
		                    <?php
		                    foreach ($this->searchFilter["regions"] as $filterCriteria) {
		                        if (empty($filterCriteria->regionName)) {
		                            continue;
		                        }else if ($counterRegions > 0) {
		                        	$counterRegions--;
		                            continue;
		                        } else {
		                            $selected = isset($this->selectedParams["regions"]) && in_array($filterCriteria->region, $this->selectedParams["city"]); ?>
		                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                                <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                    <a href="javascript:void(0)"
		                                       onclick="<?php echo $selected ? "removeFilterRule('region', &quot;" . $this->escape($filterCriteria->region) . "&quot;)" : "addFilterRule('region', &quot;".$this->escape($filterCriteria->region)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->regionName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                </div>
		                            </li>
		                            <?php
		                        }
		                    } ?>
		                    <a id="showLessRegions" class="filterExpand" href="javascript:void(0)"
		                       onclick="showLessParams('extra_regions_params','showMoreRegions')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
		                </div>
		            </ul>
		            <div class="clear"></div>
		        </div>
	        <?php } ?>
	
			<?php if(!empty($this->searchFilter["cities"])) { ?>
				<div class="filter-criteria">
		            <div class="filter-header"><?php echo JText::_("LNG_CITIES") ?></div>
		            <ul>
		                <?php $counterCities = 0;
		                foreach ($this->searchFilter["cities"] as $filterCriteria) { ?>
		                    <?php if (empty($filterCriteria->cityName)){$counterCities++; continue;} ?>
		                    <?php $selected = isset($this->selectedParams["city"]) && in_array($filterCriteria->city, $this->selectedParams["city"]); ?>
		                    <?php if ($counterCities < $this->appSettings->search_filter_items) { ?>
		                        <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                            <div <?php if ($selected) echo 'class="selected"'; ?> class="selectedlink">
		                                <a href="javascript:void(0)"
		                                   onclick="<?php echo $selected ? "removeFilterRule('city', &quot;" . $this->escape($filterCriteria->city) . "&quot;)" : "addFilterRule('city', &quot;".$this->escape($filterCriteria->city)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->cityName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                            </div>
		                        </li>
		                        <?php $counterCities++;
		                    } else { ?>
		                        <a id="showMoreCities" class="filterExpand" href="javascript:void(0)"
		                           onclick="showMoreParams('extra_cities_params','showMoreCities')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
		                        <?php break;
		                    } ?>
		                <?php } ?>
		                <div style="display: none" id="extra_cities_params">
		                    <?php
		                    foreach ($this->searchFilter["cities"] as $filterCriteria) {
		                        if (empty($filterCriteria->cityName)) {
		                            continue;
		                        }else if ($counterCities > 0) {
		                        	$counterCities--;
		                            continue;
		                    	} else {
		                            $selected = isset($this->selectedParams["city"]) && in_array($filterCriteria->city, $this->selectedParams["city"]); ?>
		                            <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
		                                <div <?php if ($selected) echo 'class="selected"'; ?>>
		                                    <a href="javascript:void(0)"
		                                       onclick="<?php echo $selected ? "removeFilterRule('city', &quot;" . $this->escape($filterCriteria->city) . "&quot;)" : "addFilterRule('city', &quot;".$this->escape($filterCriteria->city)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->cityName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
		                                </div>
		                            </li>
		                            <?php
		                        }
		                    } ?>
		                    <a id="showLessCities" class="filterExpand" href="javascript:void(0)"
		                       onclick="showLessParams('extra_cities_params','showMoreCities','showLessCities')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
		                </div>
		            </ul>
		            <div class="clear"></div>
		         </div>
	        <?php } ?>
	
			<?php if(!empty($this->searchFilter["areas"])) { ?>
				<div class="filter-criteria">
					<div class="filter-header"><?php echo JText::_("LNG_AREA") ?></div>
					<ul>
						<?php $counterAreas = 0;
						foreach ($this->searchFilter["areas"] as $filterCriteria) { ?>
							<?php if (empty($filterCriteria->areaName)){$counterAreas++;continue;} ?>
							<?php $selected = isset($this->selectedParams["area"]) && in_array($filterCriteria->areaName, $this->selectedParams["area"]); ?>
							<?php if ($counterAreas < $this->appSettings->search_filter_items) { ?>
								<li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
									<div <?php if ($selected) echo 'class="selected"'; ?>>
										<a href="javascript:void(0)"
										   onclick="<?php echo $selected ? "removeFilterRule('area', &quot;" . $this->escape($filterCriteria->areaName) . "&quot;)" : "addFilterRule('area', &quot;".$this->escape($filterCriteria->areaName)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->areaName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
									</div>
								</li>
								<?php $counterAreas++;
							} else { ?>
								<a id="showMoreAreas" class="filterExpand" href="javascript:void(0)"
								   onclick="showMoreParams('extra_areas_params','showMoreAreas')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
								<?php break;
							} ?>
						<?php } ?>
						<div style="display: none" id="extra_areas_params">
							<?php
							foreach ($this->searchFilter["areas"] as $filterCriteria) {
								if (empty($filterCriteria->areaName)) {
									continue;
								}else if ($counterAreas > 0) {
									$counterAreas--;
									continue;
								} else {
									$selected = isset($this->selectedParams["area"]) && in_array($filterCriteria->areaName, $this->selectedParams["area"]); ?>
									<li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
										<div <?php if ($selected) echo 'class="selected"'; ?>>
											<a href="javascript:void(0)"
											   onclick="<?php echo $selected ? "removeFilterRule('area', &quot;" . $this->escape($filterCriteria->areaName) . "&quot;)" : "addFilterRule('area', &quot;".$this->escape($filterCriteria->areaName)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->areaName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
										</div>
									</li>
									<?php
								}
							} ?>
							<a id="showLessAreas" class="filterExpand" href="javascript:void(0)"
							   onclick="showLessParams('extra_areas_params','showMoreAreas','showLessAreas')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
						</div>
					</ul>
					<div class="clear"></div>
				</div>
			<?php } ?>

            <?php if(!empty($this->searchFilter["provinces"])) { ?>
                <div class="filter-criteria">
                    <div class="filter-header"><?php echo JText::_("LNG_PROVINCE") ?></div>
                    <ul>
                        <?php $counterProvinces = 0;
                        foreach ($this->searchFilter["provinces"] as $filterCriteria) { ?>
                            <?php if (empty($filterCriteria->provinceName)){$counterProvinces++;continue;} ?>
                            <?php $selected = isset($this->selectedParams["province"]) && in_array($filterCriteria->provinceName, $this->selectedParams["province"]); ?>
                            <?php if ($counterProvinces < $this->appSettings->search_filter_items) { ?>
                                <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
                                    <div <?php if ($selected) echo 'class="selected"'; ?>>
                                        <a href="javascript:void(0)"
                                           onclick="<?php echo $selected ? "removeFilterRule('province', &quot;" . $this->escape($filterCriteria->provinceName) . "&quot;)" : "addFilterRule('province', &quot;".$this->escape($filterCriteria->provinceName)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->provinceName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
                                    </div>
                                </li>
                                <?php $counterProvinces++;
                            } else { ?>
                                <a id="showMoreProvinces" class="filterExpand" href="javascript:void(0)"
                                   onclick="showMoreParams('extra_provinces_params','showMoreProvinces')"><?php echo JText::_("LNG_MORE") . " (+)"  ?></a>
                                <?php break;
                            } ?>
                        <?php } ?>
                        <div style="display: none" id="extra_provinces_params">
                            <?php
                            foreach ($this->searchFilter["provinces"] as $filterCriteria) {
                                if (empty($filterCriteria->provinceName)) {
                                    continue;
                                }else if ($counterProvinces > 0) {
                                    $counterProvinces--;
                                    continue;
                                } else {
                                    $selected = isset($this->selectedParams["province"]) && in_array($filterCriteria->provinceName, $this->selectedParams["province"]); ?>
                                    <li <?php if ($searchType == 1 && $selected) echo 'class="selectedlink"'; ?>>
                                        <div <?php if ($selected) echo 'class="selected"'; ?>>
                                            <a href="javascript:void(0)"
                                               onclick="<?php echo $selected ? "removeFilterRule('province', &quot;" . $this->escape($filterCriteria->provinceName) . "&quot;)" : "addFilterRule('province', &quot;".$this->escape($filterCriteria->provinceName)."&quot;)"; ?>"><?php echo $this->escape($filterCriteria->provinceName); ?><?php echo ($selected) ? '<span class="cross">(remove)</span>' : ""; ?></a>
                                        </div>
                                    </li>
                                    <?php
                                }
                            } ?>
                            <a id="showLessProvinces" class="filterExpand" href="javascript:void(0)"
                               onclick="showLessParams('extra_provinces_params','showMoreProvinces','showLessProvinces')"><?php echo JText::_("LNG_LESS") . " (-)"  ?></a>
                        </div>
                    </ul>
                    <div class="clear"></div>
                </div>
            <?php } ?>
		</div>
	</div>
</div>