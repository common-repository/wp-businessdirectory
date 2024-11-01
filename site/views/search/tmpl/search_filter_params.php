<?php $showClear=0; ?>
<div id="search-path">
	<?php if(!empty($this->category) && ($this->appSettings->search_type != 1 || $this->appSettings->search_filter_type ==1) && false) { ?>
		<ul class="category-breadcrumbs">
			<li>
				<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search&resetSearch=1'.$menuItemId) ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
			</li>
			<?php 
			if(isset($this->searchFilter["path"])) {
				foreach($this->searchFilter["path"] as $path) {
					if($path[0]==1)
						continue;
				?>
					<li>
						<a class="search-filter-elem" href="<?php echo JBusinessUtil::getCategoryLink($path[0], $path[2]) ?>"><?php echo $path[1]?></a>
					</li>
				<?php } ?>
			<?php } ?>
			<li>
				<?php if(!empty($this->category)) echo $this->category->name ?>
			</li>
		</ul>
	<?php } ?>

    <?php if(!empty($this->selectedCategories)){?>
        <ul class="selected-criteria">
            <?php foreach($this->selectedCategories as $cat){?>
                <?php $category = JBusinessUtil::getCategoryItem($cat); ?>
                <li>
    				<a class="filter-type-elem" onclick="jbdUtils.removeFilterRuleCategory(<?php echo $cat ?>)"><?php echo $category->name; ?><i class="la la-times"></i></a>
    			</li>
                <?php $showClear++;?>
            <?php } ?>            
        </ul>
    <?php } ?>
	
    <?php if(!empty($this->selectedParams) && !empty( $this->selectedParams['selectedParams'])  
            || !empty($this->typeSearch)
            || !empty($this->countrySearch) || !empty($this->regionSearch) || !empty($this->citySearch) || !empty($this->provinceSearch)
            || !empty($this->location) || !empty($this->selectedParams["membership"])
            || !empty($this->zipCode) || !empty($this->searchkeyword) || !empty($this->customAtrributesValues)){?>
	
    	<ul class="selected-criteria">
    		<?php if(!empty($this->selectedParams["type"]) && !empty($this->searchFilter["types"])) {?>
    			<li>
    				<a class="filter-type-elem" onclick="jbdListings.removeFilterRule('type', <?php echo $this->selectedParams["type"][0] ?>)"><?php echo $this->searchFilter["types"][$this->selectedParams["type"][0]]->typeName; ?><i class="la la-times"></i></a>
    			</li>
    		<?php $showClear++; } ?>
    
            <?php if(!empty($this->typeSearch) && empty($this->searchFilter["types"])) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('type')"><?php echo $this->typeSearchName; ?><i class="la la-times"></i></a>
                </li>
                <?php $showClear++;
            } ?>
    
            <?php if(!empty($this->selectedParams["membership"]) && !empty($this->searchFilter["memberships"])) {?>
                <?php 
                    $membershipSearch = explode(",",$this->selectedParams["membership"][0]);
                    $membershipSearch= array_unique($membershipSearch);
                    foreach($membershipSearch as $membershipId){
                ?>
                    <li>
                        <a class="filter-type-elem" onclick="jbdListings.removeFilterRule('membership', <?php echo $membershipId ?>)"><?php echo $this->searchFilter["memberships"][$membershipId]->membership_name; ?><i class="la la-times"></i></a>
                    </li>
                <?php $showClear++; } ?>
    		<?php  } ?>

            <?php if(!empty($this->selectedParams["package"]) && !empty($this->searchFilter["packages"])) {?>
                <?php 
                    $packageSearch = explode(",",$this->selectedParams["package"][0]);
                    $packageSearch= array_unique($packageSearch);
                    foreach($packageSearch as $packageId){
                ?>
                    <li>
                        <a class="filter-type-elem" onclick="jbdListings.removeFilterRule('package', <?php echo $packageId ?>)"><?php echo $this->searchFilter["packages"][$packageId]->package_name; ?><i class="la la-times"></i></a>
                    </li>
                <?php $showClear++; } ?>
    		<?php  } ?>
    
            <?php if(!empty($this->selectedParams["country"]) && !empty( $this->searchFilter["countries"])) {?>
    			<li>
    				<a  class="filter-type-elem" onclick="jbdListings.removeFilterRule('country', <?php echo $this->selectedParams["country"][0] ?>)"><?php echo $this->searchFilter["countries"][$this->selectedParams["country"][0]]->countryName; ?><i class="la la-times"></i></a>
    			</li>
    		<?php $showClear++; }?>
    
            <?php if(!empty($this->countrySearch) && empty($this->searchFilter["countries"])) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('country')"><?php echo $this->country->country_name; ?><i class="la la-times"></i></a>
                </li>
                <?php $showClear++;
            } ?>
    
            <?php if(!empty($this->selectedParams["province"]) && !empty($this->searchFilter["provinces"]) && isset( $this->searchFilter["provinces"][$this->selectedParams["province"][0]])) {?>
                <li>
                    <a class="filter-type-elem" onclick="jbdListings.removeFilterRule('province', <?php echo "&quot;".$this->selectedParams["province"][0]."&quot;" ?>)"><?php echo $this->searchFilter["provinces"][$this->selectedParams["province"][0]]->provinceName; ?><i class="la la-times"></i></a>
                </li>
            <?php $showClear++; } ?>
    
            <?php if(!empty($this->provinceSearch) && empty($this->searchFilter["provinces"])) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('province')"><?php echo $this->provinceSearch; ?><i class="la la-times"></i></a>
                </li>
                <?php $showClear++;
            } ?>

    		<?php if(!empty($this->selectedParams["region"]) && !empty( $this->searchFilter["regions"])) {?>
    			<li>
    				<a class="filter-type-elem" onclick="jbdListings.removeFilterRule('region', <?php echo "&quot;".$this->selectedParams["region"][0]."&quot;" ?>)"><?php echo $this->searchFilter["regions"][$this->selectedParams["region"][0]]->regionName; ?><i class="la la-times"></i></a>
    			</li>
    		<?php $showClear++; } ?>
    
            <?php if(!empty($this->regionSearch) && empty($this->searchFilter["regions"])) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('region')"><?php echo $this->region->regionName; ?><i class="la la-times"></i></a>
                </li>
                <?php $showClear++;
            } ?>
    
    		<?php if(!empty($this->selectedParams["city"]) && !empty($this->searchFilter["cities"]) && isset( $this->searchFilter["cities"][$this->selectedParams["city"][0]])) {?>
    			<li>
    				<a class="filter-type-elem" onclick="jbdListings.removeFilterRule('city', <?php echo "&quot;".$this->selectedParams["city"][0]."&quot;" ?>)"><?php echo $this->searchFilter["cities"][$this->selectedParams["city"][0]]->cityName; ?><i class="la la-times"></i></a>
    			</li>
    		<?php $showClear++; } ?>
    
            <?php if(!empty($this->citySearch) && empty($this->searchFilter["cities"])) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('city')"><?php echo $this->city->cityName; ?><i class="la la-times"></i></a>
                </li>
                <?php $showClear++;
            } ?>
    
    		<?php if(!empty($this->selectedParams["area"]) && !empty( $this->searchFilter["areas"])) {?>
    			<li>
    				<a class="filter-type-elem" class="remove" onclick="jbdListings.removeFilterRule('area', <?php echo "&quot;".$this->selectedParams["area"][0]."&quot;" ?>)"> <?php echo $this->searchFilter["areas"][$this->selectedParams["area"][0]]->areaName; ?><i class="la la-times"></i></a>
    			</li>
    		<?php $showClear++; } ?>
            <?php if(!empty($this->selectedParams["starRating"]) && !empty( $this->searchFilter["starRating"])) {?>
    			<li>
    				<a class="filter-type-elem" class="remove" onclick="jbdListings.removeFilterRule('starRating', <?php echo "&quot;".$this->selectedParams["starRating"][0]."&quot;" ?>)"> <?php echo $this->searchFilter["starRating"][$this->selectedParams["starRating"][0]]->reviewScore; ?><i class="la la-times"></i></a>
    			</li>
    		<?php $showClear++; } ?>
    
            <?php if(!empty($this->searchkeyword)) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('keyword')"><?php echo $this->searchkeyword; ?><i class="la la-times"></i></a>
                </li>
                <?php $showClear++;
            } ?>
    
            <?php if(!empty($this->customAtrributesValues)) {
                foreach ($this->customAtrributesValues as $attribute) { ?>
                    <li>
                        <a class="filter-type-elem"
                           onclick="jbdUtils.removeAttrCond(<?php echo $attribute->attribute_id ?>,'<?php echo $attribute->id ?>')"><?php echo JBusinessDirectoryTranslations::getTranslatedItemName($attribute->name); ?><i class="la la-times"></i> </a>
                    </li>
                    <?php
                    $showClear++;
                }
            } ?>

            <?php if(!empty($this->location) && !empty($this->geoLatitude) && empty($this->zipCode)) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('location')"><?php echo JText::_("LNG_GEO_LOCATION") ?><i class="la la-times"></i> </a>
                </li>
                <?php
                $showClear++;
            } ?>
    
            <?php if(!empty($this->zipCode)) { ?>
                <li>
                    <a class="filter-type-elem"
                       onclick="jbdUtils.removeSearchRule('zipcode')"><?php echo $this->zipCode; ?><i class="la la-times"></i> </a>
                </li>
                <?php
                $showClear++;
            } ?>
    		
    	</ul>
    <?php } ?>
    <?php if($showClear > 1) { ?>
        <span class="filter-type-elem reset"><a href="javascript:jbdUtils.resetFilters(true, true)" style="text-decoration: none;"><?php echo JText::_('LNG_CLEAR_ALL_FILTERS'); ?></a></span>
    <?php } ?>
	<div class="clear"></div>
</div>