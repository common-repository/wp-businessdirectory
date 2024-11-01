<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */ 
defined('_JEXEC') or die('Restricted access');
$session = JFactory::getSession();
$searchPerformed = $session->get("listing-search");
$searchType = $session->get("lSearchType");
$menuId = $session->get("menuItemId");
?>

<div id="search-path">
	<ul class="category-breadcrumbs">
	<?php if(!isset($searchPerformed) || !isset($searchType)){?>
			<?php if(isset($this->category)){ ?>
			<li>
				<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&controller=search&view=search') ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
			</li>
			<?php } ?>
			<?php 
				if(!empty($this->company->path)){
				foreach($this->company->path as $path) {
					if(empty($path))
						continue;
				?>
				<li>
					<a  class="search-filter-elem" href="<?php echo JBusinessUtil::getCategoryLink($path->id, $path->alias) ?>"><?php echo $path->name?></a>
				</li>
			<?php }
				} 
			?>
			<li>
				<?php echo $this->company->name ?>
			</li>
		<?php }else{ ?>
			<li>
				<?php if($searchType == 2){?>
					<?php if(!empty($menuId)){ ?>
						<a href="<?php echo JRoute::_("index.php?reload=1&Itemid=".$menuId, false)?>"><?php echo JText::_("LNG_BACK_TO_SEARCH_RESULTS")?></a>
					<?php }else{ ?>
						<a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=catalog&reload=1&Itemid=".$menuId)?>"><?php echo JText::_("LNG_BACK_TO_SEARCH_RESULTS")?></a>
					<?php } ?>
				<?php }else{?>
					<?php if(!empty($menuId)){ ?>
						<a href="<?php echo JRoute::_("index.php?reload=1&Itemid=".$menuId, false)?>"><?php echo JText::_("LNG_BACK_TO_SEARCH_RESULTS")?></a>
					<?php }else{ ?>
						<a href="<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=search&reload=1&Itemid=".$menuId)?>"><?php echo JText::_("LNG_BACK_TO_SEARCH_RESULTS")?></a>
					<?php } ?>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
</div>