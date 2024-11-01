<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function(task)
    {
        JBD.submitform(task, document.getElementById('item-form'));
    }
});
</script>

<div id="jbd-container" class="jbd-container jbd-edit-container">
	<form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=region');?>" method="post" name="adminForm" id="item-form">
        <div class="row">
            <div class="col-12 order-1 col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="boxed">

                            <h2> <?php echo JText::_('LNG_REGION');?></h2>
                            <div class="form-container  label-w-100">
                                <div class="form-group">
                                    <label for="region_id"><?php echo JText::_('LNG_ID')?> </label>
                                    <input type="text"
                                           name="region_id" id="region_id" class="input_txt form-control" value="<?php echo $this->item->id ?>" maxlength="4" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="name"><?php echo JText::_('LNG_NAME')?> </label>
                                    <input type="text"
                                           name="name" id="name" class="input_txt form-control" value="<?php echo $this->item->name ?>" maxlength="65" size="32">
                                </div>

                                <div class="form-group">
                                    <label for="country_id"><?php echo JText::_('LNG_COUNTRIES')?> </label>
                                    <select name="country_id" id="country_id" class="input_txt form-control select">
                                        <?php
                                        foreach ($this->countries as $country) {
                                            $selected = '';
                                            if ($country->id == $this->item->country_id) {
                                                $selected = "selected";
                                            } ?>
                                            <option <?php echo $selected ?> value="<?php echo $country->id ?>">
                                                <?php echo $country->country_name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
		<input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
