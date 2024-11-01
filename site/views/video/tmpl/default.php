<?php // no direct access
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');
?>

<div id="jbd-container" class="jbd-container">
	<div class="video-details">
		<div class="video-wrap ibg">
			<iframe src="<?php echo $this->video->url ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>

		<div class="video-content">
			<div class="video-title">
				<?php echo $this->video->name ?>
			</div>
			<div class="video-description">
				<p>
					<?php echo $this->video->description ?>
				</p> 
			</div>
		</div>
	</div>

	<?php if(!empty($this->relatedVideos)){ ?>
		<div class="related-videos videos-list-view">
			<div class="row">
				<div class="col-12">
					<h3><?php echo JText::_("LNG_RELATED_VIDEOS") ?></h3>
				</div>
			</div>
			<div class="row">
				<?php foreach ($this->relatedVideos as $video) { ?>
					<div class="col-md-4 mt-3">
						<a class="card-video" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=video&videoId='.$video->id) ?>">
							<div class="card-video-wrap">
								<iframe src="<?php echo $video->url ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</div>

							<div class="card-video-title">
								<div class="video-btn video-category">
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
	<?php } ?>
</div>

				
