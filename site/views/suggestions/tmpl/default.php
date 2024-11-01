<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
$appSettings = JBusinessUtil::getApplicationSettings();

$menuItemId = JBusinessUtil::getActiveMenuItem();

$isProfile = true;
$newTab = false;
$showData = true;

$user = JBusinessUtil::getUser();
if ($user->ID == 0) {
    $app = JFactory::getApplication();
    $return = "index.php?option=com_jbusinessdirectory&view=suggestions";
    $app->redirect(JBusinessUtil::getLoginUrl($return, false));
    
}

?>
<script>
    var isProfile = true;
</script>
<style>
    #header-box, #control-panel-link {
        display: none;
    }

</style>

<?php echo empty($this->items)? JText::_("LNG_NO_SUGGESTIONS"):"" ?>

<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=suggestions')?>" method="post" name="adminForm" id="adminForm">
    <?php
        $type = $this->state->get("type");

        switch ($type) {
            case 1:
                $this->companies = $this->items;
                require_once JPATH_COMPONENT_SITE.'/include/listings_grid_style_2.php';
                break;
            case 2:
                $this->offers = $this->items;
                require_once JPATH_COMPONENT_SITE.'/views/offers/tmpl/offers_grid_style_2.php';
                break;
            case 3:
                $this->events = $this->items;
                require_once JPATH_COMPONENT_SITE.'/views/events/tmpl/events_grid_view_style_1.php';
                break;
            case 4:
                $this->conferences = $this->items;
                require_once JPATH_COMPONENT_SITE.'/views/conferences/tmpl/grid_view.php';
                break;

        }
    ?>

    <?php if ($this->pagination->get('pages.total') > 1) { ?>
        <div class="pagination">
            <?php echo $this->pagination->getListFooter(); ?>
            <div class="clear"></div>
        </div>
    <?php } ?>

    <input type='hidden' name='option' value='com_jbusinessdirectory'/>
    <input type='hidden' name='controller' value='suggestions' />
    <input type='hidden' name='view' value='suggestions' />
    <input type='hidden' name='type' value='<?php echo $type ?>' />
</form>
