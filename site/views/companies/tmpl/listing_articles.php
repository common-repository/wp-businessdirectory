<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
JBusinessUtil::enqueueStyle('libraries/owl/owl.carousel.min.css');
JBusinessUtil::enqueueStyle('libraries/owl/owl.theme.min.css');
JBusinessUtil::enqueueScript('libraries/owl/owl.carousel.min.js');

$plugin = JPluginHelper::getPlugin('content', 'business');
$categoryParam="";
// Check if plugin is enabled
if ($plugin)
{
    // Get plugin params
    $pluginParams = new JRegistry($plugin->params);
    
    $category_id = $pluginParams->get('category_id');
    if(!empty($category_id)){
        $categoryParam ="&id=$category_id";
    }
}

?>

<div class="row">
    <div class="col-md">
        <div id="listing-articles" class="listing-articles">
        	<?php $index = 0;?>
            <?php foreach ($this->companyArticles as $article){?>
				<?php $index ++;?>
                <div class="item article">
                	<a target="_blank" onclick="jbdUtils.jbdUtils.registerStatAction(<?php echo $article->id ?>,<?php echo STATISTIC_ITEM_ARTICLE ?>,<?php echo STATISTIC_TYPE_ARTICLE_CLICK ?>)" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=' . $article->id.'&catid=' . $article->catid); ?>"><?php echo $article->title?></a>
                </div>
                <?php if($index>=4){?>
                	<a class="right" target="_blank" href="<?php echo JRoute::_('index.php?option=com_content&view=category&business_id='.$this->company->id.$categoryParam); ?>"><?php echo JText::_("LNG_MORE")?></a>
                	<?php break;?>
                <?php }?>
            <?php } ?>
        </div>
    </div>
</div>
