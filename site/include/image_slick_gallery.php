<?php
/**
 * @package    J-BusinessDirectory
 *
 * Image slider with gallery component
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::enqueueStyle('libraries/slick/slick.css');
JBusinessUtil::enqueueScript('libraries/slick/slick.js');

$lang = JFactory::getLanguage();
$dir = $lang->get('rtl');
$sliderId = rand(1000, 10000);

$sliderParams = array();
$sliderParams['sliderId'] = $sliderId;
$sliderParams['autoplay'] =  false;
$sliderParams['autoplaySpeed'] = 7000;
$sliderParams['nrVisibleItems'] = 2;
$sliderParams['nrItemsToScrool'] = 1;
$sliderParams['rtl'] = $dir ? true : false;

$sliderParams['centerMode'] = false;
$sliderParams['variableWidth'] = true;
$sliderParams['slidesToShow'] = 1;

$sliderParams['infinite'] = false;
?>
<style>
    .gallery-slider .slick-slide img{
        width: auto;
        height: 100%;
        max-height: 350px;
        max-width: none;
    }

    .gallery-slider .slider-item .slider-content{
    	overflow: visible;
    }

    .gallery-slider {
        opacity: 0;
        visibility: hidden;
        transition: opacity 1s ease;
        -webkit-transition: opacity 1s ease;
    }
    
    .gallery-slider.slick-initialized {
        visibility: visible;
        opacity: 1;    
    }

</style>
<?php if (!empty($this->pictures) && count($this->pictures) > 2) {?>
    <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
        <div class="loader"></div>
    </div>
<?php } ?>
<div class="gallery-slider slick-gallery-slider responsive slider" id="slider-<?php echo $sliderId ?>">
	<?php if (!empty($this->pictures) && count($this->pictures) > 2) {?>
        <?php foreach ($this->pictures as $picture) { ?>
                <div>
                 	<a href="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>"  title="<?php echo $this->escape($picture->picture_info) ?>">
                		<img title="<?php echo $picture->picture_title ?>" alt="<?php echo $picture->picture_info ?>" src="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>"
                         data-image="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>"
                         data-description="<?php echo $picture->picture_info ?>">
                    </a>
                </div>
		<?php }?>
	<?php } ?>
</div>
<?php if (!empty($this->pictures)) {?>	
	<script type="text/javascript">
    	window.addEventListener('load', function() {
            jbdUtils.initSlider(<?php echo json_encode($sliderParams) ?>);
            jQuery(".slick-gallery-slider .slick-track").magnificPopup({
                delegate: 'a',
                type: 'image',
                tLoading: 'Loading image #%curr%...',
                mainClass: 'mfp-img-mobile',
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    preload: [0, 2] // Will preload 0 - before current, and 1 after the current image
                },
                image: {
                    tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
                    titleSrc: function (item) {
                        return item.el.attr('title');
                    }
                }
            });

            jQuery(".controller-prev").click(function(event){
            	  event.stopPropagation();
            });
            jQuery(".controller-next").click(function(event){
          	  event.stopPropagation();
          });
          
    	});
	</script>
<?php } ?>
	
	
