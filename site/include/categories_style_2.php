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
	$span = "col-lg-4 col-sm-6 col-12";
}
?>

<!-- BOXES VIEW -->
<div class="categories-container">
	<div class="jbd-container categories-style-2 jbd-grid-container">
		<div class="row">
		<?php $k = 0;?>
		<?php foreach ($categories as $category) {
		if (!is_array($category)) {
			$category = array($category);
			$category["subCategories"] = array();
		}
		if (isset($category[0]->name)) {
			$k= $k+1; ?>
			<div class="<?php echo $span?>">
				<div class="category-content h-100">
					<div class="row">
						<div class="col-md-3 pr-0">
							<?php if (!empty($category[0]->imageLocation)) { ?>
								<div class="category-img-container">
									<a href="<?php echo $category[0]->link ?>">
										<img alt="" src="<?php echo BD_PICTURES_PATH.$category[0]->imageLocation ?>">
									</a>
								</div>
							<?php } ?>
						</div>
						<div class="col-md-9">
							<div class="category-name">
								<a href="<?php echo $category[0]->link ?>"> <?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>
									
								</a>
							</div>
							<?php if(!empty($category["subCategories"])){ ?>
								<div><?php echo count($category["subCategories"])." ".JText::_("LNG_SUBCATEGORIES") ?></div>
							<?php } ?>
							<?php if ($appSettings->show_total_business_count) { ?>
								<div class="category-listings"> <?php echo $category[0]->nr_listings." ".JText::_("LNG_LISTINGS") ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="subcategories-container">
						<?php if(!empty($category["subCategories"])){ ?>
							<?php foreach ($category["subCategories"] as $cat) { ?>
								<a class="subcategory" title="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>" alt="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>"
									href="<?php echo $cat[0]->link ?>"
								>
									<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>
								</a>
							<?php } ?>
						<?php } ?>	
					</div>			
				</div>
			</div>
		<?php
		}
	}
		?>
		</div>
	</div>
</div>

<?php if (!empty($params) && $params->get('showviewall')) {?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>