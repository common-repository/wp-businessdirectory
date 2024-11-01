<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JBusinessUtil::loadJQueryUI();
JBusinessUtil::enqueueStyle('libraries/jquery/jquery-ui.css');
JBusinessUtil::enqueueScript('libraries/jquery/ui/jquery.ui.accordion.js');
?>

<style>
	.categories-header{
		margin-left: 15px;
	}
</style>

<!-- ACCORDION VIEW -->
<div id="categories-container" class="categories-style-1">
	<ul class="categories-accordion1">
	<?php foreach ($categories as $category) {
	if (!is_array($category)) {
		$category = array($category);
		$category["subCategories"] = array();
	}
	if (isset($category[0]->name)) {
		?>
		<li class="accordion-element">
			<div class="element-title">
				<a href="<?php echo $category[0]->link ?>">
				    <?php echo htmlspecialchars($category[0]->name, ENT_QUOTES) ?>
					<div class="la la-<?php echo $category[0]->icon ?>"></div>
				</a>
				<?php if (!empty($category["subCategories"])) { ?>
					<div class="element-categories"> <?php echo count($category["subCategories"])." ".JText::_("LNG_SUBCATEGORIES") ?></div>
				<?php } ?>
			</div>
			<div>
				<ul class="category-list">
					<?php foreach ($category["subCategories"] as $cat) { ?>
						<li>
							<a class="categoryLink" title="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES) ?>" alt="<?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES) ?>"
								href="<?php echo $cat[0]->link ?>"
							><?php echo htmlspecialchars($cat[0]->name, ENT_QUOTES)?>
							</a>
						</li> 
				<?php } ?>
				</ul>
			</div>
		</li>
	<?php
	}
}
	?>
	</ul>
</div>
<div class="clear"></div>
<?php if (!empty($params) && $params->get('showviewall')) {?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>

<script>
	window.addEventListener('load', function(){
		jQuery(".categories-accordion1" ).each( function () {
			jQuery(this).accordion({
				heightStyle: "content",
				active: "false",
				event: "click hoverintent"
			});
		});
	});

</script>