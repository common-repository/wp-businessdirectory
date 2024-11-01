<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::enqueueStyle('libraries/animate/ultimate.min.css');

if (empty($span)) {
	$span = "col-xl-3 col-lg-4 col-sm-6 col-12";
}
?>

<!-- ICONS VIEW -->
<div class="jbd-container" >
	<div class="categories-style-4  jbd-grid-container">
		<div class="row">
			<?php if(!empty($categories)){ ?>
				<?php foreach ($categories as $category) { ?>
					<?php if (isset($category[0]->name)) { ?>
						<div class="<?php echo $span?>">
							<a href="<?php echo htmlspecialchars($category[0]->link, ENT_QUOTES) ?>">
								<div class="category-wraper">
									<div class="category-icon">
										<div class="la la-<?php echo $category[0]->icon ?>"></div>
									</div>
									<div class="category-header">
										<div class="category-title">
											<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES); ?>
										</div>
										<?php if ($appSettings->show_total_business_count) { ?>
											<div class="category-listings"> <?php echo $category[0]->nr_listings." ".JText::_("LNG_LISTINGS") ?></div>
										<?php } ?>
									</div>
									<div class="category-description">
										<?php echo JBusinessUtil::truncate($category[0]->description, 100); ?>
									</div>
								</div>
							</a>
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
<?php if (!empty($params) && $params->get('showviewall')) {?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>