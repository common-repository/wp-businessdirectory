<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="main-categories-simple" id="main-categories-simple">
	<?php if(!empty($categories)) { ?>
		<?php foreach($categories as $category) {
			if(!is_array($category) || $category[0]->published==0)
				continue; 
		?>
		<a href="<?php echo $category[0]->link  ?>">
			<span class="category-icon">
				<?php if(!empty($category[0]->icon)){ ?>
						<i class="la la-custom la la-<?php echo $category[0]->icon ?>"></i>
				<?php } ?>
			</span> 
			<span class="category-name"><?php echo $category[0]->name; ?></span>
		</a>
		<?php } ?> 
	<?php } ?> 
	<div style="position: relative;">
		<span class="cta-text"><?php echo JText::_("LNG_BROWSE_HIGHLIGHTS") ?></span>
	</div>
</div>
<?php if(!empty($params) && $params->get('showviewall')){?>
    <div class="view-all-items">
        <a href="<?php echo $viewAllLink; ?>"><?php echo JText::_("LNG_VIEW_ALL")?></a>
    </div>
<?php }?>