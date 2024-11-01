<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

;
JHtml::_('behavior.multiselect');
JBusinessUtil::initializeChosen();

use MVC\Factory;
use MVC\HTML\HTMLHelper;
use MVC\Language\Multilanguage;
use MVC\Language\Text;
use MVC\Layout\LayoutHelper;
use MVC\Router\Route;
use MVC\Session\Session;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= true;
$saveOrder	= $listOrder == 'bc.ordering';

$saveOrderingUrl = JBusinessUtil::addSorting($saveOrder, $listDirn);
?>

<script type="text/javascript">
window.addEventListener('load', function() {
	JBD.submitbutton = function(task) {
		if (task != 'companies.delete' || confirm('<?php echo JText::_('COM_JBUSINESS_DIRECTORY_COMPANIES_CONFIRM_DELETE', true);?>')) {
			JBD.submitform(task);
		}
	}
});
</script>

<div id="jbd-container" class="jbd-container">
    <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=companies');?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
		    <?php
		        // Search tools bar
		        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' =>JBusinessUtil::setFilterVisibility($this->state))));
		    ?>
        </div>
        <div class="clr clearfix"></div>

	    <?php if (empty($this->items)) { ?>
            <div class="alert alert-warning">
			    <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
	    <?php } else { ?>


        <table class="jtable" id="itemList">
            <thead class="jtable-head">
                <tr class="jtable-head-row">
                	<td class="jtable-head-row-data"></td>
                    <th class="jtable-head-row-data">
                        <?php echo JHtml::_('searchtools.sort', '', 'bc.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th class="jtable-head-row-data">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="jradio">
                                <input id="jradio-2" type="checkbox" title="<?php echo JText::_('JGLOBAL_CHECK_ALL');?>" onclick="JBD.checkAll(this)" />
                                <label for="jradio-2"></label>
                            </div>
                        </div>
                    </th>
                    <th class="jtable-head-row-data">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="jtable-head-row-data-title"> # </span>
                        </div>
                    </th>
                    <th class="jtable-head-row-data"></th>
                    <th class="jtable-head-row-data"></th>
                    <th class="jtable-head-row-data">
                        <?php echo HTMLHelper::_('searchtools.sort', 'LNG_NAME', 'bc.name', $listDirn, $listOrder); ?>
                    </th>
                    <th  class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_CATEGORY', 'category_name', $listDirn, $listOrder); ?></th>
                    <th  class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_LAST_MODIFIED', 'bc.modified', $listDirn, $listOrder); ?></th>
                    
                    <?php if ($this->appSettings->enable_packages) { ?>
                        <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_PACKAGE', 'bc.package_id', $listDirn, $listOrder); ?></th>
                        <!--th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_SUBSCRIPTION', 'sb.status', $listDirn, $listOrder); ?></th-->
                    <?php } ?>

                    <?php if($this->state->get('list.show_advanced_list')){?>
                        <th class="jtable-head-row-data"><?php echo JText::_('LNG_STATISTICS') ?></th>
                    <?php } ?>
                    
                    <?php if($this->state->get('list.show_advanced_list')){?>
                    	<th class="jtable-head-row-data" width="10%" align="center"><?php echo JText::_('LNG_ITEMS') ?></th>
                    <?php } ?>
                    <th class="jtable-head-row-data text-center"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_STATE', 'bc.state', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data text-center"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_APROVED', 'bc.approved', $listDirn, $listOrder); ?></th>
                    <th class="jtable-head-row-data"></th>
                    <th class="jtable-head-row-data"><?php echo HTMLHelper::_('searchtools.sort', 'LNG_ID', 'bc.id', $listDirn, $listOrder); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="15">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody <?php if ($saveOrder) :?> class="jtable-body js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
                <?php $nrcrt = 1; $i=0;
                foreach ($this->items as $key => $company) { 
                    $ordering  = ($listOrder == 'bc.ordering');
                    $canCreate  = true;
                    $canEdit    = true;
                    $canChange  = true;
                    ?>
                    <tr class="jtable-body-row" data-draggable-group="3">
                        <td class="jtable-body-row-data">
                            <div class="d-flex align-items-center">
                            	<div id="item-status-<?php echo $company->id?>" class="jtable-body-status <?php echo $company->state == 1 && $company->approved==COMPANY_STATUS_APPROVED?"bg-success":"bg-danger" ?> "></div>
                            </div>
                        </td>
                        
                        <td class="order jtable-body-row-data">
                            <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                            } ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
                                <i class="la la-ellipsis-v"></i>
                            </span>
                            <?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $company->ordering!=0?$company->ordering:$nrcrt; ?>" />
                            <?php endif; ?>
                        </td>

                        <td class="jtable-body-row-data px-3">
                            <?php echo HTMLHelper::_('jbdgrid.id', $i, $company->id); ?>
                        </td>
                        
                        <td class="jtable-body-row-data"><?php echo $nrcrt++?></td>
                        
                        <td class="jtable-body-row-data px-3">
                        	<?php echo HTMLHelper::_('jbdgrid.action', $i , "changeFeaturedState", 'company.',"",$company->featured==1?"Featured":"Not featured","",true, $company->featured==1?"la la-star":"la la-star-o","", true); ?>
                        </td>

                        <td class="jtable-body-row-data px-3">
                        	<?php echo HTMLHelper::_('jbdgrid.action', $i , "changeRecommendedState", 'company.',"",$company->recommended==1?"Recommended":"Not recommended","",true, $company->recommended==1?"la la-check text-bold jbd-green":"la la-check gray","", true); ?>
                        </td>
                       
                        <td class="jtable-body-row-data jtable-body-name">
                        	<div class="d-flex align-items-center">
                                <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=company.edit&id='.$company->id)?>">
                                    <?php if (!empty($company->logoLocation)) { ?>
                                        <img src="<?php echo BD_PICTURES_PATH.$company->logoLocation ?>" class="jtable-data-img"/>
                                        </a>
                                    <?php } else { ?>
                                        <img src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>" class="jtable-data-img" />
                                    <?php } ?>
                                </a>
                                
                   				<span class="ml-3 d-flex flex-column justify-content-center">
									<span class="jtable-body-row-data-title">
                                        <a href="<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.edit&id='. $company->id )?>"
                                            title="<?php echo JText::_('LNG_CLICK_TO_EDIT'); ?>">
                                            <b><?php echo strip_tags($company->name)?></b>
                                        </a><br/>
                                    </span>
                                    <span class="jtable-body-row-data-allias"><?php echo $company->alias?></span>
                                </span>
                            </div>
                        </td>
                        <td class="jtable-body-row-data">
                        	<?php echo $company->category_name ?>
                        </td>
                        <td class="jtable-body-row-data">
                        	<?php if(!empty($company->display_name)){?>
                         		<?php echo $company->display_name ?> <br/>
                         	<?php }?>
                            <?php echo JBusinessUtil::getDateGeneralFormatWithTime($company->modified) ?>
                        </td>

                        <?php if($this->appSettings->enable_packages){?>
                            <td class="jtable-body-row-data">
                                <?php if(!empty($company->packgeInfo)){?>
                                    <?php foreach( $company->packgeInfo as $j=>$package){?>
                                        <div class="package-status">
                                            <strong><?php echo $package->name ?></strong><br/>
                                            <?php echo $package->active==1?JText::_("LNG_ACTIVE"):"" ?>
                                            <?php if($package->active==0){
                                                     if(!$package->future){
                                                            echo JText::_("LNG_EXPIRED");
                                                        }else {
                                                            echo JText::_("LNG_NOT_STARTED");
                                                            $showExtend = false;
                                                        }
                                                    }
                                            ?>

                                            <?php echo $package->price==0?" - ".JText::_("LNG_FREE"):"" ?>
                                            <?php echo $package->price!=0 && $package->state==1?" - ".JText::_("LNG_PAID"):"" ?>
                                            <?php echo $package->price!=0 && $package->state==='0'?" - ".JText::_("LNG_NOT_PAID"):"" ?>

                                            <?php if($package->expiration_type==2 && $package->price!=0){ ?>
                                                <br/><?php echo JText::_("LNG_EXPIRATION_DATE").": ". $package->expirationDate ?>
                                                <?php echo $j<(count($company->packgeInfo)-1)?"<br/>":""?>
                                            <?php }?>
                                        </div>
                                    <?php }?>
                                <?php }?>
                            </td>

                        <?php } ?>

                        <?php if($this->state->get('list.show_advanced_list')){?>
                            <td class="jtable-body-row-data">
                                <div class="listing-statistics">
                                    <?php echo JText::_("LNG_WEBSITE_CLICKS") ?>: <?php echo intval($company->websiteCounts) ?><br/>
                                    <?php echo JText::_("LNG_VIEW_NUMBER") ?>: <?php echo intval($company->viewCount) ?><br/>
                                    <?php echo JText::_("LNG_CONTACT_NUMBER") ?>: <?php echo intval($company->contactCount) ?><br/>
                                </div>
                            </td>
                        <?php } ?>
                        
 						<?php if($this->state->get('list.show_advanced_list')){?>
                            <td class="jtable-body-row-data">
                            	<?php echo JText::_("LNG_EVENT_NUMBER") ?>:
                                <a href='<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=events&listing_id=".$company->id); ?>'
                                    title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="btn-sm btn-primar btn-panel">
                                    <?php echo $company->eventCount ?>
                                </a><br/>
                                <?php echo JText::_("LNG_OFFER_NUMBER") ?>:
                                <a href='<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=offers&listing_id=".$company->id); ?>'
                                    title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="btn-sm btn-primar btn-panel">
                                    <?php echo $company->offerCount ?>
                                </a><br/>
                                <?php echo JText::_("LNG_REVIEW_NUMBER") ?>:
                                <a href='<?php echo JRoute::_("index.php?option=com_jbusinessdirectory&view=reviews&listing_id=".$company->id); ?>'
                                    title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" class="btn-sm btn-primar btn-panel">
                                    <?php echo $company->reviewCount ?>
                                </a>
                            </td>
                        <?php } ?>
                        <td class="jtable-body-row-data">
                        	<?php echo HTMLHelper::_('jbdgrid.published', $company->state, $i, 'companies.', true, 'cb', true, true, $company->id); ?>
                        </td>

					<td class="jtable-body-row-data">
                            <?php
                                $text="";
                                $action="";
                                switch($company->approved) {
                                    case COMPANY_STATUS_CLAIMED:
                                        $text = JTEXT::_("LNG_CLAIM_APPROVAL");
                                        $action = "aproveClaim";
                                        break;
                                    case COMPANY_STATUS_CREATED:
                                        $text = JTEXT::_("LNG_CREATION_APPROVAL");
                                        $action = "aprove";
                                        break;
                                    case COMPANY_STATUS_DISAPPROVED:
                                        $text = JTEXT::_("LNG_DISAPPROVED");
                                        $action = "aprove";
                                        break;
                                    case COMPANY_STATUS_APPROVED:
                                        $text = JTEXT::_("LNG_APPROVED");
                                        $action = "disaprove";
                                        break;
                                } 
                            ?>
                            <?php if($company->approved == COMPANY_STATUS_CREATED){?>
                                <span><?php echo $text ?></span>
                                <div class="d-flex align-items-center">
                                    <div class="jmaterial-btn-icon-sm approve" onclick="document.location.href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.aprove&id='. $company->id )?>'">
                                        <i class="la la-thumbs-up"></i>
                                    </div>
                                    &nbsp;
                                    <div class="jmaterial-btn-icon-sm dissaprove bg-danger" onclick="showDisapprovalModal(<?php echo $company->id ?>)">
                                        <i class="la la-thumbs-down"></i>
                                    </div>
                            	</div>
                           	<?php }else if($company->approved == COMPANY_STATUS_CLAIMED){?>
                           		 <span><?php echo $text ?></span>
                           		 <div class="d-flex align-items-center">
                                    <div class="jmaterial-btn-icon-sm transparent2 approve" onclick="document.location.href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.aproveClaim&id='. $company->id )?>'">
                                        <i class="la la-thumbs-up"></i>
                                    </div>
                                    &nbsp;
                                    <div class="jmaterial-btn-icon-sm dissaprove bg-danger" onclick="document.location.href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.disaproveClaim&id='. $company->id )?>'">
                                        <i class="la la-thumbs-down"></i>
                                    </div>

                                    <div title="<?php echo JText::_('LNG_RESET_CLAIM_STATUS'); ?>" class="jmaterial-btn-icon-sm dissaprove bg-info" onclick="document.location.href='<?php echo JRoute::_( 'index.php?option=com_jbusinessdirectory&task=company.resetClaim&id='. $company->id )?>'">
                                        <i class="la la-undo"></i>
                                    </div>

                            	</div>
        					<?php }else{?>
                                    <div class="d-flex">
                                        <?php echo HTMLHelper::_('jbdgrid.approve',"changeApprovalState", $company->approved, $i, 'company.', true, 'cb', true, true, $company->id); ?>
                                    </div>
        					<?php } ?>
					</td>
					<td class="jtable-body-row-data">
						<div class="row-fluid jbd-buttons-row">
							<div class="d-flex align-items-center justify-content-center">
								<a title="<?php echo JText::_('LNG_CLICK_TO_VIEW'); ?>" href="<?php echo JBusinessUtil::getCompanyLink($company) ?>" class="jtable-btn" target="_blank"> 
									<i class="la la-eye"></i>
								</a>
							</div>
							
						</div>
					</td>
					<td class="jtable-body-row-data">
                            <span><?php echo (int) $company->id; ?></span>
                        </td>
                    </tr>
                <?php
                    $i++;
                } ?>
            </tbody>
        </table>
        <?php } ?>

        <input type="hidden" name="option"	value="<?php echo JBusinessUtil::getComponentName()?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="companyId" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>

        <?php // Load the batch processing form. ?>
        <?php echo $this->loadTemplate('batch'); ?>
    </form>
                        
    <div id="disapproval-reason-dialog" style="display:none" class="jbd-container">
        <form action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=company.disaprove');?>" method="post" name="disapproveForm" id="disapproveForm" enctype="multipart/form-data">
            <div class="jmodal-sm">
                <div class="jmodal-header">
                    <h2> <?php echo JText::_('LNG_DISAPPROVAL_REASON'); ?></h2>
                    <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
                </div>
                <div class="jmodal-body">
                    <p> <?php echo JText::_('LNG_DISAPPROVAL_REASON_TEXT'); ?>.</p>
                    <textarea class="form-control text-input" style="margin-bottom:25px; width:90%;"name="disapproval_text" id="disapproval_text" rows="4" maxLength="255"></textarea>
                    <div class="clearfix clear-left">
                        <div class="button-row">
                            <button type="button" class="btn" onclick="submitForm()">
                                <span class="ui-button-text"><?php echo JText::_("LNG_CONFIRM") ?></span>
                            </button>
                            <button type="button" class="btn btn-dark" onclick="jQuery.jbdModal.close()">
                                <span class="ui-button-text"><?php echo JText::_("LNG_CANCEL") ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="company_id"  id="company_id" value="" />
        </form>
    </div>
</div>

<?php echo $this->loadTemplate('export'); ?>
<?php echo $this->loadTemplate('import'); ?>

<script>
    function showDisapprovalModal(companyId) {
        jQuery('#company_id').val(companyId);
		jQuery('#disapproval-reason-dialog').jbdModal();
    }

    function submitForm() {
        jQuery("#disapproveForm").submit()
    }
</script>