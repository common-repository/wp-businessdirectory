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

<div class='video-container row videos-list-view'>
	<?php 
	if(!empty($this->videos)){
		foreach( $this->videos as $video ){
			if(!empty($video->url))	{
			    ?>
    			<div class="col-md-4">
    				<a class="card-video" href="<?php echo $this->escape($video->url) ?>">
						<div class="card-video-wrap">
							<img src="<?php echo !empty($video->videoThumbnail) ? $video->videoThumbnail  : BD_PICTURES_PATH.'/video_default.jpg' ?>" />
							<div class="play-pause">
								<i class="la la-play"></i>
							</div>
						</div>
						<div class="card-video-title">
							<?php echo $video->title ?>
                        </div>
    				</a>
    			</div>
			<?php }
		}
	} else {
		echo JText::_("LNG_NO_COMPANY_VIDEO");
	} ?>
	<div class="clear"></div>
</div>

<script type="text/javascript">
	window.addEventListener('load', function() {
		jQuery('.card-video').magnificPopup({
			disableOn: 200,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false,
            // iframe: {
            //    markup:
			//    '<div class="mfp-content" style="width:' + jQuery('.card-video').data('width') + 'px; height:' + jQuery('.card-video').data('height') + 'px;">'+
			// 	'<div class="mfp-iframe-scaler" >'+
			// 			'<div class="mfp-close">xxxxxx</div>'+
			// 	'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
			// 	'</div>'
			// 			},
			mainClass: 'mfp-fade'
		});
	});
</script>