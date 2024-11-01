<?php
/**
 * @package     JBD.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0.en.html; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

$title = htmlspecialchars(JText::_($data->tip ?: $data->title));

JHtml::_('bootstrap.popover');

$orderIcons = array('arrow-up', 'arrow-down');

$index = (int) ($data->direction === 'desc');
$data->orderIcon = $orderIcons[$index];

?>
<a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
   data-order="<?php echo $data->order; ?>" data-direction="<?php echo strtoupper($data->direction); ?>" data-name="<?php echo JText::_($data->title); ?>"
   title="<?php echo $title; ?>" data-content="<?php echo htmlspecialchars(JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN')); ?>" data-placement="top">
<?php if (!empty($data->icon)) :
	?><span class="<?php echo $data->icon; ?>"></span><?php
endif; ?>
<?php if (!empty($data->title)) :
	?><span class="jtable-head-row-data-title"><?php echo JText::_($data->title); ?><?php
endif; ?></span>
<?php if ($data->order == $data->selected) : ?>
	<span class="la la-<?php echo $data->orderIcon; ?>"></span>
<?php else : ?>
	<span class="la la-arrows-v "></span>
<?php endif; ?>
</a>


