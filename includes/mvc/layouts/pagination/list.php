<?php
/**
 * @package     JBD.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$list = $displayData['list'];
?>
<div class="tablenav-pages"><span class="displaying-num">72 items</span>
<span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" name="paged" value="1" size="1" aria-describedby="table-paging" type="text"><span class="tablenav-paging-text"> of <span class="total-pages">4</span></span></span>
<a class="next-page" href="http://test.cmsjunkie.com/travel/wp-admin/edit.php?post_type=shop_order&amp;paged=2"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
<a class="last-page" href="http://test.cmsjunkie.com/travel/wp-admin/edit.php?post_type=shop_order&amp;paged=4"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a></span></div>


<div class="tablenav-pages"><span class="displaying-num">72 items</span>
<span class="pagination-links"><a class="first-page" href="http://test.cmsjunkie.com/travel/wp-admin/edit.php?post_type=shop_order"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
<a class="prev-page" href="http://test.cmsjunkie.com/travel/wp-admin/edit.php?post_type=shop_order&amp;paged=2"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>

<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" name="paged" value="3" size="1" aria-describedby="table-paging" type="text"><span class="tablenav-paging-text"> of <span class="total-pages">4</span></span></span>
<a class="next-page" href="http://test.cmsjunkie.com/travel/wp-admin/edit.php?post_type=shop_order&amp;paged=4"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
<span class="tablenav-pages-navspan" aria-hidden="true">»</span></span></div>

<div class="tablenav-pages">
	<span class="displaying-num">72 items</span>
	<span class="pagination-links">
		<span class="tablenav-pages-navspan" aria-hidden="true">«</span>
		<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
		
		<span class="paging-input">
			<label for="current-page-selector" class="screen-reader-text">Current Page</label>
			<input class="current-page" id="current-page-selector" name="paged" value="1" size="1" aria-describedby="table-paging" type="text">
			<span class="tablenav-paging-text"> of <span class="total-pages">4</span></span>
		</span>
		
		<a class="next-page" href=""><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
		<a class="last-page" href=""><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span>
		
		<ul>
			<li class="pagination-start"><?php echo $list['start']['data']; ?></li>
			<li class="pagination-prev"><?php echo $list['previous']['data']; ?></li>
			<?php foreach ($list['pages'] as $page) : ?>
				<?php echo '<li>' . $page['data'] . '</li>'; ?>
			<?php endforeach; ?>
			<li class="pagination-next"><?php echo $list['next']['data']; ?></li>
			<li class="pagination-end"><?php echo $list['end']['data']; ?></li>
		</ul>
	</span>
</div>