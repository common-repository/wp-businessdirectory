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
?>

<div class="gallery-slider responsive slider" id="slider-<?php echo $sliderId ?>">
    <?php if (!empty($this->pictures)) {?>
        <div class="slider-loader" id="slider-<?php echo $sliderId ?>-loader">
            <div class="loader"></div>
        </div>
        <div class="gallery-main-slider">
            <?php foreach ($this->pictures as $picture) { ?>
                <div class="gallery-slider-item abs-img">
                    <a data-fancybox="group" href="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>" title="<?php echo $this->escape($picture->picture_info) ?>">
                        <img src="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>" alt="<?php echo $picture->picture_info ?>"
                            data-image="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>"
                            data-description="<?php echo $picture->picture_info ?>">
                    </a>
                </div>
            <?php }?>
		</div>
        
     
	<?php } ?>
</div>
<script type="text/javascript">
    window.addEventListener('load', function() {
        
        jQuery(".gallery-main-slider").slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<div class="slick-arrow slick-prev"><span class=""><svg  viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5 19.5L11.55 18.45L5.925 12.75L21.75 12.75L21.75 11.25L5.925 11.25L11.55 5.55L10.5 4.5L3 12L10.5 19.5Z" fill="#344F6E"/></svg></span></div>',
            nextArrow: '<div class="slick-arrow slick-next"><span class=""><svg  viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.5 4.5L12.45 5.55L18.075 11.25H2.25V12.75H18.075L12.45 18.45L13.5 19.5L21 12L13.5 4.5Z" fill="#344F6E"/></svg></span></div>',
            fade: !0,
            infinite: !0,
            responsive: [{
                breakpoint: 768,
                settings: {
                    fade: !1
                }
            }]
        });


        jQuery(".gallery-main-slider").magnificPopup({
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
            
            if(jQuery(".slick-initialized").length) {
                jQuery(".slider-loader").hide()
            }
    });
</script>
	
	
