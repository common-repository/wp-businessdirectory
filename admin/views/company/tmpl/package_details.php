<?php $package= $this->package;?>
<div class="package-details-container">
    <div class="row">
        <div class="col-12">
            <div class="head-text" >
                <strong class="price" >
                    <div class="item1">
                        <span class="price-item"><?php echo $package->price == 0 ? JText::_("LNG_FREE"):JBusinessUtil::getPriceFormat($package->price + (float)$this->appSettings->vat*$package->price/100)?></span>
                        <?php echo $package->days > 0 ? " / ":"" ?>
                        <?php if($package->days > 0 ) {?>
                            <span>
                                <?php
                                   echo JBusinessUtil::getPackageDuration($package, true);
                                ?>
                            </span>
                         <?php }?>
                        </div>
                    <span class="item2">
                        <?php echo $package->description ?>
                    </span>
                </strong>
            </div>
        </div>

        <?php foreach($this->packageFeatures as $key=>$featureName){?>
            <div class="featured-product-cell col-md-4" >
                <?php
                    $class="la la-minus-square not-contained-feature";
                    if(isset($package->features) && in_array($key, $package->features)){
                        $class="la la-check-square contained-feature";
                    } 
                 ?>
                <div>
                    <?php
                        $featureSpec = '';
                        $max = "";
                        if($key == 'image_upload') {
                            if(!empty($package->max_pictures)){
                                $max = $package->max_pictures;
                            }
                            $class = !empty($package->max_pictures)?$class:"la la-minus-square not-contained-feature";

                        }
                        
                        if($key == 'videos') {
                            if(!empty($package->max_videos)){
                                $max = $package->max_videos;
                            }
                            $class = !empty($package->max_videos)?$class:"la la-minus-square not-contained-feature";

                        }
                        if($key == 'company_sounds') {
                            if(!empty($package->max_sounds)){
                                $max = $package->max_sounds;
                            }
                            $class = !empty($package->max_sounds)?$class:"la la-minus-square not-contained-feature";

                        }
                        
                        if($key == 'company_offers') {
                            if(!empty($package->max_offers)){
                                $max = $package->max_offers;
                            }
                        }

                        if($key == 'company_events') {
                            if(!empty($package->max_events)){
                                $max = $package->max_events;
                            }
                        }

                        if($key == 'secondary_locations') {
							if(!empty($package->max_locations)){
								$max = $package->max_locations;
							}

                            $class = !empty($package->max_locations)?$class:"la la-minus-square not-contained-feature";
						}

                        if($key == 'attachments') {
                            if(!empty($package->max_attachments)){
                                $max = $package->max_attachments;
                            }
                            $class = !empty($package->max_attachments)?$class:"la la-minus-square not-contained-feature";
                        }

                        if($key == 'multiple_categories') {
                            if(!empty($package->max_categories)){
                                $max = $package->max_categories;
                            }
                            $class = !empty($package->max_categories)?$class:"la la-minus-square not-contained-feature";
                        }

                        if($class=="not-contained-feature"){
                            $max="";
                        }
                    ?>
                    <i class="<?php echo $class?>"></i> <span class="max-items"><?php echo $max ?></span> <?php echo $featureName.$featureSpec; ?>
                </div>
            </div>
        <?php } ?>

        <?php
            foreach($this->customAttributes as $customAttribute){
                if($customAttribute->show_in_front==0){
                    continue;
                }

                $class="la la-minus-square not-contained-feature";
                if(isset($package->features) && in_array($customAttribute->code,$package->features)){
                    $class="la la-check-square contained-feature";
                } ?>

                <div class="featured-product-cell col-md-4" >
                    <div><i class="<?php echo $class?>"></i><?php echo $customAttribute->name?></div>
                </div>
        <?php } ?>

    </div>
</div>