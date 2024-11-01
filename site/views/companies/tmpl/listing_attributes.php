<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>

<?php
if(!empty(($this->companyAttributes))) {
    $attributes = JBusinessUtil::arrangeAttributesByGroup($this->companyAttributes);
    $packageFeatured = isset($this->package->features)?$this->package->features:null;
    $ungrouped = array();

    if(count($attributes) > 1) { ?>
        <div class='attribute-groups-container'>
            <?php foreach($attributes as $group => $values) { ?>
                <?php if($group != 'ungrouped') {
                    $renderedContent = AttributeService::renderAttributesFront($values,$appSettings->enable_packages, $packageFeatured);
                    if(!empty($renderedContent)) { ?>
                        <div class='attribute-group'>
                        	<div class="listing-detail">
	                        	<?php
	                        	if ($appSettings->enable_multilingual && isset($group)) {
				                  	 	$group = JBusinessDirectoryTranslations::getTranslatedItemName($group);
						            }
	                        	?>
    							<div class="listing-detail-header"><?php echo $group ?></div>
    							<?php echo $renderedContent; ?>
	                        </div>
	                    </div>
                    <?php } ?>
                <?php } else {
                    $ungrouped = $values;
                } ?>
            <?php } ?>
        </div>
        <?php
        $renderedContent = AttributeService::renderAttributesFront($ungrouped,$appSettings->enable_packages, $packageFeatured);
        echo $renderedContent;
    }
    else {?>
        <div class="custom-attributes">
           <?php 
            $renderedContent = AttributeService::renderAttributesFront($attributes['ungrouped'],$appSettings->enable_packages, $packageFeatured);
            echo $renderedContent;
            ?>
        </div>
    <?php }
}
?>