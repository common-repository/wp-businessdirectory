<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

if (empty($span)) {
	$span = "col-xl-3 col-lg-4 col-sm-6 col-12";
}
?>

<div class="categories-container jbd-container categories-style-6">
	<div class="row">
    	<?php $index = 1; ?>
    	<?php foreach ($categories as $category) {
			if (!is_array($category)) {
				$category = array($category);
				$category["subCategories"] = array();
			}
			if (isset($category[0]->name)) {  ?>
                <div class="<?php echo $span?>">
					<div class="category-item">
						<div class="cat-item-image">
							<a href="<?php echo $category[0]->link ?>">
								<?php if (!empty($category[0]->imageLocation)) { ?>
									<img title="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										alt="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										src="<?php echo BD_PICTURES_PATH . $category[0]->imageLocation ?>">
								<?php } else { ?>
									<img title="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										alt="<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>"
										src="<?php echo BD_PICTURES_PATH . '/no_image.jpg' ?>">
								<?php } ?>
							</a>
						</div>
						<div class="cat-item-title">
							<a class="d-flex align-items-start justify-content-between" href="<?php echo htmlspecialchars($category[0]->link, ENT_QUOTES) ?>"><?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>
								<?php if ($appSettings->show_total_business_count && empty($showRelated)) { ?>
									<span class="numberCircle"> <?php echo $category[0]->nr_listings ?></span>
								<?php } ?>
							</a>
						</div>					
					</div>
                </div>
            <?php } ?>
         	<?php $index++; ?>
        <?php } ?>
	</div>
</div>
<?php if (!empty($params) && $params->get('showviewall')) {?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>