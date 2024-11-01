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
<div class="jbd-container" >
	<div class="categories-style-5  jbd-grid-container">
        <div class="row" >
            <?php $k = 0; ?>
            <?php foreach ($categories as $category) {
            if (!is_array($category)) {
                $category = array($category);
                $category["subCategories"] = array();
            }
            if (isset($category[0]->name)) {
                $k++; ?>
                    <div class="<?php echo $span?>">
                        <div class="category-wraper">
                            <div class="category-header">
                                <div class="category-title">
                                    <a href="<?php echo $category[0]->link ?>"><?php echo $category[0]->name ?>
                                    </a>
                                </div>
                                <?php if ($appSettings->show_total_business_count) { ?>
                                    <div class="category-listings"> <?php echo $category[0]->nr_listings." ".JText::_("LNG_LISTINGS") ?></div>
                                <?php } ?>
                            </div>
                                
                            <?php if(!empty($category["subCategories"])){ ?>
                                <?php foreach ($category["subCategories"] as $cat) { ?>
                                    <a class="subcategory" title="<?php echo $cat[0]->name?>" alt="<?php echo $cat[0]->name?>"
                                        href="<?php echo $cat[0]->link ?>">
                                        <?php echo $cat[0]->name?>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php
            } ?>
            <?php
        } ?>
        </div>
    </div>
</div>