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

<?php if(!empty($this->pictures)){?>	
<div class="slidergallery" id="slidergallery" style="width:auto">
	<div id="pageContainer">
		<div id="slideshow">
	   		<div id="slidesContainer">
	      		<div class="slide-dir">
	      			<ul class="gallery gallery-listing">
						<?php 
							$index = 1;
							$totalItems = count($this->pictures); 
						?>
						<?php foreach( $this->pictures as $picture ){ ?>
							<li>
								<a href="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>" title="<?php echo $this->escape($picture->picture_info) ?>">
									<img itemprop="image" src="<?php echo BD_PICTURES_PATH.$picture->picture_path ?>" alt="<?php echo $this->escape($picture->picture_info) ?>" />
								</a>
							</li>
							
						<?php } ?>
					</ul>
			
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<script>
    window.addEventListener('load', function() {
        jbdListings.magnifyImages('gallery-listing');
    });
</script>