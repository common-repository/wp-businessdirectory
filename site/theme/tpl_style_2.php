<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$user = JBusinessUtil::getUser();
$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
?>
<style>
.subhead-collapse{
	margin: 0 !important;
	padding: 0 !important;	
}

</style>

<div id="jdb-wrapper" class="jbd-container jdb-wrapper-front tmpl-style-2">
	
	<nav class="jbd-user-nav">
		<ul class="nav">
			<?php foreach ($template->menus as $menu) {?>
				<li class="<?php echo isset($menu["active"])?"active":""?>">
					<a href="<?php echo JRoute::_($menu["link"])?>">
						<i class="<?php echo $menu["icon"] ?>"></i>	<span class="nav-label"><?php echo $menu["title"] ?></span>
                        <?php if (isset($menu['display-unread-message'])) { ?>
                            <span class="nav-label" id="message-unreaded">&nbsp;(<?php echo $menu['nrMessages'] ?>) </span>
                        <?php } ?>
                        <?php if (isset($menu['display-unread-quote']) && $menu['nrQuotes'] != 0) { ?>
                            <span class="nav-label"> (<?php echo $menu['nrQuotes'] ?>) </span>
                        <?php } ?>
                        <?php if (isset($menu["new"])) {?>
                            <span class="label label-info pull-right"><?php echo JText::_("LNG_NEW")?></span>
                        <?php } ?>
                        <?php if (isset($menu["submenu"])) {?>
                            <span class="la la-menu-arrow"></span>
                        <?php } ?>
					</a>
				</li>
			<?php } ?>
		</ul>
	</nav>
	
	<div id="page-wrapper">
		<div id="content-wrapper">
			<?php echo $template->content?>
			<div class="clear"></div>
		</div>
	</div>
</div>
