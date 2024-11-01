<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="company-projects-container" class="projects-container">
	<div class="row">
		<?php foreach($this->companyProjects as $index=>$project){ ?>
			<div class="col-lg-4 col-sm-6 col-12">
				<div class="card jitem-card project-card">
					<div class="jitem-img-wrap small">
						<a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);"></a>
						<?php if(!empty($project->picture_path)){?>
							<img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.$project->picture_path ?>">
						<?php }else{ ?>
							<img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>">
						<?php } ?>
					</div>
					<div class="jitem-body">
						<div class="jitem-body-content">
							<div class="jitem-title">
								<span><?php echo $project->name?></span>
							</div>
							<div class="jitem-desc text-small">
								<div class="jitem-desc-content">
									<p><?php echo $project->nrPhotos . " ". JText::_("LNG_PHOTOS");?></p>
									<!--p> <?php echo JBusinessUtil::truncate( strip_tags($project->description), 140); ?></p-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<div class="projects-container" id="project-details" style="display: none">
	<div id="search-path">
		<ul class="category-breadcrumbs">
			<li>
				<a href="javascript:jbdListings.returnToProjects();"><?php echo JText::_("LNG_PROJECTS"); ?></a>
			</li>
			<li>
				<span id="project-name-link"></span>
			</li>
		</ul>
		<div class="clear"></div>
	</div>

	<div class="row project-content">
		<div class="col-md-4">
			<h4 id="project-name"></h4>
			<div id="project-description"></div>
		</div>

		<div class="col-md-8" id="project-image-container">
			<div class="">
				<div class="row">
					<div class="col-md-12">
						<div class='picture-container' id="project-gallery">
							<div style="clear:both;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>