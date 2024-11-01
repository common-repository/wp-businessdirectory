<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');

$sliderId = rand(1000,10000);

$sliderParams = array();
$sliderParams['sliderId'] = $sliderId;
$sliderParams['autoplay'] = $params->get('autoplay') ? true : false;
$sliderParams['autoplaySpeed'] = $params->get('autoplaySpeed');
$sliderParams['nrVisibleItems'] = $params->get('nrVisibleItems', 5);
$sliderParams['nrItemsToScrool'] = $params->get('nrItemsToScrool', 2);
$sliderParams['rtl'] = $dir ? true : false;
$sliderParams['arrows'] = true;
$sliderParams['variableWidth'] = true;

?>

<div class="jbd-container ctg-slider-wrapper <?php echo $moduleclass_sfx; ?>" >
	<div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
    <div class="ctg-slider responsive slider" id="slider-<?php echo $sliderId ?>">
		<?php if(!empty($categories)) { ?>
			<?php foreach($categories as $category) {
				if(!is_array($category) || $category[0]->published==0){
					continue; 
				}
				$image = BD_PICTURES_PATH.'/no_image.jpg';
				if(!empty($category[0]->imageLocation)){
					$image = BD_PICTURES_PATH.$category[0]->imageLocation;
				}

			?>
				<a class="ctg-slider-item" href="<?php echo $category[0]->link  ?>">
					<figure>
						<div class="ctg-slider-img-wrap ibg"	
							style="background-image: url(<?php echo $image; ?>);">
						</div>
						<figcaption class="ctg-slider-item-text">
							<?php echo $category[0]->name; ?>
						</figcaption>
					</figure>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
</div>


<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_BROWSE_ALL")?></a>
    </div>
<?php }?>


<script>
	window.addEventListener('load', function(){
        jbdUtils.initSlider(<?php echo json_encode($sliderParams) ?>);
	});
</script>