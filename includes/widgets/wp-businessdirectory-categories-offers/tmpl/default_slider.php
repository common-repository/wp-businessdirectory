<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$sliderId = rand(1000,10000);
$sliderParams = array();
$sliderParams['sliderId'] = $sliderId;
$sliderParams['autoplay'] = $params->get('autoplay') ? true : false;
$sliderParams['autoplaySpeed'] = $params->get('autoplaySpeed');
$sliderParams['nrVisibleItems'] = $params->get('nrVisibleItems');
$sliderParams['nrItemsToScrool'] = $params->get('nrItemsToScrool');

?>

<div class="jbd-container offer-categories-slider-wrapper<?php echo $moduleclass_sfx; ?>" >
    <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
    <div class="offer-categories-slider responsive slider" id="slider-<?php echo $sliderId ?>">
        <?php if(!empty($categories)) { ?>
            <?php foreach($categories as $category) {
                if(!is_array($category) || $category[0]->published==0)
                    continue; ?>
                <div class="categories-slider-item">
                    <a href="<?php echo JBusinessUtil::getOfferCategoryLink($category[0]->id, $category[0]->alias) ?>">
                        <?php
                        if(!empty($category[0]->imageLocation))
                            $image = BD_PICTURES_PATH.$category[0]->imageLocation;
                        else
                            $image = BD_PICTURES_PATH.'/no_image.jpg';
                        ?>
                        <div class="categories-slide-image"
                             style="background: url(<?php echo $image; ?>); background-repeat: no-repeat; background-position: center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;">
                        </div>
                        <p><?php echo $category[0]->name; ?></p>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>

<script>
    window.addEventListener('load', function(){
        jbdUtils.initSlider(<?php echo json_encode($sliderParams) ?>);
    });
</script>