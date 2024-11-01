<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
?>

<div id="videos-list-view" class="videos-list-view">
    <div class="row">
	    <?php foreach ($this->videos as $video) {?>
            <div class="col-md-4 mt-3">
                <a class="card-video" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=video&videoId='.$video->id) ?>">
                    <div class="card-video-wrap">
                        <video class="video" src="<?php echo $video->url ?>" type="video/mp4" poster="https://vitaliiradaiev.github.io/TopPropertyBootstrap/dist/img/common-blocks/carousel-video/01.jpg"></video>
                        <!--div class="play-pause">
                            <i class="la la-play icon-play3"></i>
                        </div-->
                    </div>
                    <div class="card-video-title">
                        <div class="video-btn video-category" style="color: <?php echo $video->cat_color?>; background:<?php echo JBusinessUtil::convertHexToRGB($video->cat_color,".2")?>">
                            <?php echo $video->mainSubcategoryName ?>
                        </div>
                        <div class="video-duration d-flex justify-content-around">
                            <i class="la la-clock-o mr-2"></i>
                            <div class="video-duration-time">0.04 min</div>
                        </div>
                    </div>

                    <div class="card-video-text">
                        <?php echo $video->description ?>
                    </div>
                </a>
            </div>
	    <?php } ?>
    </div>
</div>
