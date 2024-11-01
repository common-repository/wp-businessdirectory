<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::enqueueStyle('libraries/slick/slick.css');
JBusinessUtil::enqueueScript('libraries/slick/slick.js');
?>

<div id="company-projects-container" class="projects-container project-style-2">
    <div class="row">
		<?php
        foreach($this->companyProjects as $index=>$project){ ?>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card jitem-card project-card">
                    <div class="jitem-img-wrap small">
                        <a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);" href="javascript:void(0)"></a>
						<?php if(!empty($project->picture_path)){?>
                            <img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.$project->picture_path ?>">
						<?php }else{ ?>
                            <img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>">
						<?php } ?>
                    </div>

	                <?php if (!empty($project->pictures) && $appSettings->projects_show_images == 1) { ?>
                        <div class="d-flex project-thumbs">
			                <?php
                                $picturesCount = count($project->pictures);
                                $displayPicturesCount = $picturesCount > 3 ? 3 : $picturesCount;
                                $lastPic = false;
                                for ($i = 0; $i < $displayPicturesCount; $i++) {
                                    $picture = $project->pictures[$i];
                                    if ($i == $displayPicturesCount) {
                                        $lastPic = true;
                                    }
			                ?>
                                <div class="project-img">
                                    <a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);" href="javascript:void(0)">
                                        <img title="<?php echo $project->name?>" alt="<?php echo $project->name?>" src="<?php echo BD_PICTURES_PATH.$picture[3] ?>">
                                    </a>
                                </div>
			                <?php } ?>
                        </div>
	                <?php } ?>

                    <div class="jitem-body">
                        <div class="jitem-body-content">
                            <div class="jitem-title">
                                <a onclick="jbdListings.showProjectDetail(<?php echo $project->id?>);" href="javascript:void(0)">
                                    <?php echo $project->name?>
                                </a>
                            </div>
                            <div class="jitem-desc text-small">
                                <div class="jitem-desc-content">
                                    <p><?php echo $project->nrPhotos . " ". JText::_("LNG_PHOTOS");?></p>
                                    <!-- p> <?php echo JBusinessUtil::truncate( strip_tags($project->description), 140); ?></p-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		<?php } ?>
    </div>
</div>

<div class="projects-container project-style-2" id="project-details" style="display: none">
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

    <div class="project-content">
        <div class="row ">
            <div class="col-md-12">
                <h4 id="project-name"></h4>
                <div id="project-description"></div>
            </div>
        </div>

        <div id="company-projects-container" class="projects-container mt-2">
            
            <div class="slider-loader" id="project-gallery-loader">
                <div class="loader"></div>
            </div>

            <div id="project-gallery" class="project-gallery row-rem">

            </div>
        </div>
    </div>
</div>

<script>
    var unitegalleryprojects = null;
</script>