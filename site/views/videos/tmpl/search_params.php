<div id="search-path">
	<?php if(isset($this->category)) { ?>
		<ul class="category-breadcrumbs">
			<li>
				<a class="search-filter-elem" href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=videos&resetSearch=1') ?>"><?php echo JText::_('LNG_ALL_CATEGORIES') ?></a>
			</li>
			<?php
			if(isset($this->searchFilter["path"])) {
				foreach($this->searchFilter["path"] as $path) {
					if($path[0]==1)
						continue;
					?>

					<li>
						<a class="search-filter-elem" href="<?php echo JBusinessUtil::getCategoryLink($path[0], $path[2]) ?>"><?php echo $path[1]?></a>
					</li>
				<?php } ?>
			<?php } ?>

			<li>
				<?php if(!empty($this->category)) echo $this->category->name ?>
			</li>
		</ul>
	<?php } ?>

	<ul class="selected-criteria">
		<?php if(!empty($this->searchkeyword)) { ?>
			<li>
				<a class="filter-type-elem"
				   onclick="jbdUtils.removeSearchRule('keyword')"><?php echo $this->searchkeyword; ?> x</a>
			</li>
			<?php $showClear++;
		} ?>

		<?php if($showClear > 1) { ?>
			<span class="filter-type-elem reset"><a href="javascript:jbdUtils.resetFilters(true)" style="text-decoration: none;"><?php echo JText::_('LNG_CLEAR_ALL'); ?></a></span>
		<?php } ?>
	</ul>
	<div class="clear"></div>
</div>