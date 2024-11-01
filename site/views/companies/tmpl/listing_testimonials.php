<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::enqueueStyle('libraries/owl/owl.carousel.min.css');
JBusinessUtil::enqueueStyle('libraries/owl/owl.theme.min.css');
JBusinessUtil::enqueueScript('libraries/owl/owl.carousel.min.js');

?>
<?php if(!empty($this->companyTestimonials)){?>
    <div id="testimonial-slider" class="owl-carousel owl-theme">
        <?php foreach ($this->companyTestimonials as $testimonial){?>
            <div class="item">
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p class="description">
                            <?php echo $testimonial->testimonial_description; ?>
                        </p>
                        <h3 class="testimonial-title"><?php echo $testimonial->testimonial_title; ?></h3>
                        <small class="post"><?php echo (!empty($testimonial->testimonial_title) && !empty($testimonial->testimonial_name))?" / ":"";
                            echo $testimonial->testimonial_name; ?></small>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <script>
        window.addEventListener('load', function(){
            jQuery(".owl-carousel").owlCarousel({
                items:1,
                itemsDesktop:[1000,2],
                itemsDesktopSmall:[980,1],
                itemsTablet:[767,1],
                pagination:false,
                navigation:true,
                navigationText:["",""],
                slideSpeed:1000,
                autoPlay:true
            });
        });
    </script>
<?php } ?>