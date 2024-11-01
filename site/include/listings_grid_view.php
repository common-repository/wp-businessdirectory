<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$appSettings = JBusinessUtil::getApplicationSettings();
$enableNumbering = $appSettings->enable_numbering;
$user = JBusinessUtil::getUser();

$showData = !($user->ID==0 && $appSettings->show_details_user == 1);
$showOnlyItems = false;

$grid_layout = JFactory::getApplication()->input->get('grid_layout');
if (!empty($grid_layout)) {
	$appSettings->search_result_grid_view = $grid_layout;
} 
?>

<div id="jbd-grid-view" <?php echo !$appSettings->search_view_mode?'style="display: none"':'' ?> class="loading">
	<?php
		if ($appSettings->search_result_grid_view == 3) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_grid_style_3.php';
		}else if ($appSettings->search_result_grid_view == 2) {
			require_once JPATH_COMPONENT_SITE.'/include/listings_grid_style_2.php';
		} else {
			require_once JPATH_COMPONENT_SITE.'/include/listings_grid_style_1.php';
		} 
	?>

	<div class="shimmer-container shimmer-grid" <?php echo $appSettings->search_results_loading == 0 || $appSettings->search_view_mode == 0 ?'style="display: none"':'' ?>>
		<div class="row">
			<div class="col-lg-4 col-sm-6 col-12">
				<div class="jitem-card">
					<div class="jitem-img-wrap shimmer-bg">
					</div>
					<div class="jitem-body">
						<div class="jitem-body-content">
							<div class="title shimmer-bg">
							</div>
							<div class="description">
								<div class="shimmer-bg content-line"></div>
								<div class="shimmer-bg content-line"></div>
								<div class="shimmer-bg content-line"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-sm-6 col-12">
				<div class="jitem-card">
					<div class="jitem-img-wrap shimmer-bg">
					</div>
					<div class="jitem-body">
						<div class="jitem-body-content">
							<div class="title shimmer-bg">
							</div>
							<div class="description">
								<div class="shimmer-bg content-line"></div>
								<div class="shimmer-bg content-line"></div>
								<div class="shimmer-bg content-line"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-sm-6 col-12">
				<div class="jitem-card">
					<div class="jitem-img-wrap shimmer-bg">
					</div>
					<div class="jitem-body">
						<div class="jitem-body-content">
							<div class="title shimmer-bg">
							</div>
							<div class="description">
								<div class="shimmer-bg content-line"></div>
								<div class="shimmer-bg content-line"></div>
								<div class="shimmer-bg content-line"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
