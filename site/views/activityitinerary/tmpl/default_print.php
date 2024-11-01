<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

$selected = JFactory::getApplication()->input->get("selected");

$empty = true;
?>

<?php if($selected) {?>
<table>
    <tbody>
    <?php if(!empty($this->activities)) { ?>
        <?php foreach($this->activities as $key=>$val){ ?>
            <?php foreach($val as $item) { $empty = false;?>
                <tr>
                    <td>
                        <b><?php echo $item->name ?></b><br/>
                        <i><?php echo $key ?> <?php echo $item->hours; ?></i><br/>
                        <?php echo $item->address ?><br/>
                        <a href="<?php echo $item->link ?>"><?php echo $item->link; ?></a><hr/>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<?php }
else { ?>
    <table>
        <tbody>
        <?php if(!empty($this->items)) { ?>
            <?php foreach($this->items as $key=>$val){ ?>
                <?php foreach($val as $category=>$items) { ?>
                    <?php foreach($items as $item) { $empty = false; ?>
                        <tr>
                            <td>
                                <b><?php echo $item->name ?></b><br/>
                                <i><?php echo $key ?> <?php echo $item->hours; ?></i><br/>
                                <?php echo $item->address ?><br/>
                                <a href="<?php echo $item->link ?>"><?php echo $item->link; ?></a><hr/>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
<?php if(!$empty) { ?>
<div class="col-md-3">
    <button type="button" class="btn btn-success" onclick="window.print()">
        <span class="ui-button-text"><i class="la la-pencil"></i> <?php echo JText::_("LNG_PRINT")?></span>
    </button>
</div>
<?php }
else { ?>
<p><?php echo JText::_('LNG_NO_ACTIVITIES_PRESENT'); ?></p>
<?php } ?>