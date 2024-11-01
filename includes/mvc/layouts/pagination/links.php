<?php
/**
 * @package     JBD.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use MVC\Registry\Registry;

$list = $displayData['list'];
$pages = $list['pages'];

$options = new Registry($displayData['options']);

$showLimitBox   = $options->get('showLimitBox', false);
$showPagesLinks = $options->get('showPagesLinks', true);
$showLimitStart = $options->get('showLimitStart', true);

// Calculate to display range of pages
$currentPage = 1;
$range = 1;
$step = 5;

if (!empty($pages['pages']))
{
	foreach ($pages['pages'] as $k => $page)
	{
		if (!$page['active'])
		{
			$currentPage = $k;
		}
	}
}

if ($currentPage >= $step)
{
	if ($currentPage % $step === 0)
	{
		$range = ceil($currentPage / $step) + 1;
	}
	else
	{
		$range = ceil($currentPage / $step);
	}
}
?>
<div class="tablenav-pages">
	<span class="displaying-num"><?php echo $list['total'] ?> items</span>
	<span class="pagination-links">
		<?php if ($showPagesLinks && (!empty($pages))){?>
			<?php
				echo JLayoutHelper::render('pagination.link', $pages['start']);
				echo JLayoutHelper::render('pagination.link', $pages['previous']); ?>
				<span class="paging-input">
					<label for="current-page-selector" class="screen-reader-text">Current Page</label>
					<input class="current-page" id="current-page-selector" name="paged" value="<?php echo $currentPage ?>" size="1" aria-describedby="table-paging" type="text">
					<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo count($pages['pages']) ?></span></span>
				</span>
				<?php
					echo JLayoutHelper::render('pagination.link', $pages['next']);
					echo JLayoutHelper::render('pagination.link', $pages['end']); ?>
		<?php } ?>

		<?php if ($showLimitStart) { ?>
			<input type="hidden" name="<?php echo $list['prefix']; ?>limitstart" value="<?php echo $list['limitstart']; ?>" />
		<?php } ?>

		<?php if ($showLimitBox) { ?>
			<div class="limit pull-right">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM') . $list['limitfield']; ?>
			</div>
		<?php } ?>
	</span>
</div>