<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
?>
<script>
	window.addEventListener('load', function(){
		jQuery("li.main-cat").mouseover(function() {
			jQuery(this).addClass('over');
		}).mouseout(function() {
			jQuery(this).removeClass('over');
		});

		jQuery("ul.subcategories").mouseover(function() {
			jQuery(this).parent().addClass('over');
		}).mouseout(function() {
			jQuery(this).parent().removeClass('over');
		});

		jQuery('.metismenu').metisMenu();
	});
</script>
<div class="jbd-container">
	<div class="categories-menu<?php echo $moduleclass_sfx ?>" id="category-holder">
		<ul id="categories-menu-container" class="metismenu main-categories">
			<?php 
			foreach($categories as $category) {
				if(!is_array($category) || $category[0]->published==0)
					continue; ?>
				<li>
					<?php if(isset($category["subCategories"]) && count($category["subCategories"]) > 0) { 
						$nrCategories = count($category["subCategories"]); ?>
						<a aria-expanded="true" href="#">
							<?php if(!empty($category[0]->icon)) { ?>
								<span class="la la-<?php echo $category[0]->icon ?>"></span> 
							<?php } ?>
							<span onclick="goToLink('<?php echo JBusinessUtil::getCategoryLink($category[0]->id, $category[0]->alias) ?>')"> <?php echo $category[0]->name; ?></span> 
							<span class="la la-menu-arrow"></span>
						</a>
						<ul aria-expanded="false" class="collapse">
							<?php 
							$index = 0;
							$rowIndex = 0;
							if($nrCategories>0) {
								foreach($category["subCategories"] as $subcategory) {
									$index++;
									?>
									<li>
										<a href="<?php echo JBusinessUtil::getCategoryLink($subcategory[0]->id, $subcategory[0]->alias) ?>">
											<?php echo htmlspecialchars($subcategory[0]->name, ENT_QUOTES) ?>
										</a>
									</li>
								<?php } ?>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<a href="<?php echo JBusinessUtil::getCategoryLink($category[0]->id, $category[0]->alias) ?>">
						<?php if(!empty($category[0]->icon)) { ?>
							<span class="la la-<?php echo $category[0]->icon ?>"></span> 
						<?php } ?>
						<?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?></a>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>
