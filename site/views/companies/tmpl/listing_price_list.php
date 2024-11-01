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

<?php if ($this->appSettings->price_list_view == 1) { ?>
    <?php if (!empty($this->services_list)) { ?>
        <div class="service-list">
            <div class="service-section">
                <div class="service-section-name">
                    <?php echo $this->services_list[0]->service_section ?>
                </div>
           		<?php $header = $this->services_list[0]->service_section; ?>
                <div class="service-list-container">
                <ul>
                    <?php foreach ($this->services_list as $key => $service) { ?>
                        <?php if ($header != $service->service_section) { ?>
                          	</ul>
                            </div>
                            </div>
                            <div class="service-section">
                                <div class="service-section-name">
                                    <?php echo $service->service_section ?>
                                </div>
                                <?php $header = $service->service_section; ?>
                           
                                <div class="service-list-container">
                                <ul>
                       <?php } ?>
                        <li class="service-item">
                            <div class="row">
                                <div class="col-md-2">
                                    <img alt="<?php echo $service->service_name ?>" class=""
                                         src="<?php echo !empty($service->service_image) ? BD_PICTURES_PATH . $service->service_image : BD_PICTURES_PATH . '/no_image.jpg' ?>"
                                         style="">
                                </div>
                                <div class="col-md-10">
                                    <div class="service-price">
                                   		<?php echo JBusinessUtil::getPriceFormat($service->service_price, $this->appSettings->currency_id); ?>
                                    </div>
                                    <div class="service-name">
                                    	<?php echo $service->service_name ?>
                                    </div>
                                    <p><?php echo $service->service_description ?></p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
              </div>
             </div>
		</div>
	<?php } ?>
<?php }else { ?>
    <div class="service-list">
        <div class="row">
		 <?php
       	 	if(isset($this->services_list) && !empty($this->services_list)){
                $index = 0;
            	foreach($this->services_list as $index=>$service){
                    $index++;
           ?>
           <div class="col-lg-4 col-sm-6 col-12">
        		<div class="card jitem-card">
        			<div class="jitem-img-wrap min-height-sm">
        				<?php if(!empty($service->service_image)){?>
                         	<img title="<?php echo $service->service_name?>" alt="<?php echo $service->service_name?>" src="<?php echo BD_PICTURES_PATH.$service->service_image ?>" >
                         <?php }else{ ?>
                             <img title="<?php echo $service->service_name?>" alt="<?php echo $service->service_name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" >
                         <?php } ?>
        			</div>
        			<div class="jitem-body">
        				<div class="jitem-body-content">
            				<div class="jitem-title">
            					<?php echo $service->service_name?>
            				</div>
            				<div class="jitem-desc">
            					<p><?php echo JBusinessUtil::truncate($service->service_description,100)?></p>
            					<div class="price"><?php echo JBusinessUtil::getPriceFormat($service->service_price, $this->appSettings->currency_id); ?></div>
            				</div>
            			</div>
        			</div>
        		</div>
        	</div>
            <?php } ?>
		<?php }?>
   	 	</div>
	</div>
<?php } ?>


<script>
    window.addEventListener('load', function() {
        //Expand/Collapse Individual Boxes
        jQuery(".expand_heading").toggle(function(){
            jQuery(this).addClass("active");
        }, function () {
            jQuery(this).removeClass("active");
        });
        jQuery(".expand_heading").click(function(){
            jQuery(this).nextAll(".toggle_container:first").slideToggle("slow");
        });

        //Show hide 'expand all' and 'collapse all' text
        jQuery(".expand_all").toggle(function(){
            jQuery(this).addClass("expanded");
        }, function () {
            jQuery(this).removeClass("expanded");
        });

        jQuery(".expand_all").click(function () {
            if (jQuery(this).hasClass("expanded")) {
                jQuery(".toggle_container").slideDown("slow");
            }
            else {
                jQuery(".toggle_container").slideUp("slow");
            }
        });
    });
</script>
