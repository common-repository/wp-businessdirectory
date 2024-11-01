<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

if (empty($span)) {
	$span = "col-xl-3 col-lg-4 col-sm-6 col-12";
}
?>

<div class="jbd-container">
	<div class="categories-container  jbd-grid-container categories-style-3">
		<div class="row">
			<?php $index = 1; ?>
			<?php foreach ($categories as $category) {
		if (!is_array($category)) {
			$category = array($category);
			$category["subCategories"] = array();
		}
		if (isset($category[0]->name)) {  ?>
					<div class="<?php echo $span?>">
						<div class="card jitem-card h-100">
							<div class="jitem-img-wrap">
								<a href="<?php echo $category[0]->link ?>"></a>
								<?php if (!empty($category[0]->imageLocation)) { ?>
									<img title="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										alt="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										src="<?php echo BD_PICTURES_PATH . $category[0]->imageLocation ?>">
								<?php } else { ?>
									<img title="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										alt="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>">
								<?php } ?>
								<div class="card-hoverable">
									<a href="<?php echo htmlspecialchars($category[0]->link, ENT_QUOTES) ?>" class="btn btn-outline-success btn-sm w-auto"><?php echo JText::_("LNG_VIEW")?></a>
								</div>
							</div>
							<div class="jitem-body">
								<div class="jitem-body-content">
									<div class="jitem-title">
										<a class="d-flex align-items-start justify-content-between" href="<?php echo htmlspecialchars($category[0]->link, ENT_QUOTES) ?>"><?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?></a>
									</div>
									<?php if ($appSettings->show_total_business_count) { ?>
										<div class="category-listings"> <?php echo $category[0]->nr_listings." ".JText::_("LNG_LISTINGS") ?></div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php $index++; ?>
			<?php
	}?>
		</div>
	</div>
</div>

<?php if (!empty($params) && $params->get('showviewall')) {?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>