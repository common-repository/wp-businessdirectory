<?php
/**
 * @package    WPBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('formbehavior.chosen');

$isProfile = true;
$user = JBusinessUtil::getUser();

$options = array(
        'onActive' => 'function(title, description) {
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
        'onBackground' => 'function(title, description) {
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
        'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
        'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
    JBD.submitbutton = function (task) {

        var defaultLang = "<?php echo JBusinessUtil::getLanguageTag() ?>";

        jQuery("#item-form").validationEngine('detach');
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("click", true, true);
        var tab = ("tab-" + defaultLang);
        if (!(document.getElementsByClassName(tab)[0] === undefined || document.getElementsByClassName(tab)[0] === null))
            document.getElementsByClassName(tab)[0].dispatchEvent(evt);
        if (task == 'managecompanyarticle.cancel' || !jbdUtils.validateCmpForm(false, false)) {
            JBD.submitform(task, document.getElementById('item-form'));
        }
        jQuery("#item-form").validationEngine('attach');
    }
});
</script>



<div id="jbd-container" class="jbd-container jbd-edit-container">
	<?php
	if(isset($isProfile)) { ?>
	    <div class="button-row">
	        <button id="save-btn" type="button" class="btn btn-success button-save" onclick="saveArticle('apply')">
	            <i class="la la-edit"></i> <?php echo JText::_("LNG_SAVE")?>
	        </button>
	        <button type="button" class="btn btn-success button-close" onclick="saveArticle('save');">
	            <span class="ui-button-text"><i class="la la-check"></i> <?php echo JText::_("LNG_SAVE_AND_CLOSE")?></span>
	        </button>
	        <button type="button" class="btn btn-dark button-cancel" onclick="cancel()">
	            <span class="ui-button-text"><i class="la la la-close"></i> <?php echo JText::_("LNG_CANCEL")?></span>
	        </button>
	    </div>
	    <div class="clear"></div>
	    <?php
	} ?>

    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
            		<div class="col-12">
    			 		<fieldset class="boxed">
    			 			<div class="row align-items-center">
                        		<div class="col-md">
                        			<strong><?php echo JText::_('LNG_SELECT_A_BUSINESS')?> <?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY) ?></strong>
                        		</div>
                        		<div class="col-md">
                                    <select data-placeholder="<?php echo JText::_("LNG_SELECT_OR_SEARCH_COMPANY") ?>" class="form-control input-medium ajax-chosen-select <?php echo !$this->appSettings->item_decouple?"validate[required]":""?>" name="company_id" id="company_id">
                                        <option value=""><?php echo JText::_("LNG_SELECT_OR_SEARCH_COMPANY")?></option>
                                        <?php echo JHtml::_('select.options', $this->companyOptions, 'id', 'name', $this->item->company_id); ?>
                                    </select>
                        		</div>
                        	</div>
                        </fieldset>
                    </div>
            	</div>
                 <div class="row">
                	<div class="col-12">
                        <fieldset class="boxed" >
                        	<div class="row">
                        		<div class="col-md">
                        			<h3> <?php echo JText::_('LNG_ARTICLE');?></h3>
                            		<p><?php echo JText::_('LNG_ARTICLE_INFO_TXT');?></p>		
                        		</div>
                        	</div>
                            <div class="form-container" id="tax-form-box">
                                <div class="form-group">
                                    <label for="post_title"><?php echo JText::_('LNG_TITLE') ?><?php echo JBusinessUtil::showMandatory(ATTRIBUTE_MANDATORY) ?></label>
                                    <input type="text" name="post_title" id="post_title" class="input_txt validate[required]" value="<?php echo $this->item->post_title ?>"  maxLength="100">
                                </div>

                                <div class="form-group">
                                    <label for="description"><?php echo JText::_('LNG_CONTENT') ?></label>
									<div id="content">
										<?php echo JBusinessUtil::getEditor()->display('post_content', $this->item->post_content, '95%', '550', '200', '10', false); ?>
									</div>
                                </div>
                            </div>
                   		 </fieldset>
					</div>
            	</div>
                <div class="jbd-admin-column">
                    <?php if(isset($isProfile)) { ?>
                        <div class="button-row">
                            <button id="save-btn" type="button" class="btn btn-success button-save" onclick="saveArticle('apply')">
                                <i class="la la-edit"></i> <?php echo JText::_("LNG_SAVE")?>
                            </button>
                            <button type="button" class="btn btn-success button-close" onclick="saveArticle('save');">
                                <span class="ui-button-text"><i class="la la-check"></i> <?php echo JText::_("LNG_SAVE_AND_CLOSE")?></span>
                            </button>
                            <button type="button" class="btn btn-dark button-cancel" onclick="cancel()">
                                <span class="ui-button-text"><i class="la la la-close"></i> <?php echo JText::_("LNG_CANCEL")?></span>
                            </button>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="id" id="id" value="<?php echo $this->item->ID ?>" />
        <input type="hidden" name="view" id="view" value="managecompanyarticle" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div>

<script>

    window.addEventListener('load', function() {

    });

    function saveArticle(task) {

		if(jbdUtils.validateCmpForm(true, true)){
            return false;
		}

        jQuery("#task").val('managecompanyarticle.'+task);
        var form = document.adminForm;
        form.submit();
    }

    function cancel() {
        jQuery("#task").val('managecompanyarticle.cancel');
        var form = document.adminForm;
        form.submit();
    }
</script>